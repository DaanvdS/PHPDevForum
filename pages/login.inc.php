<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
?>
<?php
if(!isLoggedIn()){
	if(!isset($_POST['forumPassword'])){
		?>
		<form method="post" action="">
			<input type='hidden' name='goto' value='<?php echo $_GET['goto'];?>'>
			<input type='hidden' name='goid' value='<?php echo $_GET['goid'];?>'>
			<table border="1">
				<tr><td>Username</td><td><input type="text" name="forumUsername" value="" autofocus></td></tr>
				<tr><td>Password</td><td><input type="password" name="forumPassword" value=""></td></tr>
				<tr><td colspan="2"><div align="center"><input type="submit" name="submit" value="Log in"></div></td></tr>
				<tr><td colspan="2"><div align="center"><a href="?p=passwordforgotten">Forgotten your password?</a></div></td></tr>
			</table>
		</form>
		<?php
	} else {
		if($_POST['forumPassword']=="" || $_POST['forumUsername']==""){
			echo "<p>Username/Password not filled in!";
		} else {
			include("dbconnect.inc.php");
			$MySQL['query']="SELECT `id`, `admin` FROM `users` WHERE `username`='".$MySQL['connection']->escape_string($_POST['forumUsername'])."' AND `password`='".md5($_POST['forumPassword'])."' AND `activated` = '1' LIMIT 1";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
			if($MySQL['result']->num_rows==1){
				$MySQL['row']=$MySQL['result']->fetch_assoc();
				$_SESSION['forumUserID']=$MySQL['row']['id'];
				if($MySQL['row']['admin']==1){$_SESSION['forumAdmin']=1;}else{$_SESSION['forumAdmin']=0;}
				echo '<meta http-equiv="refresh" content="0; url=?p='.$_POST['goto'].'&id='.$_POST['goid'].'" />';
			} else {
				echo "<p>Username/Password incorrect/Account was not activated!</p>";
			}
			include('dbdisconnect.inc.php');
		} 
	}
} else {
	echo "<p>Logged in.</p>";
	echo "<br><a href=\"?p=logout\">Log out</a>";
}
?>