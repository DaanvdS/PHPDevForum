<?php
function adminShowUserPanel(){
	include("dbconnect.inc.php");
	if(isset($_GET['action'])){
		logAction();
		//Saving the changes the admin has made
		if($_GET['action'] == "deluser"){
			$MySQL['query'] = "DELETE FROM `users` WHERE `id`= ".$_GET['id']." LIMIT 1";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Deleted succesfully", "adminpanel&section=usermanagement");
		} elseif($_GET['action'] == "setactivate"){
			$MySQL['query'] = "UPDATE `users` SET `activated` = '".toggleInt($_GET['current'])."', `activationcode` = '0' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "(De-)Activated succesfully", "adminpanel&section=usermanagement");
		} elseif($_GET['action'] == "setadmin"){
			$MySQL['query'] = "UPDATE `users` SET `admin` = '".toggleInt($_GET['current'])."' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Admin-ed succesfully", "adminpanel&section=usermanagement");
		} elseif($_GET['action'] == "changeuser"){
			$MySQL['query'] = "UPDATE `users` SET `firstname` = '".$_GET['forumFirstName']."', `lastname` = '".$_GET['forumLastName']."' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Changed succesfully", "adminpanel&section=usermanagement");
		}
	} else {
		//Set the direct of sorting the colomns.
		$sort = getIfIssetPost('sort', 'id');
		if(isset($_POST['dir'])){
			$dir = $_POST['dir'];
			if($dir == "ASC")$adir = "DESC";
			if($dir == "DESC")$adir = "ASC";
		} else {
			$dir = "ASC";
			$adir = "DESC";
		}

		$MySQL['query'] = "SELECT * FROM `users` ORDER BY `".$sort."` ".$dir."";
		$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows !== 0){
			//Displaying the adminpanel table and data
			?><h1>User management</h1><table style='width: 99%;'><tr>
				<th><form id='id' method='post'><input type='hidden' name='dir' value='<?php if($sort=="id"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='id'></form><a href='javascript:document.forms["id"].submit();'><?php if($sort=="id"){ echo "<b>"; } ?>ID<?php if($sort=="id"){ echo "</b>"; } ?></a></th>
				<th><form id='firstname' method='post'><input type='hidden' name='dir' value='<?php if($sort=="firstname"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='firstname'></form><a href='javascript:document.forms["firstname"].submit();'><?php if($sort=="firstname"){ echo "<b>"; } ?>First name<?php if($sort=="firstname"){ echo "</b>"; } ?></a></th>
				<th><form id='lastname' method='post'><input type='hidden' name='dir' value='<?php if($sort=="lastname"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='lastname'></form><a href='javascript:document.forms["lastname"].submit();'><?php if($sort=="lastname"){ echo "<b>"; } ?>Last name<?php if($sort=="lastname"){ echo "</b>"; } ?></a></th>
				<th><form id='username' method='post'><input type='hidden' name='dir' value='<?php if($sort=="username"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='username'></form><a href='javascript:document.forms["username"].submit();'><?php if($sort=="username"){ echo "<b>"; } ?>Username<?php if($sort=="username"){ echo "</b>"; } ?></a></th>
				<th><form id='avatar' method='post'><input type='hidden' name='dir' value='<?php if($sort=="avatar"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='avatar'></form><a href='javascript:document.forms["avatar"].submit();'><?php if($sort=="avatar"){ echo "<b>"; } ?>Avatar<?php if($sort=="avatar"){ echo "</b>"; } ?></a></th>
				<th><form id='activated' method='post'><input type='hidden' name='dir' value='<?php if($sort=="activated"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='activated'></form><a href='javascript:document.forms["activated"].submit();'><?php if($sort=="activated"){ echo "<b>"; } ?>Activated<?php if($sort=="activated"){ echo "</b>"; } ?></a></th>
				<th><form id='admin' method='post'><input type='hidden' name='dir' value='<?php if($sort=="admin"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='sort' value='admin'></form><a href='javascript:document.forms["admin"].submit();'><?php if($sort=="admin"){ echo "<b>"; } ?>Admin<?php if($sort=="admin"){ echo "</b>"; } ?></a></th>
				<th></th>
				</tr>
			<?php
			while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
				if(!$MySQL['row']['id'] == 0){
					echo "	<tr><form id='change".$MySQL['row']['id']."' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='action' value='changeuser'><input type='hidden' name='id' value='".$MySQL['row']['id']."'>
								<td class='right'>".$MySQL['row']['id']."</td>
								<td><input class='up' type='text' name='forumFirstName' value='".$MySQL['row']['firstname']."'></td>
								<td><input class='up' type='text' name='forumLastName' value='".$MySQL['row']['lastname']."'></td>
								<td>".$MySQL['row']['username']."</td>
								<td id='adminavatar'>".getUserAvatar($MySQL['row']['id'])."</td>
								<td><a class='up' href='?p=adminpanel&section=usermanagement&action=setactivate&id=".$MySQL['row']['id']."&current=".$MySQL['row']['activated']."'>".intToBool($MySQL['row']['activated'])."</a></td>
								<td><a class='up' href='?p=adminpanel&section=usermanagement&action=setadmin&id=".$MySQL['row']['id']."&current=".$MySQL['row']['admin']."'>".intToBool($MySQL['row']['admin'])."</a></td>
								<td><a class='up' href='javascript:document.forms[\"change".$MySQL['row']['id']."\"].submit();'><img src='images/change.png'></a>&nbsp;<a href='?p=adminpanel&section=usermanagement&action=deluser&id=".$MySQL['row']['id']."'><img src='images/remove.png'></a></td>
							</form></tr>";
				}
			}
			echo "</table>";
		} else {
			echo "<p>No users found.</p>";
		}
	}
	include("dbdisconnect.inc.php");
}

