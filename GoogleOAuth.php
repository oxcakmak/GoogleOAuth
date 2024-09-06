<?php
class GoogleOAuth {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    private $authEndpoint = 'https://accounts.google.com/o/oauth2/auth';
    private $userinfoEndpoint = 'https://www.googleapis.com/oauth2/v1/userinfo';
    private $scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';

    public function __construct($clientId, $clientSecret, $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Generates the Google OAuth URL
     */
    public function getAuthUrl($state = '') {
        $url = $this->authEndpoint . '?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return $url;
    }

    /**
     * Exchanges an authorization code for an access token
     */
    public function getAccessToken($code) {
        $postData = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $response = $this->makePostRequest($this->tokenEndpoint, $postData);

        if (isset($response['access_token'])) {
            return $response;
        }

        throw new Exception('Error fetching access token: ' . json_encode($response));
    }

    /**
     * Retrieves user information using an access token
     */
    public function getUserInfo($accessToken) {
        $url = $this->userinfoEndpoint . '?access_token=' . urlencode($accessToken);
        $response = $this->makeGetRequest($url);

        if (isset($response['id'])) {
            return $response;
        }

        throw new Exception('Error fetching user information: ' . json_encode($response));
    }

    /**
     * Helper function to make a POST request
     */
    private function makePostRequest($url, $postData) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    /**
     * Helper function to make a GET request
     */
    private function makeGetRequest($url) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
?>
