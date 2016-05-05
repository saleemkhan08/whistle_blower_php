<?php
class Issues extends Database
{
	public static function addIssue($issue)
	{
		$con = parent::getConnection ();
		$result = "error";
		$stm = $con->prepare ( "INSERT INTO Issues 
					(noOfImages, userId,userPhotoUrl,username,description, areaType, radius, latitude, longitude) 
					VALUES(?,?,?,?,?,?,?,?,?)" );
		
		$stm->bind_param ( "isssssidd", $issue ["noOfImages"], $issue ["userId"], $issue ["userPhotoUrl"], $issue ["username"], $issue ["description"], $issue ["areaType"], $issue ["radius"], $issue ["latitude"], $issue ["longitude"] );
		
		if ($stm->execute ())
		{
			$lastId = $con->insert_id;
			$result = $lastId;
		}
		$con->close ();
		return $result;
	}
	public static function deleteIssue($issueId)
	{
		$con = parent::getConnection ();
		$result = "Please Try Again";
		if ($con->query ( "DELETE FROM Issues WHERE issueId = $issueId" ))
		{
			$result =  "Deleted";
		}
		$con->close ();
		return $result;
	}
	public static function getIssueById($issueId)
	{
		$con = parent::getConnection ();
		$issues = array ();
		$result = "error";
		$stm = $con->query ( "SELECT * FROM Issues WHERE issueId = '$issueId'" );
		if ($row = $stm->fetch_assoc ())
		{
			$result = $row;
		}
		$con->close ();
		return $result;
	}
	public static function getIssues($limit, $offset)
	{
		$con = parent::getConnection ();
		$issues = array ();
		$result = "error";
		$stm = $con->query ( "SELECT * FROM Issues ORDER BY issueId DESC LIMIT $offset, $limit" );
		$i = 0;
		while ( $row = $stm->fetch_assoc () )
		{
			$issues [$i] = $row;
			$i ++;
		}
		$con->close ();
		return json_encode ( $issues );
	}
	public static function reportSpam($issueId)
	{
		$con = parent::getConnection ();
		$result = "Please Try Again!";
		if ($con->query ( "UPDATE Issues SET status = 'spam' WHERE issueId = '$issueId'" ))
		{
			$result = "Reported";
		}
		$con->close ();
		return $result;
	}
}
?>