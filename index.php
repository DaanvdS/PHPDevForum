<?php
error_reporting(E_ALL);
session_start();
// Authored by Wietze Mulder and Daan van der Spek
// Not to be copied without written permission from the owners

include('pages/functions.inc.php');

$page = getIfIssetGet('p', 'index');

if(!isset($_SESSION['forumAdmin'])){
	$_SESSION['forumAdmin'] = 0;
}

include('dbconnect.inc.php');
if(isLoggedIn()){
	//Check whether the logged in account is still activated.
	$MySQL['query'] = "SELECT `activated`, `admin` FROM `users` WHERE `id` = '".$_SESSION['forumUserID']."' LIMIT 1";
	$MySQL['result'] = $MySQL['connection']->query($MySQL['query']);
	if($MySQL['result']->num_rows == 1){
		$MySQL['row'] = $MySQL['result']->fetch_assoc();
		if($MySQL['row']['activated'] == 0){
			//Log out if the account has been disabled
			session_unset(); 
			session_destroy();
			echo '<script>alert("Woops! Your account has not been activated!");</script><meta http-equiv="refresh" content="0; url=?p=login" /></script>';
			exit();
		}
		//(Re)set the admin variable
		$_SESSION['forumAdmin'] = $MySQL['row']['admin'];
	} else {
		//Log out if the account has been deleted
		session_unset(); 
		session_destroy();
		echo '<script>alert("Woops! Your account has not been found!");</script><meta http-equiv="refresh" content="0; url=?p=login" /></script>';
		exit();
	}
}

//If page file exists then get title
if(file_exists("pages/".$page.".inc.php")){
	$title = getTitle($page, getIfIssetGet('id', ''));  // Dit doet het niet. Nee hehe. Repareren :)
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
		<script>
			function resizeBreadCrumb(){
				var width = parseInt(document.getElementById('header-table').width,10) - 54;
				console.log(width);
				document.getElementById('breadcrumbP').style.width=width;
			}
		</script>
		<?php echo "<title>".$title."</title>";?>
	</head>
	<body>	
		<div id="container">
			<div id="header">
				<table id="header-table">
				<tr>
				<td class="fill">
				<a class="hidden-a" href="?p=index">
						<h1>Forum</h1>
				</a>
				</td>
				<td>
				<?php
				if(isLoggedIn()){
					include('dbconnect.inc.php');
					$MySQL['result'] = $MySQL['connection']->query("SELECT COUNT(*) AS `amountOfRows` FROM `messages` WHERE `receiverID`='".$_SESSION['forumUserID']."' AND `unread` = '1'");
					$MySQL['row'] = $MySQL['result']->fetch_assoc();
					if($MySQL['row']['amountOfRows']==0){$unreadMessages='';}else{$unreadMessages='<b>('.$MySQL['row']['amountOfRows'].')</b>';}
					echo '<a class="hidden-a" href="?p=mailbox"><img src="images/mailbox.png" />'.$unreadMessages.'</a>';
					//echo '<td><a class="hidden-a" href="?p=userpanel">'.getFirstName($_SESSION['forumUserID']).' '.getLastName($_SESSION['forumUserID']).'</a></td>';
				
					if(isAdmin()){
						echo '<a class="hidden-a" href="?p=adminpanel"><img src="images/admin.png" /></a>'; 
					}
					
					echo '<td rowspan="2" style="width: 54px;"><a class="hidden-a" href="?p=userpanel">'.getUserAvatar($_SESSION['forumUserID']).'</a></td>';
				} else {
					echo '<td><a class="hidden-a" href="?p=login&goto='.$page.'&goid='.getIfIssetGet('id', '').'">Log in</a></td>'; 
					echo '<td style="padding-left: 8px; padding-right: 6px;"><a class="hidden-a" href="?p=register">Register</a></td>'; 
				}
				?>
				</td>
				</tr>
				<tr>
				<td colspan="2">
				<?php
				include("pages/breadcrumb.inc.php");
				?>
				</td>
				</tr>
				</table>
			</div>
		
			<div id="content">
				<?php //Include content
					include("pages/".$page.".inc.php"); 
				?>
			</div>
		</div>
	</body>
</html>
