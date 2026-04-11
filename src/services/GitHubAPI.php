<?php
/**
 * GitHubAPI - GitHub OAuth and data integration for IdeaSync
 * Optimized for production with environment variables
 */

require_once __DIR__ . '/../config/Env.php';

class GitHubAPI {
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $auth_url = 'https://github.com/login/oauth/authorize';
    private $token_url = 'https://github.com/login/oauth/access_token';
    private $api_url = 'https://api.github.com';

    public function __construct() {
        $this->client_id = Env::get('GITHUB_CLIENT_ID');
        $this->client_secret = Env::get('GITHUB_CLIENT_SECRET');
        $this->redirect_uri = Env::get('GITHUB_REDIRECT_URI', Env::get('APP_URL') . '/?page=auth&action=github-callback');
    }

    /**
     * Get GitHub authorization URL
     */
    public function getAuthorizationUrl($state = null) {
        if (!$this->client_id) return null;

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
     * Get authenticated user profile and aggregate languages
     */
    public function getUserData($username) {
        $headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: IdeaSync-Lendi'
        ];

        $token = Env::get('GITHUB_PAT');
        if ($token) {
            $headers[] = 'Authorization: token ' . $token;
        }

        $userRes = $this->makeRequest("/users/{$username}", $headers);
        $reposRes = $this->makeRequest("/users/{$username}/repos?sort=stars&per_page=10", $headers);

        if (!$userRes) throw new Exception('GitHub user not found');

        // Aggregate languages
        $languageCounts = [];
        foreach ($reposRes as $repo) {
            if ($repo['language']) {
                $lang = $repo['language'];
                $languageCounts[$lang] = ($languageCounts[$lang] ?? 0) + ($repo['size'] ?: 1);
            }
        }

        $totalSize = array_sum($languageCounts);
        $languages = [];
        foreach ($languageCounts as $lang => $size) {
            $languages[] = [
                'lang' => $lang,
                'percentage' => $totalSize > 0 ? round(($size / $totalSize) * 100) : 0
            ];
        }

        usort($languages, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        return [
            'username' => $username,
            'publicRepos' => $userRes['public_repos'] ?? 0,
            'followers' => $userRes['followers'] ?? 0,
            'topRepos' => array_map(fn($r) => [
                'name' => $r['name'],
                'stars' => $r['stargazers_count'],
                'language' => $r['language'],
                'description' => $r['description'],
                'url' => $r['html_url']
            ], array_slice($reposRes, 0, 3)),
            'languages' => array_slice($languages, 0, 5),
            'syncedAt' => date('c')
        ];
    }

    private function makeRequest($path, $headers) {
        $ch = curl_init($this->api_url . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
