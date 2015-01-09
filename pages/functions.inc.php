<?php
function intToBool($in) {
	if($in == 0) {
		$out="False";
	} else {
		$out="True";
	}
	return $out;
}

function toggleInt($in){
	if($in == 0) {
		$out=1;
	} else {
		$out=0;
	}
	return $out;
}

function isLoggedIn(){
	if(isset($_SESSION['forumUserID']) && $_SESSION['forumUserID']!==""){
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
	if($MySQL['result']->num_rows!==0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$out=$MySQL['row']['username'];
	} else {
		$out="Something went wrong.";
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
	if($MySQL['result']->num_rows!==0){
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
		if($MySQL['row']['id']=="1"){
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
				<td"; if($MySQL['row']['id']==$currentAvatar)$out.=" class='selected'"; $out.=">";
			$out.="
					".$upload."
					<img height='100px' src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>
				</td>
			</tr>";
		} else {
			$out.="
			<tr>
				<td"; if($MySQL['row']['id']==$currentAvatar)$out.=" class='selected'"; $out.=">";
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
	if($MySQL['result']->num_rows==1){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$userName=$MySQL['row']['username'];
		if($MySQL['row']['id']=="1"){$imagePath=$userName;}else{$imagePath=$MySQL['row']['id'];}
		$out="<img src='images/avatars/".$imagePath.".img' title='".$MySQL['row']['description']."'>";
	}
	return $out;
	include("dbdisconnect.inc.php");
}

function getUserRank($userID){
	include("dbconnect.inc.php");
	$MySQL['query']="SELECT COUNT(*) AS `amountofPosts` FROM `posts` WHERE `user_id` = '".$userID."'";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows!==0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$amposts=$MySQL['row']['amountofPosts'];
		if($amposts<=10)$out="Newbie";
		if($amposts>10&&$amposts<=20)$out="Junior";
		if($amposts>20)$out="Hero";
		$MySQL['result']=$MySQL['connection']->query("SELECT `admin` FROM `users` WHERE `id` = '".$userID."' AND `admin` = 1");
		if($MySQL['result']->num_rows==1){
			$out="Administrator";
		}
	} else {
		$out="Something went wrong.";
	}
	return $out;
}

function getBreadCrumb(){
	include("dbconnect.inc.php");
	if(!isset($_GET['p'])){
		$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a></p></div>";
	} elseif($_GET['p']=='board') {
		$MySQL['query']="SELECT `name` FROM `boards` WHERE `id` = '".$_GET['id']."' LIMIT 1";
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows==1){
			$MySQL['row']=$MySQL['result']->fetch_assoc();
			$boardName=$MySQL['row']['name'];
			$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a> > <a class='hidden-a' href='?p=board&id=".$_GET['id']."'>".$boardName."</a></p></div>";
		}
	} elseif($_GET['p']=='thread') {
		if(isset($_GET['ptb'])&&($_GET['ptb']=='p')){$id=$_GET['return'];}else{$id=$_GET['id'];}
		$MySQL['query']="SELECT `boards`.`id`, `boards`.`name` AS `boardname`, `threads`.`name` AS `threadname` FROM `boards`, `threads` WHERE `threads`.`id` = '".$id."' AND `boards`.`id` = `threads`.`board_id` LIMIT 1";
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows==1){
			$MySQL['row']=$MySQL['result']->fetch_assoc();
			$boardID=$MySQL['row']['id'];
			$boardName=$MySQL['row']['boardname'];
			$threadName=$MySQL['row']['threadname'];
			$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a> > <a class='hidden-a' href='?p=board&id=".$boardID."'>".$boardName."</a> > <a class='hidden-a' href='?p=thread&id=".$id."'>".$threadName."</a></p></div>";
		}
	} else {
		$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a> > <a class='hidden-a' href='?p=".$_GET['p']."'>".ucwords($_GET['p'])."</a></p></div>";
	}
	
	return $out;
	include("dbdisconnect.inc.php");
}

function ptbSwitch($ptb){
	switch($ptb){
    	case 'p':
      		return array ('posts','thread','thread');
    		break;
      	case 't':
      		return array ('threads','board','board');
      		break;
      	case 'b':
      		return array ('boards','index','board');
      		break;
    }
}

function ptbDelete($ptb, $id, $return){
	include("dbconnect.inc.php");
	$ptb=ptbSwitch($ptb);
  	$MySQL['query']="DELETE FROM `".$ptb[0]."` WHERE `id`= ".$id." LIMIT 1";	
	$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['connection']->affected_rows==1){
		echo '<meta http-equiv="refresh" content="0; url=?p='.$ptb[1].'&id='.$return.'" />';
	}
	include("dbdisconnect.inc.php");
}

