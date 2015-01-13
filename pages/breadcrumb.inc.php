<?php
include("dbconnect.inc.php");
if(!isset($_GET['p'])){
	//If page is not set, the indexpage will be shown
	$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a></p></div>";
} elseif($_GET['p']=='board') {
	//Get boardname and display it
	$MySQL['query']="SELECT `name` FROM `boards` WHERE `id` = '".$_GET['id']."' LIMIT 1";
	$MySQL['result']=$MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows==1){
		$MySQL['row']=$MySQL['result']->fetch_assoc();
		$boardName=$MySQL['row']['name'];
		$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a> > <a class='hidden-a' href='?p=board&id=".$_GET['id']."'>".$boardName."</a></p></div>";
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
		$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a> > <a class='hidden-a' href='?p=board&id=".$boardID."'>".$boardName."</a> > <a class='hidden-a' href='?p=thread&id=".$id."'>".$threadName."</a></p></div>";
	}
} else {
	//Display the other pages that there are, and make the first letter a capital
	$out="<div id='breadcrumb'><p><a class='hidden-a' href='?p=index'>PHPDev Forums</a> > <a class='hidden-a' href='?p=".$_GET['p']."'>".ucwords($_GET['p'])."</a></p></div>";
}

echo $out;
include("dbdisconnect.inc.php");
?>