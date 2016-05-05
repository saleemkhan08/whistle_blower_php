<?php
class GCM
{
	public static function send($registatoin_ids, $message)
	{
		// Google cloud messaging GCM-API url
		$url = 'https://android.googleapis.com/gcm/send';
		$fields = array (
				'registration_ids' => $registatoin_ids,
				'data' => $message 
		);
		// Update your Google Cloud Messaging API Key
		define ( "GOOGLE_API_KEY", "AIzaSyAF1X4WKvN-5gBdpkAHpvC8ynLlyGmdwNU" );
		$headers = array (
				'Authorization: key=' . GOOGLE_API_KEY,
				'Content-Type: application/json' 
		);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $fields ) );
		$result = curl_exec ( $ch );
		if ($result === FALSE)
		{
			die ( 'Curl failed: ' . curl_error ( $ch ) );
		}
		curl_close ( $ch );
		return $result;
	}
}
?>

<?php
class GCM
{
	public static function send($to, $message)
	{
		$fields = array (
				'to' => $to,
				'data' => $message 
		);
		return GCM::sendPushNotification ( $fields );
	}
	
	// Sending message to a topic by topic id
	public static  function sendToTopic($to, $message)
	{
		$fields = array (
				'to' => '/topics/' . $to,
				'data' => $message 
		);
		return GCM::sendPushNotification ( $fields );
	}
	
	// sending push message to multiple users by gcm registration ids
	public static function sendMultiple($registration_ids, $message)
	{
		$fields = array (
				'registration_ids' => $registration_ids,
				'data' => $message 
		);
		return GCM::sendPushNotification ( $fields );
	}
	
	// function makes curl request to gcm servers
	private static function sendPushNotification($fields)
	{
		
		// include config
		include_once __DIR__ . '/../../include/config.php';
		
		// Set POST variables
		$url = 'https://gcm-http.googleapis.com/gcm/send';
		
		define ( "GOOGLE_API_KEY", "AIzaSyAF1X4WKvN-5gBdpkAHpvC8ynLlyGmdwNU" );
		
		$headers = array (
				'Authorization: key=' . GOOGLE_API_KEY,
				'Content-Type: application/json' 
		);
		// Open connection
		$ch = curl_init ();
		
		// Set the url, number of POST vars, POST data
		curl_setopt ( $ch, CURLOPT_URL, $url );
		
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		// Disabling SSL Certificate support temporarly
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $fields ) );
		
		// Execute post
		$result = curl_exec ( $ch );
		if ($result === FALSE)
		{
			die ( 'Curl failed: ' . curl_error ( $ch ) );
		}
		
		// Close connection
		curl_close ( $ch );
		
		return $result;
	}
}
?>