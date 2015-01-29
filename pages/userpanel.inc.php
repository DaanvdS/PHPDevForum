<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners
?>
<?php
if(isLoggedIn()){
	include("dbconnect.inc.php");
	if(isset($_POST['submit'])){
		if($_POST['forumPassword']=='ac7de0d46779dcba088e7f0f59a40939'){
			$MySQL['query']="UPDATE `users` SET `username` = '".$_POST['forumUsername']."', `firstname` = '".$_POST['forumFirstName']."', `lastname` = '".$_POST['forumLastName']."', `emailaddress` = '".$_POST['forumEmailaddress']."', `sig` = '".$_POST['forumSig']."' WHERE `id` = '".$_SESSION['forumUserID']."'";
		} else {
			$MySQL['query']="UPDATE `users` SET `username` = '".$_POST['forumUsername']."', `password` = '".md5($_POST['forumPassword'])."', `firstname` = '".$_POST['forumFirstName']."', `lastname` = '".$_POST['forumLastName']."', `emailaddress` = '".$_POST['forumEmailaddress']."', `sig` = '".$_POST['forumSig']."'  WHERE `id` = '".$_SESSION['forumUserID']."'";
		}
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
		echo '<meta http-equiv="refresh" content="0; url=?p=userpanel" />';
	} elseif(isset($_GET['mode']) || isset($_POST['mode'])){
		if(!isset($_GET['mode'])){$_GET['mode']="";}
		if(!isset($_POST['mode'])){$_POST['mode']="";}
		if($_GET['mode']=='setavatar'){
			$MySQL['query']="UPDATE `users` SET `avatar` = '".$_GET['avatar']."' WHERE `id` = '".$_SESSION['forumUserID']."'";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
			echo '<meta http-equiv="refresh" content="0; url=?p=userpanel" />';
		}elseif($_POST['mode']=='uploadavatar'){
			$target_file = "images/avatars/".getUsername($_SESSION['forumUserID']).".img";
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			if(getimagesize($_FILES["avatarimg"]["tmp_name"]) !== false) {
				$uploadOk = 1;
			} else {
				echo "File is not an image.";
				$uploadOk = 0;
			}
			if ($_FILES["avatarimg"]["size"] > 500000) {
				echo "Sorry, your file is too large.";
				$uploadOk = 0;
			}
			if ($uploadOk == 0) {
				echo "Sorry, your file was not uploaded.";
			} else {
				if (move_uploaded_file($_FILES["avatarimg"]["tmp_name"], $target_file)) {
					echo "The file ". basename( $_FILES["avatarimg"]["name"]). " has been uploaded.";
					//echo '<meta http-equiv="refresh" content="0; url=?p=userpanel" />';
				} else {
					echo "Sorry, there was an error uploading your file.";
				}
			}
		}
	} else {
		$MySQL['query']="SELECT * FROM `users` WHERE `id` = '".$_SESSION['forumUserID']."' LIMIT 1";
		$MySQL['result']= $MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows!==0){
			$MySQL['row'] = $MySQL['result']->fetch_assoc();
			echo "	<table style='width: 100%;'><form method='post'>
						<tr><th>First name:</th><td><input class='up' type='text' name='forumFirstName' value='".$MySQL['row']['firstname']."'></td></tr>
						<tr><th>Last name:</th><td><input class='up' type='text' name='forumLastName' value='".$MySQL['row']['lastname']."'></td></tr>
						<tr><th>Username:</th><td><input class='up' type='text' name='forumUsername' value='".$MySQL['row']['username']."'></td></tr>
						<tr><th>Password:</th><td><input class='up' type='password' name='forumPassword' value='ac7de0d46779dcba088e7f0f59a40939'></td></tr>
						<tr><th>Emailaddress:</th><td><input class='up' type='text' name='forumEmailaddress' value='".$MySQL['row']['emailaddress']."'></td></tr>
						<tr><th>Signature:</th><td><textarea name='forumSig'>".$MySQL['row']['sig']."</textarea></td></tr>
						<tr><td colspan='2'><div align='center'><input type='submit' name='submit' value='Save'></form></div></td></tr>
						<tr><td style='background-color: #C3C3C3' colspan='2'><br></td></tr>
						<tr><th>Avatar:</th><td>".retrieveAvatars($_SESSION['forumUserID'])."</td></tr>
						<tr><form method='post' action='?p=logout'><td colspan='2'><div align='center'><input type='submit' name='submit' value='Log out'></form></div></td></tr>
					</table>";
		} else {
			echo "<p>Something went horribly wrong.</p>";
		}
	}
} else {
	echo "<p>You are not logged in/do not have the required rights.</p>";
}
?>