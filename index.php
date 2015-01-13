<?php
error_reporting(E_ALL);
session_start();
// Authored by Wietze Mulder and Daan van der Spek
// Not to be copied without written permission from the owners

include('pages/functions.inc.php');

$page = getStrIfIsset("page");
if($page == "notset")$page = "index";

if(!isset($_SESSION['forumAdmin'])){
	$_SESSION['forumAdmin'] = 0;
}

include('dbconnect.inc.php');
if(isLoggedIn()){
	$MySQL['query'] = "SELECT `activated`, `admin` FROM `users` WHERE `id` = '".$_SESSION['forumUserID']."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows == 1){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		if($MySQL['row']['activated'] == 0){
			session_unset(); 
			session_destroy();
			echo '<script>alert("Woops! Your account has not been activated!");</script><meta http-equiv="refresh" content="0; url=?p=login" /></script>';
			exit();
		}
		$_SESSION['forumAdmin'] = $MySQL['row']['admin'];
	} else {
		session_unset(); 
		session_destroy();
		echo '<script>alert("Woops! Your account has not been found!");</script><meta http-equiv="refresh" content="0; url=?p=login" /></script>';
		exit();
	}
}

if(file_exists("pages/".$page.".inc.php")){
	$title = getTitle($_GET['ptb'], $_GET['id'], $page);
} else {
	$title = "Forum - 404";
	$page = "404";
}
include('dbdisconnect.inc.php');
?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" /> 
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:700,400' rel='stylesheet' type='text/css' />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
		<link rel="stylesheet" type="text/css" href="styles/table.css" />
		<link rel="stylesheet" type="text/css" href="styles/ptb.css" />
		<script type="text/javascript" src="tinymce/tinymce.min.js"></script>
		<script type="text/javascript">
			tinyMCE.PluginManager.add('stylebuttons', function(editor, url) {
			  ['p', 'code', 'h1'].forEach(function(name){
			   editor.addButton("style-" + name, {
				   tooltip: "Toggle " + name,
					 text: name.toUpperCase(),
					 onClick: function() { editor.execCommand('mceToggleFormat', false, name); },
					 onPostRender: function() {
						 var self = this, setup = function() {
							 editor.formatter.formatChanged(name, function(state) {
								 self.active(state);
							 });
						 };
						 editor.formatter ? setup() : editor.on('init', setup);
					 }
				 })
			  });
			});
			tinymce.init({
				selector: "textarea",
				content_css : "styles/tinymce.css",
				menubar : false,
				toolbar: [
					"bold italic strikethrough | undo redo | style-p style-h1 style-code | bullist numlist | link image "
				],
				statusbar : false,
				plugins: "stylebuttons link image"
			});
		</script>
		<?php echo "<title>".$title."</title>";?>
	</head>
	<body>	
		<div id="container">
			<div id="header">
				<a class="hidden-a" href="?p=index">
					<div id="title">
						<h1>Forum</h1>
					</div>
				</a>
				<div id="account-info">
					<?php 
					if(isAdmin()){
						echo '<a class="hidden-a" href="?p=adminpanel">Admin panel</a>'; 
					}
					
					if(isLoggedIn()){
						include('dbconnect.inc.php');
						$MySQL['result'] = $MySQL['connection']->query("SELECT COUNT(*) AS `amountOfRows` FROM `messages` WHERE `receiverID`='".$_SESSION['forumUserID']."' AND `unread` = '1'");
						$MySQL['row']=$MySQL['result']->fetch_assoc();
						if($MySQL['row']['amountOfRows']==0){$unreadMessages='';}else{$unreadMessages='<b>('.$MySQL['row']['amountOfRows'].')</b>';}
						echo '<a class="hidden-a" href="?p=inbox">Inbox '.$unreadMessages.'</a>';
						$MySQL['result'] = $MySQL['connection']->query("SELECT `firstname`, `lastname` FROM `users` WHERE `id`='".$_SESSION['forumUserID']."' LIMIT 1");
						$MySQL['row']=$MySQL['result']->fetch_assoc();
						echo '<a class="hidden-a" href="?p=userpanel">'.$MySQL['row']['firstname'].' '.$MySQL['row']['lastname'].'</a>';
						echo '<a class="hidden-a" href="?p=userpanel">'.getUserAvatar($_SESSION['forumUserID']).'</a>';
					} else {
						if(isset($_GET['id'])){$id=$_GET['id'];}else{$id='';}
						echo '<a class="hidden-a" href="?p=login&goto='.$page.'&goid='.$id.'">Log in</a>'; 
					}
					
					if(!isLoggedIn()){ 
						echo '<a class="hidden-a" href="?p=register">Register</a>'; 
					}
					?>
				</div>
			
			</div>
			<div id="content">
				<?php include("pages/breadcrumb.inc.php"); ?>
				<?php if($page!=="404"){include("pages/".$page.".inc.php");}else{echo "404 - Page not found";} ?>
			</div>
		</div>
	</body>
</html>