<?php
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
	$ptb = ptbSwitch($ptb);
  	$MySQL['query'] = "DELETE FROM `".$ptb[0]."` WHERE `id`= ".$id." LIMIT 1";	
	$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['connection']->affected_rows == 1){
		echo '<meta http-equiv="refresh" content="0; url=?p='.$ptb[1].'&id='.$return.'" />';
	}
	include("dbdisconnect.inc.php");
}

function ptbNew($ptb, $data, $return, $userID){
	include("dbconnect.inc.php");
	$data=$MySQL['connection']->real_escape_string($data);
	switch($ptb){
    	case 'p':
			$columns[0] = '`text`';
			$columns[1] = '`thread_id`';
			$columns[2] = '`user_id`';
			$values[0] = "'".$data."'";
			$values[1] = "'".$return."'";
			$values[2] = "'".$userID."'";
			$MySQL['query'] = "SELECT `name`, `op` FROM `threads` WHERE `id` = '".$return."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			$MySQL['row'] = $MySQL['result']->fetch_assoc();
			$op = $MySQL['row']['op'];
			$name = $MySQL['row']['name'];
			$username = getFirstName($userID)." ".getLastName($userID);
			if(!$op == $userID){
				$MySQL['query'] = "INSERT INTO `messages` (`senderID`, `receiverID`, `text`) VALUES (0, ".$MySQL['row']['op'].", '<p>Hi,</p><p>".$username." has posted a reply onto your thread \"".$name."\". Click <a href=\"?p=thread&id=".$return."\">here</a> to view it.</p>')";
				$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			}
			break;
		case 't':
			$columns[0] = '`name`';
			$columns[1] = '`board_id`';
			$columns[2] = '`op`';
			$values[0] = "'".$data."'";
			$values[1] = "'".$return."'";
			$values[2] = "'".$userID."'";
			break;
		case 'b':
			$columns[0] = '`name`';
			$values[0] = "'".$data."'";
			break;
	}
	$ptb = ptbSwitch($ptb);
	$i = 0;
	while($i < count($columns)){
		if($i == 0){
			$fin_columns = $columns[$i];
			$fin_values = $values[$i];
		} else {
			$fin_columns = $fin_columns.', '.$columns[$i];
			$fin_values = $fin_values.', '.$values[$i];
		}
		$i++;
	}
  	$MySQL['query'] = "INSERT INTO `".$ptb[0]."` (".$fin_columns.") VALUES (".$fin_values.")";
	$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['connection']->affected_rows == 1){
		echo '<meta http-equiv="refresh" content="0; url=?p='.$ptb[1].'&id='.$return.'" />';
	}
	include("dbdisconnect.inc.php");
}

function ptbChgSav($ptb, $id, $data, $return, $pag){
	include("dbconnect.inc.php");
	for($i=0;$i<count($data);$i++){
		$data[$i] = $MySQL['connection']->real_escape_string($data[$i]);
	}
	switch($ptb){
    	case 'p':
			$columns[0] = '`text`';
			$values[0] = "'".$data[0]."'";
			break;
		case 'b':
			$columns[0] = '`name`';
			$columns[1] = '`groupID`';
			$values[0] = "'".$data[0]."'";
			$values[1] = "'".$data[3]."'";
			break;	
		case 't':
			$columns[0] = '`name`';
			$columns[1] = '`sticky`';
			$columns[2] = '`board_id`';
			$values[0] = "'".$data[0]."'";
			$values[1] = "'".$data[1]."'";
			$values[2] = "'".$data[2]."'";
			break;			
	}
	
	$ptb=ptbSwitch($ptb);
	if(count($columns) == 1){
		$fin_update=$columns[0].' = '.$values[0];
	} elseif(count($columns) == 2) {
		$fin_update=$columns[0].' = '.$values[0].', '.$columns[1].' = '.$values[1];
	}
	elseif(count($columns) == 3) {
		$fin_update=$columns[0].' = '.$values[0].', '.$columns[1].' = '.$values[1].', '.$columns[2].' = '.$values[2];
	}
  	$MySQL['query']="UPDATE `".$ptb[0]."` SET ".$fin_update." WHERE `id` = ".$id;
	$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['connection']->affected_rows == 1){
		echo '<meta http-equiv="refresh" content="0; url=?p='.$ptb[1].'&id='.$return.'&pag='.$pag.'" />';
	}
	include("dbdisconnect.inc.php");
}

