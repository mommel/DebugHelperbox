<?php
header('Access-Control-Allow-Origin: *'); 
error_reporting(E_ALL); 
ini_set("display_errors", 1); 
if(!isset($_GET['database']) || strlen($MYSQL_DB = $_GET['database'] )<=0 || strtolower($_GET['database'])=='nothing') {
	echo 'Database connection failed. Please select Project first!';
	die();
}
?>
<ul class="list-group">
<?
$MYSQL_HOST = "database";
$MYSQL_USER = "xxxxx";
$MYSQL_PW = "pwpwpwpw";
$MYSQL_DB = $_GET['database'];

$conn = mysql_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PW);
mysql_select_db($MYSQL_DB, $conn);


$sql  = "Select created,tracer,sql1 FROM adodb_logsql ORDER BY created DESC LIMIT 10;";
$res = mysql_query($sql);

echo date("d.m.Y h:i:s",time()).'<br />';

while($row = mysql_fetch_assoc($res)) {
	?>
    <li class="list-group-item list-group-item-success"><i><?=$row['created']?></i></li>
    <li class="list-group-item list-group-item-danger"><b><?=$row['tracer']?></b></li>
    <li class="list-group-item list-group-item-warning"><?=$row['sql1']?></li>
    <?
}
?>
</ul>