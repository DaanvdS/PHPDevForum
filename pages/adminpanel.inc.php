<?php
//forum Informatica, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
?>
<?php
logAction();
if(isLoggedIn() && isAdmin()){
	include("pages/adminfunctions.inc.php");
	switch(getIfIssetGet('section', '')){
    	case '': echo "<p>Section: <a href='?p=adminpanel&section=usermanagement'>User management</a> <a href='?p=adminpanel&section=pull'>Git pull</a></p>"; break;
		case 'usermanagement': adminShowUserPanel(); break;
		case 'pull': 
			include("dbconnect.inc.php");
			$outcome=shell_exec("sh /home/daan/public_html/forum/PHPDevForum/pull.sh 2>&1");
			echo '<script>alert("Git: '.$MySQL['connection']->escape_string($outcome).'");</script><meta http-equiv="refresh" content="0; url=?p=adminpanel" />';
			include("dbdisconnect.inc.php");
			break;
	}
} else {
	echo "<p>You are not logged in/do not have the required rights.</p>";
}
//echo "<form id='gitpull' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='mode' value='gitpull'><input type='submit' value='Git pull' /></form>";
?>