function ptbNew($ptb, $data, $return, $userID){
	include("dbconnect.inc.php");
	$data=$MySQL['connection']->real_escape_string($data);
	switch($ptb){
    	case 'p':
			$columns[0]='`text`';
			$columns[1]='`thread_id`';
			$columns[2]='`user_id`';
			$values[0]="'".$data."'";
			$values[1]="'".$return."'";
			$values[2]="'".$userID."'";
			break;
		case 't':
			$columns[0]='`name`';
			$columns[1]='`board_id`';
			$columns[2]='`op`';
			$values[0]="'".$data."'";
			$values[1]="'".$return."'";
			$values[2]="'".$userID."'";
			break;
		case 'b':
			$columns[0]='`name`';
			$values[0]="'".$data."'";
			break;
	}
	$ptb=ptbSwitch($ptb);
	$i=0;
	while($i<count($columns)){
		if($i==0){
			$fin_columns=$columns[$i];
			$fin_values=$values[$i];
		} else {
			$fin_columns=$fin_columns.', '.$columns[$i];
			$fin_values=$fin_values.', '.$values[$i];
		}
		$i++;
	}
  	$MySQL['query']="INSERT INTO `".$ptb[0]."` (".$fin_columns.") VALUES (".$fin_values.")";
	$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['connection']->affected_rows==1){
		echo '<meta http-equiv="refresh" content="0; url=?p='.$ptb[1].'&id='.$return.'" />';
	}
	include("dbdisconnect.inc.php");
}

function ptbChgSav($ptb, $id, $data, $return){
	include("dbconnect.inc.php");
	for($i=0;$i<count($data);$i++){
		$data[$i]=$MySQL['connection']->real_escape_string($data[$i]);
	}
	switch($ptb){
    	case 'p':
			$columns[0]='`text`';
			$values[0]="'".$data[0]."'";
			break;
		case 'b':
			$columns[0]='`name`';
			$values[0]="'".$data[0]."'";
			break;	
		case 't':
			$columns[0]='`name`';
			$columns[0]='`sticky`';
			$values[0]="'".$data[0]."'";
			$values[0]="'".$data[1]."'";
			break;			
	}
	
	$ptb=ptbSwitch($ptb);
	$fin_update=$columns[0].' = '.$values[0];
  	$MySQL['query']="UPDATE `".$ptb[0]."` SET ".$fin_update." WHERE `id` = ".$id;
	
	$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['connection']->affected_rows==1){
		echo '<meta http-equiv="refresh" content="0; url=?p='.$ptb[1].'&id='.$return.'" />';
	}
	include("dbdisconnect.inc.php");
}

function ptbChgForm($ptb, $id, $return){
	include("dbconnect.inc.php");
	switch($ptb){
    	case 'p':
			$columns[0]='`text`';
			break;
		case 't':
			$columns[0]='`name`';
			$columns[1]='`sticky`';
			break;
		case 'b':
			$columns[0]='`name`';
			break;
	}
	$ptbs=ptbSwitch($ptb);
	$i=0;
	while($i<count($columns)){
		if($i==0){
			$fin_columns=$columns[$i];
		} else {
			$fin_columns=$fin_columns.', '.$columns[$i];
		}
		$i++;
	}

	$MySQL['query']="SELECT ".$fin_columns." FROM `".$ptbs[0]."` WHERE `id` = ".$id." LIMIT 1";
  	
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows!==0){
		$MySQL['row']=$MySQL['result']->fetch_assoc();	
		echo "
				<form method='get'>
					<input type='hidden' name='action' value='save'>
					<input type='hidden' name='ptb' value='".$ptb."'>
					<input type='hidden' name='id' value='".$id."'>
					<input type='hidden' name='return' value='".$return."'>
					<input type='hidden' name='p' value='".$ptbs[1]."'>";
		if($ptb=='t'||$ptb=='b'){
			echo "	<input type='text' name='data' value='".$MySQL['row'][(substr($columns[0], 1, -1))]."'>";
		} elseif($ptb=='p'){
			echo "	<textarea name='data'>".$MySQL['row'][(substr($fin_columns, 1, -1))]."</textarea>";
		}
		if($ptb=='t'){
			if($MySQL['row'][(substr($columns[1], 1, -1))]){$sticky = ' selected';}else{$sticky='';}
			echo "	<label for='sticky'>Sticky: </label><select name='sticky'><option".$sticky." value='0'>False</option><option".$sticky." value='1'>True</option>";
		}
		echo "	<input class='post-area-submit' type='submit' name='save' value='Save'></form>";
	}
	include("dbdisconnect.inc.php");
}

function ptbAction(){
	//id is the id of the item that is being affected_rows
	//return is the id 
	switch($_GET['action']){
		case 'delete':
			ptbDelete($_GET['ptb'], $_GET['id'], $_GET['return']);
			break;
		case 'new': 
			ptbNew($_GET['ptb'], $_GET['data'], $_GET['return'], $_SESSION['forumUserID']);
			break;
		case 'save': 
			if($_GET['ptb']=='t'){$sticky=$_GET['sticky'];}{$sticky=0;}
			ptbChgSav($_GET['ptb'], $_GET['id'], array($_GET['data'],$sticky), $_GET['return']);
			break;
		case 'change': 
			ptbChgForm($_GET['ptb'], $_GET['id'], $_GET['return']);
			break;
	}
}

