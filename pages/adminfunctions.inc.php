<?php
function adminShowUserPanel(){
	include("dbconnect.inc.php");
	if(isset($_GET['mode'])){
		logAction();
		//Saving the changes the admin has made
		if($_GET['mode'] == "deluser"){
			$MySQL['query'] = "DELETE FROM `users` WHERE `id`= ".$_GET['id']." LIMIT 1";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Deleted succesfully", "adminpanel&section=usermanagement");
		} elseif($_GET['mode'] == "setactivate"){
			$MySQL['query'] = "UPDATE `users` SET `activated` = '".toggleInt($_GET['current'])."', `activationcode` = '0' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "(De-)Activated succesfully", "adminpanel&section=usermanagement");
		} elseif($_GET['mode'] == "setadmin"){
			$MySQL['query'] = "UPDATE `users` SET `admin` = '".toggleInt($_GET['current'])."' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
			redirectIfDone($MySQL['connection'], "Admin-ed succesfully", "adminpanel&section=usermanagement");
		} elseif($_GET['mode'] == "changeuser"){
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
		
		//Git pull button (not yet working)
		
	
		$MySQL['query'] = "SELECT * FROM `users` ORDER BY `".$sort."` ".$dir."";
		$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows !== 0){
			//Displaying the adminpanel table and data
			?><table style='width: 99%;'><tr>
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
					echo "	<tr><form id='change".$MySQL['row']['id']."' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='section' value='usermanagement'><input type='hidden' name='mode' value='changeuser'><input type='hidden' name='idid' value='".$MySQL['row']['id']."'>
								<td class='right'>".$MySQL['row']['id']."</td>
								<td><input class='up' type='text' name='forumFirstName' value='".$MySQL['row']['firstname']."'></td>
								<td><input class='up' type='text' name='forumLastName' value='".$MySQL['row']['lastname']."'></td>
								<td>".$MySQL['row']['username']."</td>
								<td id='adminavatar'>".getUserAvatar($MySQL['row']['id'])."</td>
								<td><a class='up' href='?p=adminpanel&section=usermanagement&mode=setactivate&id=".$MySQL['row']['id']."&current=".$MySQL['row']['activated']."'>".intToBool($MySQL['row']['activated'])."</a></td>
								<td><a class='up' href='?p=adminpanel&section=usermanagement&mode=setadmin&id=".$MySQL['row']['id']."&current=".$MySQL['row']['admin']."'>".intToBool($MySQL['row']['admin'])."</a></td>
								<td><a class='up' href='javascript:document.forms[\"change".$MySQL['row']['id']."\"].submit();'><img src='images/change.png'></a>&nbsp;<a href='?p=adminpanel&section=usermanagement&mode=deluser&id=".$MySQL['row']['id']."'><img src='images/remove.png'></a></td>
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
?>