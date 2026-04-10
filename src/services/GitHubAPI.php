<?php
/**
 * GitHubAPI - GitHub OAuth and data integration for IdeaSync
 */

class GitHubAPI {
    private $client_id = 'YOUR_GITHUB_CLIENT_ID';
    private $client_secret = 'YOUR_GITHUB_CLIENT_SECRET';
    private $redirect_uri = 'http://localhost:8000/src/controllers/github_auth.php';
    private $auth_url = 'https://github.com/login/oauth/authorize';
    private $token_url = 'https://github.com/login/oauth/access_token';
    private $api_url = 'https://api.github.com';

    public function __construct($client_id = null, $client_secret = null) {
        if ($client_id && $client_secret) {
            $this->client_id = $client_id;
            $this->client_secret = $client_secret;
        }
    }

    /**
     * Get GitHub authorization URL
     */
    public function getAuthorizationUrl($state = null) {
        $state = $state ?? bin2hex(random_bytes(16));
        $_SESSION['github_state'] = $state;

        return "{$this->auth_url}?" . http_build_query([
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'user:email read:user public_repo',
            'state' => $state
        ]);
    }

    /**
     * Exchange code for access token
     */
    public function getAccessToken($code, $state) {
        if (!isset($_SESSION['github_state']) || $_SESSION['github_state'] !== $state) {
            throw new Exception('Invalid GitHub state parameter');
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'redirect_uri' => $this->redirect_uri
            ])
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['error'])) {
            throw new Exception($data['error_description'] ?? 'GitHub auth failed');
        }

        return $data['access_token'] ?? null;
    }

    /**
     * Get authenticated user's GitHub profile
     */
    public function getUserProfile($access_token) {
        $user = $this->apiCall('/user', $access_token);
        $repos = $this->getTopRepositories($access_token, 5);

        return [
            'github_id' => $user['id'] ?? null,
            'github_username' => $user['login'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'bio' => $user['bio'] ?? null,
            'avatar_url' => $user['avatar_url'] ?? null,
            'profile_url' => $user['html_url'] ?? null,
            'public_repos' => $user['public_repos'] ?? 0,
            'followers' => $user['followers'] ?? 0,
            'following' => $user['following'] ?? 0,
            'primary_language' => $this->getPrimaryLanguage($repos),
            'repositories' => $repos
        ];
    }

    /**
     * Get user's top repositories
     */
    public function getTopRepositories($access_token, $limit = 5) {
        $repos = $this->apiCall('/user/repos?sort=stars&per_page=' . $limit, $access_token);

        return array_map(function($repo) {
            return [
                'name' => $repo['name'],
                'url' => $repo['html_url'],
                'description' => $repo['description'],
                'stars' => $repo['stargazers_count'],
                'language' => $repo['language'],
                'updated_at' => $repo['updated_at']
            ];
        }, $repos);
    }

    /**
     * Extract skills from repositories
     */
    public function extractSkills($repositories) {
        $languages = [];

        foreach ($repositories as $repo) {
            if ($repo['language']) {
                $languages[$repo['language']] = ($languages[$repo['language']] ?? 0) + 1;
            }
        }

        // Sort by frequency and return top 10
        arsort($languages);
        return array_keys(array_slice($languages, 0, 10));
    }

    /**
     * Get primary programming language
     */
    private function getPrimaryLanguage($repositories) {
        $languages = [];

        foreach ($repositories as $repo) {
            if ($repo['language']) {
                $languages[$repo['language']] = ($languages[$repo['language']] ?? 0) + 1;
            }
        }

        arsort($languages);
        return array_key_first($languages) ?? 'Unknown';
    }

    /**
     * Make GitHub API call
     */
    private function apiCall($endpoint, $access_token) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->api_url . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/vnd.github.v3+json',
                'User-Agent: IdeaSync'
            ],
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code >= 400) {
            throw new Exception("GitHub API error: HTTP {$http_code}");
        }

        return json_decode($response, true);
    }

    /**
     * Verify user's skills by checking repositories
     */
    public function verifySkills($username, $required_skills = []) {
        try {
            $repos = $this->apiCall("/users/{$username}/repos?per_page=100", null);
            $found_skills = [];

            foreach ($repos as $repo) {
                if ($repo['language'] && in_array($repo['language'], $required_skills)) {
                    $found_skills[] = $repo['language'];
                }
            }

            return [
                'verified' => !empty($found_skills),
                'skills' => array_unique($found_skills),
                'confidence' => count($found_skills) / count($required_skills)
            ];
        } catch (Exception $e) {
            return ['verified' => false, 'skills' => [], 'error' => $e->getMessage()];
        }
    }
}
?>