function ptbChgForm($ptb, $id, $return, $pag){
	include("dbconnect.inc.php");
	switch($ptb){
    	case 'p':
			$columns[0] = '`text`';
			break;
		case 't':
			$columns[0] = '`name`';
			$columns[1] = '`sticky`';
			$columns[2] = '`board_id`';
			break;
		case 'b':
			$columns[0] = '`name`';
			$columns[1] = '`groupID`';
			break;
	}
	$ptbs = ptbSwitch($ptb);
	
	$i = 0;
	while($i < count($columns)){
		if($i == 0){
			$fin_columns = $columns[$i];
		} else {
			$fin_columns = $fin_columns.', '.$columns[$i];
		}
		$i++;
	}

	$MySQL['query'] = "SELECT ".$fin_columns." FROM `".$ptbs[0]."` WHERE `id` = ".$id." LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	if($MySQL['result']->num_rows !== 0){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();	
		echo "
				<form method='get'>
					<input type='hidden' name='action' value='save'>
					<input type='hidden' name='ptb' value='".$ptb."'>
					<input type='hidden' name='id' value='".$id."'>
					<input type='hidden' name='return' value='".$return."'>
					<input type='hidden' name='pag' value='".$pag."'>
					<input type='hidden' name='p' value='".$ptbs[1]."'>";
		if($ptb == 't' || $ptb == 'b'){
			echo "	<input type='text' name='data' value='".$MySQL['row'][(substr($columns[0], 1, -1))]."'>";
		} elseif($ptb == 'p'){
			echo "	<textarea rows='15' name='data'>".$MySQL['row'][(substr($fin_columns, 1, -1))]."</textarea>";
		}
		if($ptb == 'b'){
			$MySQL['result2'] = $MySQL['connection']->query("SELECT * FROM usergroups");
			echo "<select name='groupID'>";
			while($MySQL['row2'] = $MySQL['result2']->fetch_assoc()) { 
				if($MySQL['row']['groupID'] == $MySQL['row2']['id']){
					$sticky = ' selected';
				} else {
					$sticky = '';
				}
				echo "<option".$sticky." value='".$MySQL['row2']['id']."'>".$MySQL['row2']['name']."</option>";
			}
			echo "</select>";
		}
		if($ptb == 't'){
			$MySQL['result2'] = $MySQL['connection']->query("SELECT * FROM boards");
			echo "<select name='board_id'>";
			while($MySQL['row2'] = $MySQL['result2']->fetch_assoc()) { 
				echo "<option ";
				if ($MySQL['row2']['id'] == $MySQL['row']['board_id'])echo "selected ";
				echo "value='".$MySQL['row2']['id']."' >".$MySQL['row2']['name']."</option>";
			}
			echo "</select>";
		
			if($MySQL['row'][(substr($columns[1], 1, -1))]){
				$sticky = ' selected';
			} else {
				$sticky = '';
			}
			echo "<label for='sticky'>Sticky: </label><select name='sticky'><option".$sticky." value='0'>False</option><option".$sticky." value='1'>True</option>";
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
			logAction();
			ptbDelete(getIfIssetGet('ptb', ''), getIfIssetGet('id', ''), getIfIssetGet('return', ''));
			break;
		case 'new': 
			logAction();
			ptbNew(getIfIssetGet('ptb', ''), getIfIssetGet('data', ''), getIfIssetGet('return', ''), $_SESSION['forumUserID']);
			break;
		case 'save': 
			logAction();
			if($_GET['ptb']=='t'){$sticky=$_GET['sticky'];}else{$sticky=0;}
			if($_GET['ptb']=='b'){$groupID=$_GET['groupID'];}else{$groupID=1;}
			if(isset($_GET['board_id'])){$board_id = $_GET['board_id'];}else{$board_id="";}
			ptbChgSav(getIfIssetGet('ptb', ''), getIfIssetGet('id', ''), array(getIfIssetGet('data', ''),$sticky,$board_id,$groupID), getIfIssetGet('return', ''), getIfIssetGet('pag', ''));
			break;
		case 'change': 
			ptbChgForm(getIfIssetGet('ptb', ''), getIfIssetGet('id', ''), getIfIssetGet('return', ''), getIfIssetGet('pag', ''));
			break;
		case 'like':
			ptbLike(getIfIssetGet('ptb', ''), getIfIssetGet('id', ''), $_SESSION['forumUserID']);
			break;
	}
}

