<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
if(isset($_GET['c']) && isset($_GET['u'])){
	include("dbconnect.inc.php");
	$MySQL['result']= $MySQL['connection']->query("SELECT `id` FROM `users` WHERE `activationcode` = '".$_GET['c']."' AND `activated` = '0' AND `id` = '".$_GET['u']."' LIMIT 1");
	if($MySQL['result']->num_rows==1){
		$MySQL['result']= $MySQL['connection']->query("UPDATE `users` SET `activated` = '1', `activationcode` = '0' WHERE  `id` = '".$_GET['u']."'");
		if($MySQL['connection']->affected_rows==1){
			echo "<p>Account activated succesfully.</p>";
		} else {
			echo "<p>Something went wrong when activating the account.</p>";
		}
	} else {
		echo "<p>Account not found or it has already been activated.</p>";
	}
} else { echo "<p>Not all the information required was specified.</p>"; }