<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', 1 );

include_once 'general.inc';
if (isset ( $_POST ['action'] ) && ! empty ( $_POST ['action'] ))
{
	switch ($_POST ['action'])
	{
		case "addAccount" :
			signUp ();
			break;
		case "updateGcmId" :
			updateGcmId ();
			break;
		case "uploadFiles" :
			uploadFiles ();
			break;
		case "uploadFile" :
			uploadFile ();
			break;
		case "addIssue" :
			addIssue ();
			break;
		case "getIssues" :
			getIssues ();
			break;
		case "deleteIssue" :
			deleteIssue ();
			break;
		case "getUsers" :
			getUsers ();
			break;
		case "getFriends" :
			getFriends ();
			break;
		case "uploadFileToOtherServer" :
			uploadFileToOtherServer ();
			break;
		case "addFriend" :
			addFriend ();
			break;
		case "removeFriend" :
			removeFriend ();
			break;
		case "reportSpam" :
			reportSpam ();
			break;
	}
}

if (isset ( $_GET ['action'] ) && ! empty ( $_GET ['action'] ))
{
	switch ($_GET ['action'])
	{
		case "getIssues" :
			getIssues ();
			break;
		case "getIssueById" :
			getIssueById ();
			break;
	}
}
function reportSpam()
{
	echo Issues::reportSpam( $_POST['issueId']);
}

function deleteIssue ()
{
	echo Issues::deleteIssue($_POST['issueId']);
}

function getUsers()
{
	echo UserAccounts::getUsers ( $_POST ['email'], $_POST ['str'], $_POST ['offset'], $_POST ['limit'], $_POST ['category'] );
}
function getFriends()
{
	echo UserAccounts::getFriends ( $_POST ['userEmail'] );
}
function addFriend()
{
	echo UserAccounts::addFriend ( $_POST ['FriendsName'], $_POST ['FriendsPhoto'], $_POST ['FriendsEmail'], $_POST ['UserEmail'] );
}
function removeFriend()
{
	echo UserAccounts::removeFriend ( $_POST ['FriendsEmail'], $_POST ['UserEmail'] );
}
function sendMessage()
{
}
function getIssueById()
{
	echo json_encode ( Issues::getIssueById ( $_GET ["issueId"] ) );
}
function getIssues()
{
	echo Issues::getIssues ( $_GET ["limit"], $_GET ["offset"] );
}
function uploadFileToOtherServer()
{
	$target_path = "uploads/test.png";
	if (imagepng ( imagecreatefromstring ( file_get_contents ( $_FILES ['image'] ['tmp_name'] ) ), $target_path ))
	{
		uploadsHandler ( $target_path );
	}
}
function uploadsHandler($target_path)
{
	$source = "http://whistleblower-thnkin.rhcloud.com/" . $target_path;
	
	$url = 'http://uploads-thnkin.rhcloud.com/upload.php';
	$post_data = array ();
	$post_data ["source"] = $source;
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, true );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	return curl_exec ( $ch );
}
function addIssue()
{
	$issueId = Issues::addIssue ( $_POST );
	$fileName = "image";
	$response = "error";
	if ($response != $issueId)
	{
		$validextensions = array (
				"jpeg",
				"jpg",
				"png",
				"gif",
				"bmp" 
		);
		
		$ext = explode ( '.', basename ( $_FILES [$fileName] ['name'] ) );
		$file_extension = end ( $ext );
		
		if (in_array ( $file_extension, $validextensions ))
		{
			$target_path = "uploads/$issueId.png";
			if (imagepng ( imagecreatefromstring ( file_get_contents ( $_FILES [$fileName] ['tmp_name'] ) ), $target_path ))
			{
				if (uploadsHandler ( $target_path ) == "error")
				{
					Issues::deleteIssue ( $issueId );
				}
				else
				{
					$response = $issueId;
					unlink ( $target_path );
				}
			}
			else
			{
				Issues::deleteIssue ( $issueId );
			}
		}
	}
	echo $response;
}
function uploadFile()
{
	print_r ( $_POST );
}
function signUp()
{
	$account ['gcmId'] = getValue ( 'gcmId' );
	$account ['googleId'] = getValue ( 'googleId' );
	$account ['email'] = getValue ( 'email' );
	$account ['name'] = getValue ( 'name' );
	$account ['photoUrl'] = getValue ( 'photoUrl' );
	
	if (UserAccounts::addAccount ( $account ))
	{
		echo "Success";
	}
	else
	{
		echo "Failure";
	}
}
function uploadFiles()
{
	$noOfImages = count ( $_FILES ['image'] ['name'] );
	$totalNoOfIssues = file_get_contents ( "totalNoOfIssues.txt" );
	$response ['totalNoOfIssues'] = $totalNoOfIssues;
	for($imageIndex = 0; $imageIndex < $noOfImages; $imageIndex ++)
	{
		$validextensions = array (
				"jpeg",
				"jpg",
				"png",
				"gif",
				"bmp" 
		);
		
		$ext = explode ( '.', basename ( $_FILES ["image"] ['name'] [$imageIndex] ) );
		$file_extension = end ( $ext );
		
		if (in_array ( $file_extension, $validextensions ))
		{
			$imgName = 'issue_' . $totalNoOfIssues . "_" . $imageIndex . ".png";
			$pngFile = imagepng ( imagecreatefromstring ( file_get_contents ( $_FILES [$fileName] ['tmp_name'] [$imageIndex] ) ), $imgName );
			
			$target_path = "uploads/" . $imgName;
			if (move_uploaded_file ( $pngFile, $target_path ))
			{
				$response [$imgName] = 1;
			}
			else
			{
				$response [$imgName] = 0;
			}
		}
		else
		{
			$response [$imgName] = - 1;
		}
	}
	file_put_contents ( "totalNoOfIssues.txt", $totalNoOfIssues + 1 );
	echo json_encode ( $response );
}
function updateGcmId()
{
	$account ['gcmId'] = getValue ( 'gcmId' );
	$account ['googleId'] = getValue ( 'googleId' );
	
	if (UserAccounts::updateGcmId ( $account ))
	{
		echo "Success";
	}
	else
	{
		echo "Failure";
	}
}
function getValue($str)
{
	if (isset ( $_POST [$str] ) && ! empty ( $_POST [$str] ))
	{
		return $_POST [$str];
	}
	return "";
}