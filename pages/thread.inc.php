<script language="javascript" type="text/javascript">
	function quote(author,text) {
		tinyMCE.activeEditor.execCommand('mceInsertContent', false, "<blockquote><span class='small'>" + author + ":</span>" + text + "</blockquote><br>");
	}
</script>
<?php
	include('dbconnect.inc.php');
	$id=getID();
	if(isset($_GET['action'])){
		ptbAction();
	} else {
		ptbShow("p", $id);
	}
	include('dbdisconnect.inc.php');
?>