<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
?>
<?php
if(!isLoggedIn()){
	if(!isset($_POST['forumUsername'])){
		?>
		<form method="post" action="">
			<table border="1">
				<tr><td>Username</td><td><input type="text" name="forumUsername" value=""></td></tr>
				<tr><td>Email address</td><td><input type="text" name="forumEmailaddress" value=""></td></tr>
				<tr><td colspan="2"><div align="center"><input type="submit" name="submit" value="Register"></div></td></tr>
			</table>
		</form>
		<?php
	} else {
		if($_POST['forumEmailaddress'] == "" || $_POST['forumUsername'] == ""){
			echo "<p>Username/Emailaddress not filled in!";
		} else {
			include("dbconnect.inc.php");
			$MySQL['query'] = "SELECT `id` FROM `users` WHERE `username`='".$_POST['forumUsername']."' AND `emailaddress`='".$_POST['forumEmailaddress']."' LIMIT 1"
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			if($MySQL['result']->num_rows == 1){
				$code = rand();
				$MySQL['query'] = "UPDATE `users` SET `password` = '".md5($code)."' WHERE `username` = '".$_POST['forumUsername']."'";
				$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
				if($MySQL['connection']->affected_rows == 1){
					echo 'A new password had been sent to your emailaddress.';
					$message = '
					<html>
					<head>
					  <title>New password PHPDev forum</title>
					</head>
					<body>
					  <p>Dear '.$_POST['forumUsername'].',</p> 
					  <p>It seems you have requested a new password. It has been changed to '.$code.'</p>
					  <p>Good luck!</p>
					</body>
					</html>
					';

					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'To: '.$_POST['forumUsername'].'<'.$_POST['forumEmailaddress'].'>' . "\r\n";
					$headers .= 'From: PHPDev <info@m6a9.leerling.lekenlinge.nl>' . "\r\n";

					mail($_POST['forumEmailaddress'], "Activation account at PHPDev", $message, $headers);
				} else {
					echo "<p>Something went wrong!</p>";
				}
				
			} else {
				echo "<p>This username/emailaddress is not known.</p>";
			}	
		}
	}
} else {
	echo "<p>You are already logged in!</p>";
}
?>