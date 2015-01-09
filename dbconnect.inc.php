<?php
//PWS Muziektheater, authored by Wietze Mulder and Daan van der Spek
//Not to be copied without written permission from the owners

$MySQL['hostname'] = "localhost";
$MySQL['username'] = "phpdev";
$MySQL['password'] = "ez9dbSQKrDpzBqPm";
$MySQL['database'] = "phpdev";

$MySQL['connection'] = new mysqli($MySQL['hostname'], $MySQL['username'], $MySQL['password'], $MySQL['database']);

if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}
?>