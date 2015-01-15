<?php
//forum Informatica, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners

if(isLoggedIn() && isAdmin()){
	include("dbconnect.inc.php");
	if(isset($_GET['mode'])){
		if($_GET['mode']=="deluser"){
			$MySQL['query']="DELETE FROM `users` WHERE `id`= ".$_GET['id']." LIMIT 1";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
			if($MySQL['connection']->affected_rows==1){
				echo '<script>alert("Deleted succesfully");</script><meta http-equiv="refresh" content="0; url=?p=adminpanel" />';
			} else {
				echo "Something went wrong: <a href='?p=adminpanel'>Return</a>";
			}
		} elseif($_GET['mode']=="setactivate"){
			$MySQL['query']="UPDATE `users` SET `activated` = '".toggleInt($_GET['current'])."', `activationcode` = '0' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
			echo '<meta http-equiv="refresh" content="0; url=?p=adminpanel" />';
		} elseif($_GET['mode']=="setadmin"){
			$MySQL['query']="UPDATE `users` SET `admin` = '".toggleInt($_GET['current'])."' WHERE `id` = '".$_GET['id']."'";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
			echo '<meta http-equiv="refresh" content="0; url=?p=adminpanel" />';
		} elseif($_GET['mode']=="changeuser"){
			$MySQL['query']="UPDATE `users` SET `firstname` = '".$_GET['forumFirstName']."', `lastname` = '".$_GET['forumLastName']."' WHERE `id` = '".$_GET['forumID']."'";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
			echo '<meta http-equiv="refresh" content="0; url=?p=adminpanel" />';
		}
	} else {
		
		if(isset($_POST['sort'])){
			$sort = $_POST['sort'];
		} else {
			$sort = "id";
		}
		
		if(isset($_POST['dir'])){
			$dir = $_POST['dir'];
			if($dir=="ASC"){$adir = "DESC";}
			elseif($dir=="DESC"){$adir = "ASC";}
		} else {
			$dir = "ASC";
			$adir = "DESC";
		}
	
		$MySQL['query']="SELECT * FROM `users` ORDER BY `".$sort."` ".$dir."";
		$MySQL['result']= $MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows!==0){
			?><table style='width: 99%;'><tr>
				<th><form id='id' method='post'><input type='hidden' name='dir' value='<?php if($sort=="id"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='id'></form><a href='javascript:document.forms["id"].submit();'><?php if($sort=="id"){ echo "<b>"; } ?>ID<?php if($sort=="id"){ echo "</b>"; } ?></a></th>
				<th><form id='firstname' method='post'><input type='hidden' name='dir' value='<?php if($sort=="firstname"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='firstname'></form><a href='javascript:document.forms["firstname"].submit();'><?php if($sort=="firstname"){ echo "<b>"; } ?>First name<?php if($sort=="firstname"){ echo "</b>"; } ?></a></th>
				<th><form id='lastname' method='post'><input type='hidden' name='dir' value='<?php if($sort=="lastname"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='lastname'></form><a href='javascript:document.forms["lastname"].submit();'><?php if($sort=="lastname"){ echo "<b>"; } ?>Last name<?php if($sort=="lastname"){ echo "</b>"; } ?></a></th>
				<th><form id='username' method='post'><input type='hidden' name='dir' value='<?php if($sort=="username"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='username'></form><a href='javascript:document.forms["username"].submit();'><?php if($sort=="username"){ echo "<b>"; } ?>Username<?php if($sort=="username"){ echo "</b>"; } ?></a></th>
				<th><form id='avatar' method='post'><input type='hidden' name='dir' value='<?php if($sort=="avatar"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='avatar'></form><a href='javascript:document.forms["avatar"].submit();'><?php if($sort=="avatar"){ echo "<b>"; } ?>Avatar<?php if($sort=="avatar"){ echo "</b>"; } ?></a></th>
				<th><form id='activated' method='post'><input type='hidden' name='dir' value='<?php if($sort=="activated"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='activated'></form><a href='javascript:document.forms["activated"].submit();'><?php if($sort=="activated"){ echo "<b>"; } ?>Activated<?php if($sort=="activated"){ echo "</b>"; } ?></a></th>
				<th><form id='admin' method='post'><input type='hidden' name='dir' value='<?php if($sort=="admin"){ echo $adir; } else { echo "ASC"; }?>'><input type='hidden' name='sort' value='admin'></form><a href='javascript:document.forms["admin"].submit();'><?php if($sort=="admin"){ echo "<b>"; } ?>Admin<?php if($sort=="admin"){ echo "</b>"; } ?></a></th>
				<th></th>
				</tr>
			<?php
			while($MySQL['row'] = $MySQL['result']->fetch_assoc()) {
				if(!$MySQL['row']['id']==0){
					echo "	<tr><form id='change".$MySQL['row']['id']."' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='mode' value='changeuser'><input type='hidden' name='forumID' value='".$MySQL['row']['id']."'>
								<td class='right'>".$MySQL['row']['id']."</td>
								<td><input class='up' type='text' name='forumFirstName' value='".$MySQL['row']['firstname']."'></td>
								<td><input class='up' type='text' name='forumLastName' value='".$MySQL['row']['lastname']."'></td>
								<td>".$MySQL['row']['username']."</td>
								<td id='adminavatar'>".getUserAvatar($MySQL['row']['id'])."</td>
								<td><a class='up' href='?p=adminpanel&mode=setactivate&id=".$MySQL['row']['id']."&current=".$MySQL['row']['activated']."'>".intToBool($MySQL['row']['activated'])."</a></td>
								<td><a class='up' href='?p=adminpanel&mode=setadmin&id=".$MySQL['row']['id']."&current=".$MySQL['row']['admin']."'>".intToBool($MySQL['row']['admin'])."</a></td>
								<td><a class='up' href='javascript:document.forms[\"change".$MySQL['row']['id']."\"].submit();'><img src='images/change.png'></a>&nbsp;<a href='?p=adminpanel&mode=deluser&id=".$MySQL['row']['id']."'><img src='images/remove.png'></a></td>
							</form></tr>";
				}
			}
			echo "</table>";
		} else {
			echo "<p>No users found.</p>";
		}
=======
?>
<?php
logAction();
if(isLoggedIn() && isAdmin()){
	include("pages/adminfunctions.inc.php");
	switch(getIfIssetGet('section', '')){
    	case '': echo "<p>Section: <a href='?p=adminpanel&section=usermanagement'>User management</a> <a href='?p=adminpanel&section=groupmanagement'>Group management</a> <a href='?p=adminpanel&section=pull'>Git pull</a></p>"; break;
		case 'usermanagement': adminShowUserPanel(); break;
		case 'groupmanagement': adminShowGroupPanel(); break;
		case 'pull': 
			include("dbconnect.inc.php");
			$outcome=shell_exec("sh /home/daan/public_html/forum/PHPDevForum/pull.sh 2>&1");
			echo '<script>alert("Git: '.$MySQL['connection']->escape_string($outcome).'");</script><meta http-equiv="refresh" content="0; url=?p=adminpanel" />';
			include("dbdisconnect.inc.php");
			break;
>>>>>>> e3049ec1ef610f659fcba213d672328436a9073c
	}
} else {
	echo "<p>You are not logged in/do not have the required rights.</p>";
}
//echo "<form id='gitpull' method='get'><input type='hidden' name='p' value='adminpanel'><input type='hidden' name='mode' value='gitpull'><input type='submit' value='Git pull' /></form>";
?>
