<?php

function getClient(  ) {
	define('SCOPES', implode(' ', array(
		Google_Service_Sheets::SPREADSHEETS_READONLY,
		Google_Service_Calendar::CALENDAR
	)
	));
	define('APPLICATION_NAME', 'calendarSync');
	


	
	$client = new Google_Client(  );
	$client->setApplicationName( APPLICATION_NAME );
	$client->setScopes( SCOPES );
	$client->setAuthConfig( CLIENT_SECRET_PATH );
	$client->setAccessType( 'offline' );

	// Load previously authorized credentials from a file.
	$credentialsPath = CREDENTIALS_PATH;
	if ( file_exists( $credentialsPath ) ) {
		$accessToken = json_decode( file_get_contents( $credentialsPath ), true );
	} else {
	// Request authorization from the user.
	$authUrl = $client->createAuthUrl(  );
	printf( "** Granbury Account ** \nOpen the following link in your browser:\n%s\n", $authUrl );
	print 'Enter verification code: ';
	$authCode = trim( fgets( STDIN ) );

	// Exchange authorization code for an access token.
	$accessToken = $client->fetchAccessTokenWithAuthCode( $authCode );

	// Store the credentials to disk.
	if( !file_exists( dirname( $credentialsPath ) ) ) {
	  mkdir( dirname( $credentialsPath ), 0700, true );
	}
	file_put_contents( $credentialsPath, json_encode( $accessToken ) );
		printf( "Credentials saved to %s\n", $credentialsPath );
	}
	$client->setAccessToken( $accessToken );

	// Refresh the token if it's expired.
	if ( $client->isAccessTokenExpired(  ) ) {
		$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken(  ) );
		file_put_contents( $credentialsPath, json_encode( $client->getAccessToken(  ) ) );
	}
	return $client;
}

function getClient2(  ) {
	define('SCOPES', implode(' ', array(
		Google_Service_Sheets::SPREADSHEETS_READONLY,
		Google_Service_Calendar::CALENDAR
	)
	));
	define('APPLICATION_NAME', 'calendarSync');
	


	
	$client = new Google_Client(  );
	$client->setApplicationName( APPLICATION_NAME );
	$client->setScopes( SCOPES );
	$client->setAuthConfig( CLIENT_SECRET_PATH );
	$client->setAccessType( 'offline' );

	// Load previously authorized credentials from a file.
	$credentialsPath = CREDENTIALS_PATH2;
	if ( file_exists( $credentialsPath ) ) {
		$accessToken = json_decode( file_get_contents( $credentialsPath ), true );
	} else {
	// Request authorization from the user.
	$authUrl = $client->createAuthUrl(  );
	printf( "** Calendar Account ** \n Open the following link in your browser:\n%s\n", $authUrl );
	print 'Enter verification code: ';
	$authCode = trim( fgets( STDIN ) );

	// Exchange authorization code for an access token.
	$accessToken = $client->fetchAccessTokenWithAuthCode( $authCode );

	// Store the credentials to disk.
	if( !file_exists( dirname( $credentialsPath ) ) ) {
	  mkdir( dirname( $credentialsPath ), 0700, true );
	}
	file_put_contents( $credentialsPath, json_encode( $accessToken ) );
		printf( "Credentials saved to %s\n", $credentialsPath );
	}
	$client->setAccessToken( $accessToken );

	// Refresh the token if it's expired.
	if ( $client->isAccessTokenExpired(  ) ) {
		$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken(  ) );
		file_put_contents( $credentialsPath, json_encode( $client->getAccessToken(  ) ) );
	}
	return $client;
}

/*
function getCalendar( $service ) {
	// Get the Work Schedule Calendar so we can compare events created against events that need to be created
	
	
}




function createCalendarEvent(  $schedule  ) {

/*
	
		

*/

//}
