<?php
require_once 'gcm.php';
$gcm = new GCM();
$data = array();
$data['user'] = "Saleem";
$data['message'] = "Test Message";
$data ['title'] = "Google Cloud Messaging";
$gcmId = "dQ-ve5499eY:APA91bHqeLyCeiJLwySZRAvnbdtRkjM4PQFWER8r_OoWJ8zkFlS-ZtqP_wSG1qVYu-nozdmjKwcuvuXsn6z7FdfBS2_9DAA2DVFHxCE9wKr-tlremmhapl4bfb3pSapIPvyLL5ninTMm";

echo $gcm->send($gcmId, $data);
?>