function adminShowGroupPanel(){
	include("dbconnect.inc.php");
	if(isset($_GET['action'])){
		logAction();
		//Saving the changes the admin has made
		if($_GET['action'] == "delgroup"){
			$MySQL['query'] = "DELETE FROM `usergroups` WHERE `id`= ".$_GET['id']." LIMIT 1";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Deleted succesfully", "adminpanel&section=groupmanagement");
		} elseif($_GET['action'] == "changegroup"){
			$MySQL['query'] = "UPDATE `usergroups` SET `name` = '".$_GET['name']."' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Changed succesfully", "adminpanel&section=groupmanagement");
		} elseif($_GET['action'] == "new"){
			$MySQL['query'] = "INSERT INTO `usergroups` (`name`) VALUES ('".getIfIssetGet('name', '')."')";
			$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			redirectIfDone($MySQL['connection'], "Added succesfully", "adminpanel&section=groupmanagement");
		} elseif($_GET['action'] == "assignUsersSave"){
			$i=0;
			$j=0;
			while($i < $_GET['i']){
				$MySQL['query'] = "SELECT * FROM `usersInGroups` WHERE `userID` = '".$_GET['id'.$i]."' AND `groupID` = '".$_GET['groupid']."' LIMIT 1";
				$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
				if(!$MySQL['result']->num_rows == getIfIssetGet('assign'.$i, '0')){
					if(getIfIssetGet('assign'.$i, '0') == 0){
						$MySQL['query'] = "DELETE FROM `usersInGroups` WHERE `userID` = ".$_GET['id'.$i]." AND `groupID` = ".$_GET['groupid']."";
					} elseif(getIfIssetGet('assign'.$i, '0') == 1){
						$MySQL['query'] = "INSERT INTO `usersInGroups` (`userID`, `groupID`) VALUES ('".$_GET['id'.$i]."', '".$_GET['groupid']."')";
					}
					$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
					if($MySQL['connection']->affected_rows == 1){
						$j++;
					}
					//redirectIfDone($MySQL['connection'], "(De-)assigned succesfully", "adminpanel&section=groupmanagement&action=assignUsersFrm&id=".$_GET['groupid']."&name=".$_GET['name']);
				} else {
					$j++;
				}	
				$i++;
			}
			//if($j == $_GET['i'])echo '<meta http-equiv="refresh" content="0; url=?p=adminpanel&section=groupmanagement&action=assignUsersFrm&id='.$_GET["groupid"].'&name='.$_GET["name"].'">';
		} elseif($_GET['action'] == "assignUsersFrm"){
			//Set the direct of sorting the colomns.
			$sort = getIfIssetPost('sort', 'id');
			if(isset($_POST['dir'])){
				$dir = $_POST['dir'];
				if($dir == "ASC")$adir = "DESC";
				if($dir == "DESC")$adir = "ASC";
			} else {
				$dir = "ASC";
				$adir = "DESC";
			}		
		
			$MySQL['query'] = "SELECT * FROM `users` ORDER BY `".$sort."` ".$dir."";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			if($MySQL['result']->num_rows !== 0){
				//Displaying the adminpanel table and data
				?><h1>Assign users to <?php echo $_GET['name']; ?></h1><table><tr>
					<th><form id='id' method='post'><input type='hidden' name='dir' value='<?php if($sort=="id"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='action' value='assignUsersFrm'><input type='hidden' name='sort' value='id'></form><a href='javascript:document.forms["id"].submit();'><?php if($sort=="id"){ echo "<b>"; } ?>ID<?php if($sort=="id"){ echo "</b>"; } ?></a></th>
					<th><form id='firstname' method='post'><input type='hidden' name='dir' value='<?php if($sort=="firstname"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='action' value='assignUsersFrm'><input type='hidden' name='sort' value='firstname'></form><a href='javascript:document.forms["firstname"].submit();'><?php if($sort=="firstname"){ echo "<b>"; } ?>First name<?php if($sort=="firstname"){ echo "</b>"; } ?></a></th>
					<th><form id='lastname' method='post'><input type='hidden' name='dir' value='<?php if($sort=="lastname"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='action' value='assignUsersFrm'><input type='hidden' name='sort' value='lastname'></form><a href='javascript:document.forms["lastname"].submit();'><?php if($sort=="lastname"){ echo "<b>"; } ?>Last name<?php if($sort=="lastname"){ echo "</b>"; } ?></a></th>
					<th><form id='username' method='post'><input type='hidden' name='dir' value='<?php if($sort=="username"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='action' value='assignUsersFrm'><input type='hidden' name='sort' value='username'></form><a href='javascript:document.forms["username"].submit();'><?php if($sort=="username"){ echo "<b>"; } ?>Username<?php if($sort=="username"){ echo "</b>"; } ?></a></th>
					<th></th>
					</tr><form id='change' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='action' value='assignUsersSave'><input type='hidden' name='groupid' value='<?php echo $_GET['id']; ?>'><input type='hidden' name='name' value='<?php echo $_GET['name']; ?>'>
				<?php
				$i=0;
				while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
					$assigned = '';
					$MySQL['query2'] = "SELECT * FROM `usersInGroups` WHERE `userID` = '".$MySQL['row']['id']."' AND `groupID` = '".$_GET['id']."' LIMIT 1";
					$MySQL['result2'] = $MySQL['connection']->query($MySQL['query2']);
					if($MySQL['result2']->num_rows == 1){
						$assigned = ' checked';
					}
					if(!$MySQL['row']['id'] == 0){
						echo "	<tr>
								<td class='right'>".$MySQL['row']['id']."</td>
								<td><p>".$MySQL['row']['firstname']."</p></td>
								<td><p>".$MySQL['row']['lastname']."</p></td>
								<td>".$MySQL['row']['username']."</td>
								<td><input type='hidden' name='id".$i."' value='".$MySQL['row']['id']."'><input type='checkbox' name='assign".$i."' value='1'".$assigned."></td>
							</tr>";
						//&nbsp;<a class='up' href='javascript:document.forms[\"change".$MySQL['row']['id']."\"].submit();'><img src='images/change.png'></a>
						$i++;
					}
				}
				echo "<tr><td colspan='5'><input type='hidden' name='i' value='".$i."'><input type='submit' value='Save'></td></tr></form></table>";
			} else {
				echo "<p>No groups found.</p>";
			}
		}
	} else {
		//Set the direct of sorting the colomns.
		$sort = getIfIssetPost('sort', 'id');
		if(isset($_POST['dir'])){
			$dir = $_POST['dir'];
			if($dir == "ASC")$adir = "DESC";
			if($dir == "DESC")$adir = "ASC";
		} else {
			$dir = "ASC";
			$adir = "DESC";
		}		
	
		$MySQL['query'] = "SELECT * FROM `usergroups` ORDER BY `".$sort."` ".$dir."";
		$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows !== 0){
			//Displaying the adminpanel table and data
			?><h1>Group management</h1><table><tr>
				<th><form id='id' method='post'><input type='hidden' name='dir' value='<?php if($sort=="id"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='sort' value='id'></form><a href='javascript:document.forms["id"].submit();'><?php if($sort=="id"){ echo "<b>"; } ?>ID<?php if($sort=="id"){ echo "</b>"; } ?></a></th>
				<th><form id='name' method='post'><input type='hidden' name='dir' value='<?php if($sort=="name"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='sort' value='name'></form><a href='javascript:document.forms["name"].submit();'><?php if($sort=="name"){ echo "<b>"; } ?>Name<?php if($sort=="name"){ echo "</b>"; } ?></a></th><th></th>
				<th></th>
				</tr>
			<?php
			while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
				if(!$MySQL['row']['id'] == 0){
					echo "	<tr><form id='change".$MySQL['row']['id']."' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='section' value='groupmanagement'><input type='hidden' name='action' value='changegroup'><input type='hidden' name='id' value='".$MySQL['row']['id']."'>
								<td class='right'>".$MySQL['row']['id']."</td>
								<td><input class='up' type='text' name='name' value='".$MySQL['row']['name']."'></td>
								<td><a href='?p=adminpanel&section=groupmanagement&action=assignUsersFrm&id=".$MySQL['row']['id']."&name=".$MySQL['row']['name']."'>Assign users</a></td>
								<td><a class='up' href='javascript:document.forms[\"change".$MySQL['row']['id']."\"].submit();'><img src='images/change.png'></a>&nbsp;<a href='?p=adminpanel&section=usermanagement&action=deluser&id=".$MySQL['row']['id']."'><img src='images/remove.png'></a></td>
							</form></tr>";
				}
			}
			echo "</table>";
		} else {
			echo "<p>No groups found.</p>";
		}
		echo "
					<div class='newPtb'>
						<form method='get'>
							<input type='hidden' name='action' value='new'>
							<input type='hidden' name='section' value='groupmanagement'>
							<input type='hidden' name='p' value='adminpanel'>
							<input type='text' name='name' value='New'>
							<input type='submit' name='submit' value='Make'>
						</form>
					</div>";
	}
	include("dbdisconnect.inc.php");
}
?>
