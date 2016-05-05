<?php
class Database
{
	public static function getConnection()
	{
		$HostName = getenv ( 'OPENSHIFT_MYSQL_DB_HOST' );
		$DatabasePort = getenv ( 'OPENSHIFT_MYSQL_DB_PORT' );
		$DatabaseUsername = getenv ( 'OPENSHIFT_MYSQL_DB_USERNAME' );
		$DatabasePassword = getenv ( 'OPENSHIFT_MYSQL_DB_PASSWORD' );
		$DatabaseName = getenv ( 'OPENSHIFT_GEAR_NAME' );
		if (! $HostName)
		{
			$HostName = "localhost";
			$DatabasePort = "3306";
			$DatabaseUsername = "saleem";
			$DatabasePassword = "third.o5";
			$DatabaseName = "whistleblower";
		}
		
		$mysqli = new mysqli ( $HostName, $DatabaseUsername, $DatabasePassword, $DatabaseName, $DatabasePort );
		if (! $mysqli)
		{
			return null;
		}
		return $mysqli;
	}
}
?>