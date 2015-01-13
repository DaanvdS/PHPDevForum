<?php
	$id=getID();
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		ptbShow("t",$id);
	}
?>
