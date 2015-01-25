<?php
	//Displays the boards
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		showBoards();
	}
?>
