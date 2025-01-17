<?php
// Start a session to store the OAuth2 state value
session_start();

// Include Composer's autoload file using the correct relative path
require_once __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;
use Dotenv\Dotenv;

// Load environment variables from the project root (one level up)
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Get client ID and client secret from the environment
$clientId     = $_ENV['CLIENT_ID'] ?? null;
$clientSecret = $_ENV['CLIENT_SECRET'] ?? null;

// Check that credentials are available
if (!$clientId || !$clientSecret) {
    exit("Error: CLIENT_ID or CLIENT_SECRET environment variable is missing.");
}

// Set the redirect URI; it must match one configured in your Google Cloud Console
$redirectUri  = 'http://localhost:8000/helpers/rtg.php';

// Create a new Google OAuth2 provider instance
$provider = new Google([
    'clientId'     => $clientId,
    'clientSecret' => $clientSecret,
    'redirectUri'  => $redirectUri,
]);

// If the authorization code is not set, start the OAuth flow
if (!isset($_GET['code'])) {
    // Build the authorization URL. Request offline access to receive a refresh token.
    $authUrl = $provider->getAuthorizationUrl([
        'access_type' => 'offline',   // Request offline access to get a refresh token
        'prompt'      => 'consent',    // Force the consent screen to ensure a refresh token is returned
        // Request the Gmail scope for sending mail
        'scope'       => ['https://mail.google.com/']
    ]);
    

    // Save the state for CSRF protection
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to Google's OAuth2 consent screen
    header('Location: ' . $authUrl);
    exit;
}
// Check for a valid state to guard against CSRF
elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state parameter. Please try again.');
}
// We have a valid authorization code; exchange it for tokens
else {
    try {
        // Exchange the authorization code for an access token
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Display token details; be sure to save the refresh token securely for future use!
        echo "<h2>OAuth2 Token Details</h2><pre>";
        echo "Access Token: " . htmlspecialchars($token->getToken()) . "\n";
        echo "Refresh Token: " . htmlspecialchars($token->getRefreshToken()) . "\n";
        echo "Expires At: " . date('Y-m-d H:i:s', $token->getExpires()) . "\n\n";
        echo "Full Token Values:\n";
        print_r($token->getValues());
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        exit('Error during token exchange: ' . $e->getMessage());
    }
}
