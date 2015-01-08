<?php
	include('dbconnect.inc.php');
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		ptbShow("b","");
	}
	include('dbdisconnect.inc.php');
?>