<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
?>
<?php
if(!isLoggedIn()){
	if(!isset($_POST['forumPassword'])){
		?>
		<form method="post" action="">
			<table border="1">
				<tr><td>First name</td><td><input type="text" name="forumFirstName" value=""></td></tr>
				<tr><td>Last name</td><td><input type="text" name="forumLastName" value=""></td></tr>
				<tr><td>Username</td><td><input type="text" name="forumUsername" value=""></td></tr>
				<tr><td>Password</td><td><input type="password" name="forumPassword" value=""></td></tr>
				<tr><td>Email address</td><td><input type="text" name="forumEmailaddress" value=""></td></tr>
				<tr><td colspan="2"><div align="center"><input type="submit" name="submit" value="Register"></div></td></tr>
			</table>
		</form>
		<?php
	} else {
		if($_POST['forumFirstName']=="" || $_POST['forumLastName']=="" || $_POST['forumPassword']=="" || $_POST['forumUsername']=="" || $_POST['forumEmailaddress']==""){
			echo "<p>Something was not filled in!";
		} else {
			include("dbconnect.inc.php");
			$MySQL['result']= $MySQL['connection']->query("SELECT `id` FROM `users` WHERE `username`='".$_POST['forumUsername']."' LIMIT 1");
			if($MySQL['result']->num_rows==1){
				echo "<p>This username is already taken.</p>";
			} else {
				$code = rand();
				$MySQL['result']=$MySQL['connection']->query("INSERT INTO `users` (`firstname`, `lastname`, `groupid`, `username`, `password`, `emailaddress`, `activationcode`, `activated`, `admin`) VALUES ('".$_POST['forumFirstName']."', '".$_POST['forumLastName']."', '1', '".$_POST['forumUsername']."', '".md5($_POST['forumPassword'])."', '".$_POST['forumEmailaddress']."', '".$code."', '0', '0')");
				if($MySQL['connection']->affected_rows==1){
					echo "<p>Registration was completed succesfully. Please activate your account via the email that was sent to your address.";
					$message = '
					<html>
					<head>
					  <title>Registration PHPDev forum</title>
					</head>
					<body>
					  <p>Dear '.$_POST['forumUsername'].',</p> 
					  <p>Thank you for signing up at the PHPDev forums.</p>
					  <p>In order to complete your registration, please click <a href="http://forum.s-nl.net/?p=activate&c='.$code.'&u='.$MySQL['connection']->insert_id.'">this link</a> to activate your account.</p>
					  <p>See you online!</p>
					</body>
					</html>
					';

					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					// Additional headers
					$headers .= 'To: '.$_POST['forumUsername'].'<'.$_POST['forumEmailaddress'].'>' . "\r\n";
					$headers .= 'From: PHPDev <info@m6a9.leerling.lekenlinge.nl>' . "\r\n";


					// Mail it
					mail($_POST['forumEmailaddress'], "Activation account at PHPDev", $message, $headers);
				} else {
					echo "<p>Username/Password incorrect!</p>";
				}
			}
			include('dbdisconnect.inc.php');
		} 
	}
} else {
	echo "<p>You are already logged in!.</p>";
	echo "<br><a href=\"?p=logout\">Log uit</a>";
}
?>