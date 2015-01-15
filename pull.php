<?php
if(isset($_GET['password']) && $_GET['password'] == 'kol'){
	echo shell_exec("sh /home/daan/public_html/forum/PHPDevForum/pull.sh 2>&1");
} else {
	echo "<form method='get'><input type='password' name='password'><input type='submit' value='Pull'></form>";
}
?>