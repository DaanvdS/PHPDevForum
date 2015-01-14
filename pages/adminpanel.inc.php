<?php
//forum Informatica, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
?>
<?php
logAction();
if(isLoggedIn() && isAdmin()){
	include("pages/adminfunctions.inc.php");
	switch(getIfIssetGet('section', '')){
    	case '': echo "<p>Section: <a href='?p=adminpanel&section=usermanagement'>User management</a></p>"; break;
		case 'usermanagement': adminShowUserPanel(); break;
	}
} else {
	echo "<p>You are not logged in/do not have the required rights.</p>";
}
?>
