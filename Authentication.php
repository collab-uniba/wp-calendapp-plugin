<?php
require_once 'google-api-php-client/src/Google/Client.php';
require_once 'google-api-php-client/src/Google/Service/Calendar.php';
require_once 'google-api-php-client/autoload.php';
require_once 'Load_Write.php';
session_start();

    if (isset($_GET['code'])) {
    	$client=set_settings_client();
        $credentials = $client->authenticate($_GET['code']);
        $_SESSION['token'] = $client->getAccessToken();
 		Write_token($credentials);
		Write_client($client);
}

function set_settings_client() {
		$dati_accesso = Load_google_access_data(0);
    	$client_id = $dati_accesso[0];
    	$client_secret = $dati_accesso[1];
    	$uri='';

    	// Crea un HTTP client autenticato
    	$client = new Google_Client();
    	$client->setApplicationName("CalendApp");
    	$client->setClassConfig("Google_Http_Request", "disable_gzip", true);
    	$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		//$client->setRedirectUri('http://localhost/wordpress/wp-content/plugins/wp-calendapp-plugin-master/Authentication.php');
		$client->setRedirectUri(Load_path());
		$client->setScopes('https://www.googleapis.com/auth/calendar');
		$client->setApprovalPrompt('force');
    	$client->setAccessType('offline');   
		return $client;
}
?>

