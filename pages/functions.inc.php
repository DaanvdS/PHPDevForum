<?php 
function isLoggedIn(){
	if((isset($_SESSION['forumUserID'])) & ($_SESSION['forumUserID']!=="")){
		return true;
	} else {
		return false;
	}
}
?>