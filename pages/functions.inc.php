<?php 
function isLoggedIn(){
	if((isset($_SESSION['forumUserID'])) & ($_SESSION['forumUserID']!=="")){
		return true;
	} else {
		return false;
	}
}
function isAdmin(){
	if((isset($_SESSION['forumAdmin'])) & ($_SESSION['forumAdmin']!=="")){
		return true;
	} else {
		return false;
	}
}
//hoi
?>
