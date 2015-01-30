<?php
include("dbconnect.inc.php");
$out = "";
if(isset($_GET['p'])){
	if($_GET['p']=='board') {
		//Get boardname and display it
		$MySQL['query']="SELECT `name` FROM `boards` WHERE `id` = '".$_GET['id']."' LIMIT 1";
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows==1){
			$MySQL['row']=$MySQL['result']->fetch_assoc();
			$boardName=$MySQL['row']['name'];
			$out.="<a class='hidden-a' href='?p=board&id=".$_GET['id']."'>".$boardName."</a>";
		} else {
			$out="Unknown";
		}
	} elseif($_GET['p']=='thread') {
		//Get threadname and display it
		if(isset($_GET['ptb'])&&($_GET['ptb']=='p')){$id=$_GET['return'];}else{$id=$_GET['id'];}
		$MySQL['query']="SELECT `boards`.`id`, `boards`.`name` AS `boardname`, `threads`.`name` AS `threadname` FROM `boards`, `threads` WHERE `threads`.`id` = '".$id."' AND `boards`.`id` = `threads`.`board_id` LIMIT 1";
		$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
		if($MySQL['result']->num_rows==1){
			$MySQL['row']=$MySQL['result']->fetch_assoc();
			$boardID=$MySQL['row']['id'];
			$boardName=$MySQL['row']['boardname'];
			$threadName=$MySQL['row']['threadname'];
			$out.="<a class='hidden-a' href='?p=board&id=".$boardID."'>".$boardName."</a> > <a class='hidden-a' href='?p=thread&id=".$id."'>".$threadName."</a>";
		} else {
			$out="Unknown";
		}
	} else {
		//Display the other pages that there are, and make the first letter a capital
		$out.="<a class='hidden-a' href='?p=".$_GET['p']."'>".ucwords($_GET['p'])."</a>";
	}
}else {
	$out = "<a class='hidden-a' href='?p=index'>Index</a>";
}
echo "<p id='breadcrumbP' style='overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>".$out."</p>";
include("dbdisconnect.inc.php");
?>
