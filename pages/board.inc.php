<?php
	include('dbconnect.inc.php');
	$id=getID();
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		ptbShow("t",$id);
	}
	include('dbdisconnect.inc.php');
?>