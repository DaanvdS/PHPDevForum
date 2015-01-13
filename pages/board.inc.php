<?php
	//Displays threads in a specified board
	$id=getID();
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		ptbShow("t",$id);
	}
?>