function showBoards(){
	include('dbconnect.inc.php');
	if (isLoggedIn()){
		$id = $_SESSION['forumUserID'];
	} else {
		$id = "";
	}
	$MySQL['query'] = "SELECT * FROM `boards`";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	
	if($MySQL['result']->num_rows > 0){
		echo "
		<table class='item-container'>";
		while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
			if(hasRights($id,$MySQL['row']['groupID'])){
				if(isset($MySQL['row']['sticky']) && $MySQL['row']['sticky']==1){$sticky='-sticky';}else {$sticky='';}
				echo "
				<tr>
					<td class='item".$sticky."' onclick='window.location.href = \"?p=".substr("boards",0,-1)."&id=".$MySQL['row']["id"]."\"'>
						".$MySQL['row']["name"]."
					</td>";
				if(isLoggedIn() && isAdmin()){
					echo "
					<td class='item-icons'>
						<p>
							<a class='hidden-a' href='?p=index&action=change&ptb=b&id=".$MySQL['row']["id"]."&return='>
								<img src='images/change.png'>
							</a>
							<a class='hidden-a' href='?p=index&action=delete&ptb=b&id=".$MySQL['row']['id']."&return='>
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
		}
		echo "
		</table>";	
	}
	if(isLoggedIn() && isAdmin()) {
		echo "
			<div class='post-area'>
				<form method='get'>
					<input type='hidden' name='action' value='new'>
					<input type='hidden' name='ptb' value='b'>
					<input type='hidden' name='return' value='board'>
					<input type='hidden' name='p' value='index'>
					<input type='text' name='data' value='New'>
					<input type='submit' name='submit' value='Make'>
				</form>
			</div>";
	}
}

function showThreads($board){
	include('dbconnect.inc.php');
	$MySQL['query'] = "SELECT * FROM `threads` WHERE board_id=".$board." ORDER BY `sticky` DESC, `id` DESC";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	
	if($MySQL['result']->num_rows > 0){
		echo "
		<table class='item-container'>";
		while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
				//if(hasRights($_SESSION['forumUserID'], $MySQL['row']['groupID'])){
				if(isset($MySQL['row']['sticky']) && $MySQL['row']['sticky']==1){$sticky='-sticky';}else {$sticky='';}
				echo "
				<tr>
					<td class='item".$sticky."' onclick='window.location.href = \"?p=".substr("threads",0,-1)."&id=".$MySQL['row']["id"]."\"'>
						".$MySQL['row']["name"]."
					</td>";
				if(isLoggedIn() && isAdmin()){
					echo "
					<td class='item-icons'>
						<p>
							<a class='hidden-a' href='?p=index&action=change&ptb=t&id=".$MySQL['row']["id"]."&return=".$board."'>
								<img src='images/change.png'>
							</a>
							<a class='hidden-a' href='?p=index&action=delete&ptb=t&id=".$MySQL['row']['id']."&return=".$board."'>
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
			//}
		} 
		echo "
		</table>";
		}
	if(isLoggedIn()) {
		echo "
			<div class='post-area'>
				<form method='get'>
					<input type='hidden' name='action' value='new'>
					<input type='hidden' name='ptb' value='t'>
					<input type='hidden' name='return' value='".$board."'>
					<input type='hidden' name='p' value='index'>
					<input type='text' name='data' value='New'>
					<input type='submit' name='submit' value='Make'>
				</form>
			</div>";
	}
}

function showPosts($thread){
	include('dbconnect.inc.php');
	if(isset($_GET['pag'])){
		$pag = $_GET['pag'];
	} else {
		$MySQL['query'] = "SELECT COUNT(*) AS `amRows` FROM `posts` WHERE `posts`.`thread_id`=".$thread."";
		$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		$amRows = $MySQL['row']['amRows'];
		$amPages = ceil($amRows / 10);
		if($amPages == 0){$amPages = 1;}
		$pag = $amPages;
	}
	$MySQL['query'] = "SELECT `posts`.`text`, `posts`.`date_created`, `users`.`firstname`, `users`.`sig`, `posts`.`id`, `posts`.`user_id`, `threads`.`name`, `threads`.`op` FROM `posts`, `users`, `threads` WHERE `threads`.`id`= ".$thread." AND `posts`.`thread_id`=".$thread." AND `users`.`id` = `posts`.`user_id` ORDER BY date_created ASC LIMIT ".(($pag-1)*10).", ".($pag*10)."";

	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));

	$i= 0;
	if($MySQL['result']->num_rows > 0){
		ptbPageLinks("p",$thread, $pag);
		while($MySQL['row'] = $MySQL['result']->fetch_assoc()) { 
			// Count the posts
			$MySQL['result2'] = $MySQL['connection']->query("SELECT COUNT(*) AS postcount FROM posts WHERE posts.user_id=".$MySQL['row']["user_id"]);
			$MySQL['row2'] = $MySQL['result2']->fetch_assoc();
			// Show Profile information
			echo "
			<table class='item-container'>
				<tr>
					<td class='userbar'>
						<p class='username'>".$MySQL['row']["firstname"]."</p>
						<p class='rank'>".getUserRank($MySQL['row']["user_id"])."</p>
						<p class='avatar'>".getUserAvatar($MySQL['row']["user_id"])."</p>
						<p class='user-info'>posts: ".$MySQL['row2']['postcount']."</p>";
			if(isLoggedIn()){
				echo "
						<p class='postbuttons'>
							<script>var text".$i." = '".$MySQL['connection']->real_escape_string($MySQL['row']["text"])."';</script>
							<a class='hidden-a' onClick='quote(\"".$MySQL['row']["firstname"]."\",text".$i.")' href='#newPost'>
								<img src='images/quote.png'>
							</a>";
			}
			if (hasSpecialRights($MySQL['row']['user_id'],$MySQL['row']['op'])) {
				echo "
							<a class='hidden-a' href='?p=thread&action=change&ptb=p&id=".$MySQL['row']['id']."&return=".$thread."&pag=".$pag."'>
								<img src='images/change.png'>
							</a>
							<a class='hidden-a' href='?p=thread&action=delete&ptb=p&id=".$MySQL['row']['id']."&return=".$thread."&pag=".$pag."'>
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
					<td class='post-content'>
						
							<p><b>".$threadtitle."</b></p>
							<p class='postedon'>".$postnr.", posted on: ".$MySQL['row']["date_created"]."
							<hr />".$MySQL['row']["text"].$sig."
						
					</td>
				</tr>
			</table>";
			$i++;
		}
		ptbPageLinks("p",$thread, $pag);
	} else {
		echo "<p>Nothing here yet.</p>";
	}
	if(isLoggedIn()){
	echo "	<div class='post-area'><form method='get' id='newPost' action=''>
				<input type='hidden' name='action' value='new'>
				<input type='hidden' name='ptb' value='p'>
				<input type='hidden' name='return' value='".$thread."'>
				<input type='hidden' name='p' value='index'>
				<textarea name='data' rows='4' cols='50'></textarea></div>
				<div class='post-area' style='height: 29px;'><input class='post-area-submit' type='submit' name='submit' value='Submit'>
			</form></div>";
	}
	
}

