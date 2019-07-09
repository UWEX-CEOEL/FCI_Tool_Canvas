<?php
echo "Hello World";
echo "<br />";

global $CFG;

$usernameDB = $CFG->dbuser;
echo $usernameDB;
echo "!";
?>
