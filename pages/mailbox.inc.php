			<script language="javascript" type="text/javascript">
				function quote(author,text,authorid) {
					tinyMCE.activeEditor.execCommand('mceInsertContent', false, "<blockquote><span class='small'>" + author + ":</span>" + text + "</blockquote><br>");		
					document.getElementById('sendID').value = authorid;
				}
			</script>
<?php
//Forum, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners

if(isLoggedIn()){
	if(isset($_GET['action'])){
		logAction();
		if($_GET['action']=="sendmessage"){
			include('dbconnect.inc.php');
			$MySQL['query']="INSERT INTO `messages` (`senderID`, `receiverID`, `text`) VALUES ('".getLoggedInUser()."', '".$_GET['sendID']."', '".$_GET['data']."')";
			$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			if($MySQL['connection']->affected_rows==1){
				echo '<meta http-equiv="refresh" content="0; url=?p=mailbox" />';
			}
			include('dbdisconnect.inc.php');
		}
		if($_GET['action']=="delmessage"){
			include('dbconnect.inc.php');
			$MySQL['query']="SELECT `receiverID`, `senderID` FROM `messages` WHERE `id`=".$_GET['id']."";
			$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
			if($MySQL['result']->num_rows==0){ 
				echo "Something went wrong!"; 
			} else {
				$MySQL['row']=$MySQL['result']->fetch_assoc();
				if($MySQL['row']['receiverID']==getLoggedInUser()){
					$MySQL['query']="UPDATE `messages` SET `delbyReceiver` = '1' WHERE `id` = ".$_GET['id']."";
				} elseif($MySQL['row']['senderID']==getLoggedInUser()) {
					$MySQL['query']="UPDATE `messages` SET `delbySender` = '1' WHERE `id` = ".$_GET['id']."";
				}
				$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
				if($MySQL['connection']->affected_rows==1){
					echo '<meta http-equiv="refresh" content="0; url=?p=mailbox" />';
				}
			}
			include('dbdisconnect.inc.php');
		}
	} else {
		include('dbconnect.inc.php');
		if(isset($_GET['ip'])){
			$ip=$_GET['ip'];
		} else {
			$ip=1;
		}
		$MySQL['query']="SELECT * FROM `messages` WHERE `receiverID` = '".getLoggedInUser()."' OR `senderID` = '".getLoggedInUser()."' ORDER BY date_created DESC";
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
		$i=0;
		$j=(($ip-1)*10);
		$k=($ip*10);
		$l=0;
		if($MySQL['result']->num_rows==0){ echo "No messages!"; }
		mailboxPageLinks($ip);
		while($MySQL['row']=$MySQL['result']->fetch_assoc()) {
			if($MySQL['row']['receiverID']==getLoggedInUser()){
				$MySQL['connection']->query("UPDATE `messages` SET `unread` = '0' WHERE `id` = '".$MySQL['row']['id']."'");
			}
			if(($MySQL['row']['receiverID']==getLoggedInUser() && $MySQL['row']['delbyReceiver']==1)||($MySQL['row']['senderID']==getLoggedInUser() && $MySQL['row']['delbySender']==1)){
				//Do not show
			} else {
				if(($l>=$j)&&($l<$k)){
					echo "
				<table class='post'>
					<tr>
						<td class='userbar'>
							<p class='username'>".getFirstName($MySQL['row']["senderID"])."</p>
							<p class='rank'>".getUserRank($MySQL['row']["senderID"])."</p>
							<p class='avatar'>".getUserAvatar($MySQL['row']["senderID"])."</p>";
				if($MySQL['row']['senderID']==getLoggedInUser()){
					$authorid=$MySQL['row']["receiverID"];
				} else {
					$authorid=$MySQL['row']["senderID"];
				}
				echo "
						<p class='postbuttons'>";
				if(!$authorid==0){
						echo "
							
								<script>var text".$i." = '".$MySQL['connection']->real_escape_string($MySQL['row']["text"])."';</script>
								<a class='hidden-a' onClick='quote(\"".getFirstName($MySQL['row']["senderID"])."\",text".$i.", \"".$authorid."\")' href='#newPost'>
									<img src='images/reply.png'>
								</a>";
				}
				
				echo "
							<a class='hidden-a' href='?p=mailbox&action=delmessage&id=".$MySQL['row']['id']."'>
								<img src='images/remove.png'>
							</a>
						</p>";
				
				$sig=getSignature($MySQL['row']["senderID"]);
				if($MySQL['row']['senderID']==getLoggedInUser()){
					$sendto=", Sent to ".getFirstName($MySQL['row']['receiverID'])." ".getLastName($MySQL['row']['receiverID'])."";
				} else {
					$sendto="";
				}
				echo "
						</td>
						<td class='post-td'>
							<div class='post-content'>
								<p><b></b></p>
								<p><b></b></p>
								<p class='postedon'>Sent on: ".$MySQL['row']["date_created"].$sendto."
								<hr />".$MySQL['row']["text"].$sig."
							</div>
						</td>
					</tr>
				</table>";
				$i++;
				}
				$l++;
			}
		}
		mailboxPageLinks($ip);
		
		echo "
			<div class='post-area'><form method='get' id='newPost' action=''>
				Send to: <select id='sendID' name='sendID'>";
				
		$MySQL['query']="SELECT `id`, `firstname`, `lastname` FROM `users` ORDER BY `lastname`, `firstname` ASC";
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
		while($MySQL['row']=$MySQL['result']->fetch_assoc()) {
			if(!$MySQL['row']['id']==0){
				echo "<option value='".$MySQL['row']['id']."'>".$MySQL['row']['firstname']." ".$MySQL['row']['lastname']."</option>";
			}
		}
		
		echo "
				</select>
				<input type='hidden' name='p' value='mailbox'>
				<input type='hidden' name='action' value='sendmessage'>
				<textarea name='data' rows='4' cols='50'></textarea>
				<input class='post-area-submit' type='submit' name='submit' value='Submit'>
			</form></div>";
		include('dbdisconnect.inc.php');
	}
}
function mailboxPageLinks($ip){
	include('dbconnect.inc.php');
	$MySQL['query']="SELECT * FROM `messages` WHERE `receiverID` = '".getLoggedInUser()."' OR `senderID` = '".getLoggedInUser()."'";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	$amRows=0;
	while($MySQL['row']=$MySQL['result']->fetch_assoc()){
		if(($MySQL['row']['receiverID']==getLoggedInUser() && $MySQL['row']['delbyReceiver']==1)||($MySQL['row']['senderID']==getLoggedInUser() && $MySQL['row']['delbySender']==1)){
			//Do not show
		} else {
			$amRows++;
		}
	}
	if($amRows>10){
		echo "<p class='pagination'>Page: ";
		$amPages=ceil($amRows/10);
		for($i=0;$i<$amPages;$i++){
			if($ip==($i+1)){
				echo "<a href='?p=mailbox&ip=".($i+1)."'><b>[".($i+1)."]</b></a>&nbsp;";
			} else {
				echo "<a href='?p=mailbox&ip=".($i+1)."'>".($i+1)."</a>&nbsp;";
			}
		}
		echo "</p>";
	}
	include('dbdisconnect.inc.php');
}
?>