<?php
class GitHubAPI {
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct() {
        $this->client_id = Env::get('GITHUB_CLIENT_ID');
        $this->client_secret = Env::get('GITHUB_CLIENT_SECRET');
        $this->redirect_uri = BASE_URL . '/src/controllers/github_auth.php';
    }

    public function getAuthUrl() {
        return "https://github.com/login/oauth/authorize?client_id=" . $this->client_id . "&redirect_uri=" . $this->redirect_uri . "&scope=user,repo";
    }
}
?>
