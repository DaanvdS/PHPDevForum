<?php
function intToBool($in) {
	if($in  ==  0) {
		$out = "False";
	} else {
		$out = "True";
	}
	return $out;
}

function getIntIfIsset($getvar){
	if(isset($_GET[$getvar])){
		$out = $_GET[$getvar];
	} else {
		$out = 0;
	}
	return $out;
}

function getStrIfIsset($in,$getvar){
	if(isset($_GET[$getvar])){
		$out = $_GET[$getvar];
	} else {
		$out = "";
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
	if(isset($_SESSION['forumUserID']) && $_SESSION['forumUserID']! == ""){
		return true;
	} else {
		return false;
	}
}

function isOwner($id){
	if($id == $_SESSION['forumUserID']){
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

function hasRights($id,$op){
	if(isLoggedIn() && (isOwner($id) || isAdmin() || isOP($op))) {
		return true;
	} else {
		return false;
	}
}

function isOP($op){
	if ($_SESSION['forumUserID'] == $op) {
		return true;
	} else {
		return false;
	}
}

function getUsername($userID){
	include("dbconnect.inc.php");
	$MySQL['query']="SELECT `username` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows! == 0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$out=$MySQL['row']['username'];
	} else {
		$out="Something went wrong.";
	}
	return $out;
}

function getFirstName($userID){
	include("dbconnect.inc.php");
	$MySQL['query'] = "SELECT `firstname` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows! == 0){
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
	if($MySQL['result']->num_rows! == 0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$out=$MySQL['row']['lastname'];
	} else {
		$out="Something went wrong.";
	}
	return $out;
}

function getSignature($userID){
	include("dbconnect.inc.php");
	$MySQL['query']="SELECT `sig` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows! == 0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		if(($MySQL['row']['sig']! == "")&&($MySQL['row']['sig']! == null)){
			$out="<hr /><div class='signature'>".$MySQL['row']['sig']."</div>";
		} else {
			$out="";
		}
	} else {
		$out="Oops.";
	}
	return $out;
}

function getID(){
	if(isset($_GET['id'])){$id=$_GET['id'];}else{echo "Something's gone wrong.";exit();}
	return $id;
}

function retrieveAvatars($userID){
	include("dbconnect.inc.php");
	$MySQL['query']="SELECT `username`, `avatar` FROM `users` WHERE `id` = '".$userID."' LIMIT 1";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows! == 0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$userName=$MySQL['row']['username'];
		$currentAvatar=$MySQL['row']['avatar'];
	} else {
		echo "User doesn't exist.";
	}
	
	$MySQL['query']="SELECT * FROM `avatars`";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	
	$out="<table>";
	while($MySQL['row']=$MySQL['result']->fetch_assoc()){
		if($MySQL['row']['id'] == "1"){
			$imagePath=$userName;
			$upload="<form method='post' enctype='multipart/form-data'>
						<input type='hidden' name='mode' value='uploadavatar'>
						<input type='file' name='avatarimg' accept='image/*'><br>
						<input type='submit' name='avatarsubmit' value='Upload'>
					</form><br>";
		} else {
			$imagePath=$MySQL['row']['id'];
			$upload="";
		}
		if(!file_exists("images/avatars/".$imagePath.".img")){
			$imagePath="notfound";
			$out.="
			<tr>
				<td"; if($MySQL['row']['id'] == $currentAvatar)$out.=" class='selected'"; $out.=">";
			$out.="
					".$upload."
					<img height='100px' src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>
				</td>
			</tr>";
		} else {
			$out.="
			<tr>
				<td"; if($MySQL['row']['id'] == $currentAvatar)$out.=" class='selected'"; $out.=">";
			$out.="
					".$upload."<a href='?p=userpanel&mode=setavatar&avatar=".$MySQL['row']['id']."'>
						<img height='100px' src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>
					</a>
				</td>
			</tr>";
		}
	}
	$out.="</table>";
	return $out;
	include("dbdisconnect.inc.php");
}

function getUserAvatar($userID){
	include("dbconnect.inc.php");
	$MySQL['query']="SELECT `avatars`.`id`, `avatars`.`description`, `users`.`username` FROM `avatars`, `users` WHERE `users`.`id` = '".$userID."' AND `avatars`.`id` = `users`.`avatar` LIMIT 1";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows == 1){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$userName=$MySQL['row']['username'];
		if($MySQL['row']['id'] == "1"){$imagePath=$userName;}else{$imagePath=$MySQL['row']['id'];}
		$out="<img src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>";
	}
	return $out;
	include("dbdisconnect.inc.php");
}

function getUserRank($userID){
	include("dbconnect.inc.php");
	$MySQL['query']="SELECT COUNT(*) AS `amountofPosts` FROM `posts` WHERE `user_id` = '".$userID."'";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows! == 0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
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
?>