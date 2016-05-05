<?php
class UserAccounts extends Database
{
	public static function addAccount($account)
	{
		$con = parent::getConnection ();
		$result = false;
		$con->query ( 'DELETE FROM UserAccounts WHERE googleId = "' . $account ["googleId"] . '"' );
		
		$stm = $con->prepare ( "INSERT INTO UserAccounts (gcmId,googleId,email,name,photoUrl) VALUES(?,?,?,?,?)" );
		$stm->bind_param ( "sssss", $account ["gcmId"], $account ["googleId"], $account ["email"], $account ["name"], $account ["photoUrl"] );
		
		if ($stm->execute ())
		{
			$result = true;
		}
		$con->close ();
		return $result;
	}
	public static function updateGcmId($account)
	{
		$con = parent::getConnection ();
		$result = false;
		// TODO check if Google id exists and then insert
		$gcmId = $account ["gcmId"];
		$googleId = $account ["googleId"];
		
		if ($con->query ( "UPDATE UserAccounts SET gcmId = '$gcmId' WHERE googleId = '$googleId'" ))
		{
			$result = true;
		}
		$con->close ();
		return $result;
	}
	static function getFriendQuery($email, $str)
	{
		$query = "";
		if (trim ( $str ) != "")
		{
			$query = " AND ( FriendsEmail LIKE '%$str%' OR FriendsName LIKE '%$str%' )";
		}
		return "SELECT FriendsEmail AS email, FriendsName AS name, FriendsPhoto AS photoUrl, 'Friend' AS relation
		FROM UserConnections
		WHERE UserEmail = '$email' $query";
	}
	static function getUserQuery($email, $str, $limit, $offset)
	{
		$query = "";
		if (trim ( $str ) != "")
		{
			$query = " AND	( email LIKE '%$str%' OR name LIKE '%$str%' )";
		}
		return "SELECT email, name, photoUrl, 'Not' AS relation
			FROM UserAccounts
			WHERE email NOT IN (SELECT FriendsEmail AS email
								FROM UserConnections
								WHERE UserEmail = '$email'
								UNION
								SELECT '$email' AS email) 
			$query 
			LIMIT $offset, $limit";
	}
	public static function getUsers($email, $str, $offset, $limit, $category)
	{
		if ($category == "Both")
		{
			$query = self::getFriendQuery ( $email, $str ) . " UNION " . self::getUserQuery ( $email, $str, $limit, $offset );
		}
		elseif ($category == "Friend")
		{
			$query = self::getFriendQuery ( $email, $str );
		}
		elseif ($category == "User")
		{
			$query = self::getUserQuery ( $email, $str, $Limit, $offset );
		}
		$con = parent::getConnection ();
		
		$stm = $con->query ( $query );
		$i = 0;
		$result = array ();
		while ( $row = $stm->fetch_assoc () )
		{
			$result [$i] = $row;
			$i ++;
		}
		
		$con->close ();
		return json_encode ( $result );
	}
	public static function getFriends($email)
	{
		$con = parent::getConnection ();
		$stm = $con->query ( "SELECT FriendsName, FriendsEmail, FriendsPhoto, FROM UserConnections WHERE UserEmail = '$email'" );
		$i = 0;
		$friends = "";
		while ( $row = $stm->fetch_assoc () )
		{
			$friends [$i] = $row;
			$i ++;
		}
		$con->close ();
		return json_encode ( $friends );
	}
	public static function addFriend($FriendsName, $FriendsPhoto, $FriendsEmail, $UserEmail)
	{
		$con = parent::getConnection ();
		$result = false;
		$stm = $con->prepare ( "INSERT IGNORE INTO UserConnections (FriendsName, FriendsPhoto, FriendsEmail, UserEmail) VALUES(?,?,?,?)" );
		$stm->bind_param ( "ssss", $FriendsName, $FriendsPhoto, $FriendsEmail, $UserEmail );
		
		if ($stm->execute ())
		{
			$result = true;
		}
		$con->close ();
		return $result;
	}
	public static function removeFriend($FriendsEmail, $UserEmail)
	{
		$con = parent::getConnection ();
		$result = false;
		if ($con->query ( "DELETE FROM UserConnections WHERE FriendsEmail = '$FriendsEmail' AND UserEmail = '$UserEmail'" ))
		{
			$result = true;
		}
		$con->close ();
		return $result;
	}
}
?>