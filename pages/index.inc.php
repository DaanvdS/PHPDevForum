<?php
	include('ptb.inc.php');
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		ptbShow("b","");
	}
?>