function ptbShow($ptb, $return){
	include('dbconnect.inc.php');
	$ptbs=ptbSwitch($ptb);
	if($ptb=='b'){$MySQL['query']="SELECT * FROM `".$ptbs[0]."`";}
	if($ptb=='t'){$MySQL['query']="SELECT * FROM `".$ptbs[0]."` WHERE board_id=".$return." ORDER BY `sticky` DESC, `id` ASC";}
	if($ptb=='p'){$MySQL['query']="SELECT `posts`.`text`, `posts`.`date_created`, `users`.`firstname`, `users`.`sig`, `posts`.`id`, `posts`.`user_id`, `threads`.`name`, `threads`.`op` FROM `posts`, `users`, `threads` WHERE `threads`.`id`= ".$return." AND `posts`.`thread_id`=".$return." AND `users`.`id` = `posts`.`user_id` ORDER BY date_created ASC";}
	$MySQL['result']= $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	switch($ptb){
		case 'p':
			$i=0;
			if($MySQL['result']->num_rows > 0){
				while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
					echo "
					<table class='post'>
						<tr>
							<td class='userbar'>
								<p class='username'>".$MySQL['row']["firstname"]."</p>
								<p class='rank'>".getUserRank($MySQL['row']["user_id"])."</p>
								<p class='avatar'>".getUserAvatar($MySQL['row']["user_id"])."</p>";
					if(isLoggedIn()){
						echo "
								<p class='postbuttons'>
									<script>var text".$i." = '".$MySQL['connection']->real_escape_string($MySQL['row']["text"])."';</script>
									<a class='hidden-a' onClick='quote(\"".$MySQL['row']["firstname"]."\",text".$i.")' href='#newPost'>
										<img src='images/quote.png'>
									</a>";
					}
					if (hasRights($MySQL['row']['user_id'],$MySQL['row']['op'])) {
						echo "
									<a class='hidden-a' href='?p=thread&action=change&ptb=p&id=".$MySQL['row']['id']."&return=".$return."'>
										<img src='images/change.png'>
									</a>
									<a class='hidden-a' href='?p=thread&action=delete&ptb=p&id=".$MySQL['row']['id']."&return=".$return."'>
										<img src='images/remove.png'>
									</a>
								</p>";
					}
					if($i==0){
						$threadtitle=$MySQL['row']["name"];
						$postnr="First post";
					} else {
						$threadtitle="Re: ".$MySQL['row']["name"];
						$postnr="Answer #".$i;
					}
					if(!$MySQL['row']['sig']==''){
						$sig="<hr /><div class='signature'>".$MySQL['row']['sig']."</div>";
					} else { 
						$sig=""; 
					}
					echo "
							</td>
							<td>
								<div class='post-content'>
									<p><b>".$threadtitle."</b></p>
									<p class='postedon'>".$postnr.", posted on: ".$MySQL['row']["date_created"]."
									<hr />".$MySQL['row']["text"].$sig."
								</div>
							</td>
						</tr>
					</table>";
					$i++;
				}
			}
			if(isLoggedIn()){
			echo "
					<form method='get' id='newPost' action=''>
						<input type='hidden' name='action' value='new'>
						<input type='hidden' name='ptb' value='p'>
						<input type='hidden' name='return' value='".$return."'>
						<input type='hidden' name='p' value='index'>
						<div class='post-area'><textarea name='data' rows='4' cols='50'></textarea></div>
						<input class='post-area-submit' type='submit' name='submit' value='Submit'>
					</form>";
			}
			break;
		case 't' || 'b':
			if($MySQL['result']->num_rows > 0){
				echo "
				<table class='item-container'>";
				while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
					if(isset($MySQL['row']['sticky']) && $MySQL['row']['sticky']==1){$sticky='-sticky';}else {$sticky='';}
					echo "
					<tr>
						<td class='item".$sticky."' onclick='window.location.href = \"?p=".substr($ptbs[0],0,-1)."&id=".$MySQL['row']["id"]."\"'>
							".$MySQL['row']["name"]."
						</td>";
					if(isLoggedIn() && isAdmin()){
						echo "
						<td class='item-icons'>
							<p>
								<a class='hidden-a' href='?p=index&action=change&ptb=".$ptb."&id=".$MySQL['row']["id"]."&return=".$return."'>
									<img src='images/change.png'>
								</a>
								<a class='hidden-a' href='?p=index&action=delete&ptb=".$ptb."&id=".$MySQL['row']['id']."&return=".$return."'>
									<img src='images/remove.png'>
								</a>
							</p>
						</td>";
						
					} else {
						echo "
						<td class='item-empty'>
						</td>";
					}
					echo "
					</tr>";
				} 
				echo "
				</table>";
			}
			
			if(isLoggedIn() && (isAdmin() || $ptb == 't')) {
				echo "
					<form method='get'>
						<input type='hidden' name='action' value='new'>
						<input type='hidden' name='ptb' value='".$ptb."'>
						<input type='hidden' name='return' value='".$return."'>
						<input type='hidden' name='p' value='index'>
						<input type='text' name='data' value='New'>
						<input type='submit' name='submit' value='Make'>
					</form>";
			}
			break;
	}
	include('dbdisconnect.inc.php');
}
?>