<?php
//forum Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners


if(isLoggedIn()){
	include('dbconnect.inc.php');
	$MySQL['query']="SELECT * FROM `messages` WHERE `receiverID` = '".$_SESSION['forumUserID']."' LIMIT 1";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']) or die(mysqli_error($MySQL['connection']));
	$i=0;
	while($MySQL['row']=$MySQL['result']->fetch_assoc()) {
		echo "
		<table class='post'>
			<tr>
				<td class='userbar'>
					<p class='username'>".getUsername($MySQL['row']["senderID"])."</p>
					<p class='rank'>".getUserRank($MySQL['row']["senderID"])."</p>
					<p class='avatar'>".getUserAvatar($MySQL['row']["senderID"])."</p>";
		if(isLoggedIn()){
			echo "
					<p class='postbuttons'>
						<script>var text".$i." = '".$MySQL['connection']->real_escape_string($MySQL['row']["text"])."';</script>
						<a class='hidden-a' onClick='quote(\"".getFirstName($MySQL['row']["senderID"])."\",text".$i.")' href='#newPost'>
							<img src='images/quote.png'>
						</a>";
		}
		
			echo "
						<a class='hidden-a' href='?p=thread&action=delete&ptb=p&id=".$MySQL['row']['id']."'>
							<img src='images/remove.png'>
						</a>
					</p>";
		
		/*if($i==0){
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
		}*/
		echo "
				</td>
				<td class='post-td'>
					<div class='post-content'>
						<p><b></b></p>
						<p><b></b></p>
						<p class='postedon'>".$postnr.", posted on: ".$MySQL['row']["date_created"]."
						<hr />".$MySQL['row']["text"]."
					</div>
				</td>
			</tr>
		</table>";
		$i++;
	}
	include('dbdisconnect.inc.php');
}
?>