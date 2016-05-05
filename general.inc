<?php
function __autoload($class_name)
{
	require_once ("model/$class_name.php");
}
?>