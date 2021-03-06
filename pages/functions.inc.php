<?php
include("ptb.inc.php");
require_once 'mobileDetect.inc.php';
$detect = new Mobile_Detect;

function intToBool($in) {
	if($in  ==  0) {
		$out = "False";
	} else {
		$out = "True";
	}
	return $out;
}

function getIfIssetGet($getvar, $default){
	if(isset($_GET[$getvar])){
		$out = $_GET[$getvar];
	} else {
		$out = $default;
	}
	return $out;
}

function getIfIssetPost($postvar, $default){
	if(isset($_POST[$postvar])){
		$out = $_POST[$postvar];
	} else {
		$out = $default;
	}
	return $out;
}

function getLoggedInUser(){
	if(isset($_SESSION['forumUserID'])){
		return $_SESSION['forumUserID'];
	} else {
		return "0";
	}
}

function getTitle($page, $id){
	include("dbconnect.inc.php");
	if($page=='thread' || $page=='board'){
		switch($page){
	    	case 'thread':
	      		$tablename='threads';
	    		break;
	      	case 'board':
	      		$tablename='boards';
	      		break;
		}
		$MySQL['query'] = "SELECT `name` FROM `".$tablename."` WHERE `id` = '".$id."' LIMIT 1";
		$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows == 1){
			$MySQL['row'] = $MySQL['result']->fetch_assoc();
			$out = "Forum - ".$MySQL['row']['name'];
		} else {
			$out = "Forum - Unknown";
		}
	} else {
		$out = "Forum - ".ucfirst($page);
	}
	return $out;
}

function toggleInt($in){
	if($in  ==  0) {
		$out = 1;
	} else {
		$out = 0;
	}
	return $out;
}

function isLoggedIn(){
	if(isset($_SESSION['forumUserID']) && $_SESSION['forumUserID'] !== ""){
		return true;
	} else {
		return false;
	}
}

function isOwner($id){
	if($id == getLoggedInUser()){
		return true;
	} else {
		return false;
	}
}

function isAdmin(){
	if($_SESSION['forumAdmin'] == 1){
		return true;
	} else {
		return false;
	}
}
function hasRights($id,$groupid){
	if(isset($groupid)){
		return isInGroup($id,$groupid);
	} else {
		return false; // If the board isn't in a group, everyone has permission.
	}
}
function hasSpecialRights($id,$op){
	if(isLoggedIn() && (isOwner($id) || isAdmin() || isOP($op))){
		return true;
	} else {
		return false;
	}
}

function isInGroup($id,$groupid){
	if ($groupid == 1) {
		return true;
	}
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT * FROM usersInGroups WHERE userID = '".$id."' AND groupID = '".$groupid."'";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if ($MySQL['result']->num_rows > 0){
		return true;
	} else {
		return false;
	}
}

function isOP($op){
	if(getLoggedInUser() == $op){
		return true;
	} else {
		return false;
	}
}

function getUsername($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `username` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$out = $MySQL['row']['username'];
	} else {
		$out = "Something went wrong.";
	}
	return $out;
}

function getFirstName($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `firstname` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$out = $MySQL['row']['firstname'];
	} else {
		$out = "Something went wrong.";
	}
	return $out;
}

function getLastName($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `lastname` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$out = $MySQL['row']['lastname'];
	} else {
		$out = "Something went wrong.";
	}
	return $out;
}

function getSignature($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `sig` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		if(($MySQL['row']['sig'] !== "")&&($MySQL['row']['sig'] !== null)){
			$out = "<hr /><div class='signature'>".$MySQL['row']['sig']."</div>";
		} else {
			$out = "";
		}
	} else {
		$out = "Oops.";
	}
	return $out;
}

function getID(){
	if(isset($_GET['id'])){
		$id = $_GET['id'];
	} else {
		echo "Something's gone wrong.";exit();
	}
	return $id;
}

function retrieveAvatars($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `username`, `avatar` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$userName = $MySQL['row']['username'];
		$currentAvatar = $MySQL['row']['avatar'];
	} else {
		echo "User doesn't exist.";
	}
	
	$MySQL['query'] = "SELECT * FROM `avatars`";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	
	$out="<table>";
	while($MySQL['row'] = $MySQL['result']->fetch_assoc()){
		if($MySQL['row']['id'] == "1"){
			$imagePath = $userName;
			$upload =
					"<form method='post' enctype='multipart/form-data'>
						<input type='hidden' name='mode' value='uploadavatar'>
						<input type='file' name='avatarimg' accept='image/*'><br>
						<input type='submit' name='avatarsubmit' value='Upload'>
					</form><br>";
		} else {
			$imagePath = $MySQL['row']['id'];
			$upload = "";
		}
		if(!file_exists("images/avatars/".$imagePath.".img")){
			$imagePath = "notfound";
			$out.= "
			<tr>
				<td"; if($MySQL['row']['id'] == $currentAvatar)$out.=" class='selected'"; $out.=">";
			$out.= "
					".$upload."
					<img height='100px' src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>
				</td>
			</tr>";
		} else {
			$out.= "
			<tr>
				<td"; if($MySQL['row']['id'] == $currentAvatar)$out.=" class='selected'"; $out.=">";
			$out.= "
					".$upload."<a href='?p=userpanel&mode=setavatar&avatar=".$MySQL['row']['id']."'>
						<img height='100px' src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>
					</a>
				</td>
			</tr>";
		}
	}
	$out.= "</table>";
	return $out;
	include("dbdisconnect.inc.php");
}

function getUserAvatar($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `avatars`.`id`, `avatars`.`description`, `users`.`username` FROM `avatars`, `users` WHERE `users`.`id` = '".$userID."' AND `avatars`.`id` = `users`.`avatar` LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows == 1){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$userName = $MySQL['row']['username'];
		if($MySQL['row']['id'] == "1"){
			$imagePath=$userName;
		} else {
			$imagePath=$MySQL['row']['id'];
		}
		$out = "<img src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>";
	}
	return $out;
	include("dbdisconnect.inc.php");
}

function getUserRank($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT COUNT(*) AS `amountofPosts` FROM `posts` WHERE `user_id` = '".$userID."'";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$amposts=$MySQL['row']['amountofPosts'];
		if($amposts<=10)$out="Newbie";
		if($amposts>10&&$amposts<=20)$out="Junior";
		if($amposts>20)$out="Hero";
		$MySQL['result']=$MySQL['connection']->query("SELECT `admin` FROM `users` WHERE `id` = '".$userID."' AND `admin` = 1");
		if($MySQL['result']->num_rows == 1){
			$out="Administrator";
		}
	} else {
		$out="Something went wrong.";
	}
	return $out;
}

function logAction(){
	$userID=getLoggedInUser();
	$page=getIfIssetGet('p', '');
	$id=getIfIssetGet('id', '0');
	$action=getIfIssetGet('action', '');
	if($action==""){$action=getIfIssetGet('mode', '');}
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	include("dbconnect.inc.php");
	$MySQL['query'] = "INSERT INTO `logging` (`userID`, `ip`, `page`, `action`, `changedID`) VALUES ('".$userID."', '".$ip."', '".$page."', '".$action."', '".$id."')";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
}

function redirectIfDone($conn, $text, $page){
	if($conn->affected_rows==1){
		echo '<script>alert("'.$text.'");</script><meta http-equiv="refresh" content="0; url=?p='.$page.'" />';
	} else {
		echo "Something went wrong: <a href='?p=".$page."'>Return</a>";
	}
}
?>
