# GoogleOAuth
PHP Google OAuth 2.0 authentication without using Composer and compatible with PHP versions 5, 6, 7, and 8.

## Support Me

This software is developed during my free time and I will be glad if somebody will support me.

Everyone's time should be valuable, so please consider donating.

[https://buymeacoffee.com/oxcakmak](https://buymeacoffee.com/oxcakmak)

### Installation
No installation is required. You can directly include the `GoogleOAuth.php` file in your project:
```php
require_once 'path/to/GoogleOAuth.php';
```

## Usage
Replace these values with your actual client ID and secret from the Google Developer Console
```php
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$redirectUri = 'http://localhost/google'; // The URL link you want to return to (such as the My Account page)

$googleOAuth = new GoogleOAuth($clientId, $clientSecret, $redirectUri);
```

### Redirect the user to the Google OAuth login page
```php
if (!isset($_GET['code'])) {
    $authUrl = $googleOAuth->getAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
```

### Handle the callback from Google and retrieve the access token
```php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    try {
        $tokenData = $googleOAuth->getAccessToken($code);
        $accessToken = $tokenData['access_token'];

        // Step 3: Use the access token to get user information
        $userInfo = $googleOAuth->getUserInfo($accessToken);

        // Output user info
        echo 'User ID: ' . $userInfo['id'] . '<br>';
        echo 'Name: ' . $userInfo['name'] . '<br>';
        echo 'Email: ' . $userInfo['email'] . '<br>';
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
```

> The above codes are examples, you can edit them as you wish. Since I only worked on one file, I only used isset. You can use it on the login or sign up page.