// DEPRECIATED
function ptbShow($ptb, $return){
	include('dbconnect.inc.php');
	$ptbs = ptbSwitch($ptb);
	if($ptb == 'b'){$MySQL['query'] = "SELECT * FROM `".$ptbs[0]."`";}
	if($ptb == 't'){$MySQL['query'] = "SELECT * FROM `".$ptbs[0]."` WHERE board_id=".$return." ORDER BY `sticky` DESC, `id` DESC";}
	if($ptb == 'p'){
		if(isset($_GET['pag'])){
			$pag = $_GET['pag'];
		} else {
			$MySQL['query'] = "SELECT COUNT(*) AS `amRows` FROM `posts` WHERE `posts`.`thread_id`=".$return."";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			$MySQL['row'] = $MySQL['result']->fetch_assoc();
			$amRows = $MySQL['row']['amRows'];
			$amPages = ceil($amRows / 10);
			if($amPages == 0){$amPages = 1;}
			$pag = $amPages;
		}
		$MySQL['query'] = "SELECT `posts`.`text`, `posts`.`date_created`, `users`.`firstname`, `users`.`sig`, `posts`.`id`, `posts`.`user_id`, `threads`.`name`, `threads`.`op` FROM `posts`, `users`, `threads` WHERE `threads`.`id`= ".$return." AND `posts`.`thread_id`=".$return." AND `users`.`id` = `posts`.`user_id` ORDER BY date_created ASC LIMIT ".(($pag-1)*10).", ".($pag*10)."";
	}
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	switch($ptb){
		case 'p':
			$i= 0;
			if($MySQL['result']->num_rows > 0){
				ptbPageLinks($ptb,$return, $pag);
				while($MySQL['row'] = $MySQL['result']->fetch_assoc()) { 
				    // Count the posts
					$MySQL['result2'] = $MySQL['connection']->query("SELECT COUNT(*) AS postcount FROM posts WHERE posts.user_id=".$MySQL['row']["user_id"]);
					$MySQL['row2'] = $MySQL['result2']->fetch_assoc();
					// Show Profile information
					echo "
					<table class='post'>
						<tr>
							<td class='userbar'>
								<p class='username'>".$MySQL['row']["firstname"]."</p>
								<p class='rank'>".getUserRank($MySQL['row']["user_id"])."</p>
								<p class='avatar'>".getUserAvatar($MySQL['row']["user_id"])."</p>
								<p class='avatar'>".$MySQL['row2']['postcount']."</p>";
					if(isLoggedIn()){
						echo "
								<p class='postbuttons'>
									<script>var text".$i." = '".$MySQL['connection']->real_escape_string($MySQL['row']["text"])."';</script>
									<a class='hidden-a' onClick='quote(\"".$MySQL['row']["firstname"]."\",text".$i.")' href='#newPost'>
										<img src='images/quote.png'>
									</a>";
					}
					if (hasSpecialRights($MySQL['row']['user_id'],$MySQL['row']['op'])) {
						echo "
									<a class='hidden-a' href='?p=thread&action=change&ptb=p&id=".$MySQL['row']['id']."&return=".$return."&pag=".$pag."'>
										<img src='images/change.png'>
									</a>
									<a class='hidden-a' href='?p=thread&action=delete&ptb=p&id=".$MySQL['row']['id']."&return=".$return."&pag=".$pag."'>
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
				ptbPageLinks($ptb,$return, $pag);
			} else {
				echo "<p>Nothing here yet.</p>";
			}
			if(isLoggedIn()){
			echo "	<form method='get' id='newPost' action=''>
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
					<div class='post-area'>
						<form method='get'>
							<input type='hidden' name='action' value='new'>
							<input type='hidden' name='ptb' value='".$ptb."'>
							<input type='hidden' name='return' value='".$return."'>
							<input type='hidden' name='p' value='index'>
							<input type='text' name='data' value='New'>
							<input type='submit' name='submit' value='Make'>
						</form>
					</div>";
			}
			break;
	}
	include('dbdisconnect.inc.php');
}

function ptbPageLinks($ptb, $return, $pag){
	include('dbconnect.inc.php');
	switch($ptb){
		case 'p':
			$MySQL['query']="SELECT COUNT(*) AS `amRows` FROM `posts` WHERE `posts`.`thread_id`=".$return."";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			$MySQL['row']=$MySQL['result']->fetch_assoc();
			$amRows=$MySQL['row']['amRows'];
			if($amRows>10){
				echo "<p class='pagination'>Page: ";
				$amPages=ceil($amRows/10);
				for($i=0;$i<$amPages;$i++){
					if($pag==($i+1)){
						echo "<a href='?p=thread&id=".$return."&pag=".($i+1)."'><b>[".($i+1)."]</b></a>&nbsp;";
					} else {
						echo "<a href='?p=thread&id=".$return."&pag=".($i+1)."'>".($i+1)."</a>&nbsp;";
					}
				}
				echo "</p>";
			}
			break;
	}
	include('dbdisconnect.inc.php');
}
?>
