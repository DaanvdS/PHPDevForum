<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners


if(isLoggedIn()){
	include('dbconnect.inc.php');
	$MySQL['query']="SELECT * FROM `messages` WHERE `receiverID` = '".$_SESSION['forumUserID']."' OR `senderID` = '".$_SESSION['forumUserID']."' ORDER BY date_created ASC";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	$i=0;
	if($MySQL['result']->num_rows==0){ echo "No messages!"; }
	while($MySQL['row']=$MySQL['result']->fetch_assoc()) {
		echo "
		<table class='post'>
			<tr>
				<td class='userbar'>
					<p class='username'>".getUsername($MySQL['row']["senderID"])."</p>
					<p class='rank'>".getUserRank($MySQL['row']["senderID"])."</p>
					<p class='avatar'>".getUserAvatar($MySQL['row']["senderID"])."</p>";
		
		echo "
					<p class='postbuttons'>
						<script>var text".$i." = '".$MySQL['connection']->real_escape_string($MySQL['row']["text"])."';</script>
						<a class='hidden-a' onClick='quote(\"".getFirstName($MySQL['row']["senderID"])."\",text".$i.")' href='#newPost'>
							<img src='images/quote.png'>
						</a>";
		
		
		echo "
						<a class='hidden-a' href='?p=thread&action=delete&ptb=p&id=".$MySQL['row']['id']."'>
							<img src='images/remove.png'>
						</a>
					</p>";
		
		$sig=getSignature($MySQL['row']["senderID"]);
		
		echo "
				</td>
				<td class='post-td'>
					<div class='post-content'>
						<p><b></b></p>
						<p><b></b></p>
						<p class='postedon'>Sent on: ".$MySQL['row']["date_created"]."
						<hr />".$MySQL['row']["text"].$sig."
					</div>
				</td>
			</tr>
		</table>";
		$i++;
	}
	echo "
		<form method='get' id='newPost' action=''>
			<input type='hidden' name='p' value='inbox'>
			<div class='post-area'><textarea name='data' rows='4' cols='50'></textarea></div>
			<input class='post-area-submit' type='submit' name='submit' value='Submit'>
		</form>";
	include('dbdisconnect.inc.php');
}
?>