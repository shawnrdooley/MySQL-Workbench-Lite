<?php
include "creds.php";
include "CRUD.php";
//creds.php looks like:
//<?php
// $server = "not_your_server";
// $username = "not_your_username";
// $password = "not_your_password";


error_reporting(0);
session_start();

function save($v){
//sanitize here?

if ( isset($_POST[$v])) $_SESSION[$v] = $_POST[$v];

}
?>

<html>
<head>
  <!--<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  -->
  <title>Lite</title></head>

<body style=" background: linear-gradient(to right, lightskyblue 10%, antiquewhite, lightskyblue 90%); text-align: center;">
<div style="background-color: mintcream; width:70%; margin:auto; border-radius: 25px; " >
   
   
<form action="" method="post">
<input type="text" name="server" value="<?php echo $server?>"><br>
    <input type="text" name="username" value="<?php echo $username?>"><br>
    <input type="password" name="password" value="<?php echo $password?>"><br>
    <input type="hidden" name="action" value="log_in">
    <input type="submit" value="Log In">
</form>
<?php


echo "<form action='' method='post'><input type='submit' value='big submit'>";

save("server");
save("username");
save("password");
save("database");
save("tables");
save("tables2");
save("condition1");
save("condition2");
save("attributes");
save("attributes2");
save("run_query");


$s = new SchemaCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);
$tb = new GUItable();
$tb = $s->read();
echo $tb->getListBox("database");




$d = new DatabaseCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"]);
$tb2 = new GUItable();
$tb2 = $d->read();
echo $tb2->getListBox("tables");

$tbt = new GUItable();
$tbt = $d->read();
echo $tbt->getListBox("tables2");



$t = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], $_SESSION["tables"]);
$tb3 = new GUItable();
$tb3 = $t->read();
echo $tb3->getHTMLTable();

echo $tb3->getListBox("FKtest", 1);
save("FKtest"); echo $_SESSION["FKtest"];

$t2 = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], $_SESSION["tables2"]);
$tbt3 = new GUItable();
$tbt3 = $t2->read();
echo $tbt3->getHTMLTable();

$tb_attrib = new GUItable();
$tb_attrib = $t->getAttributes();
echo $tb_attrib ->getListBoxMultiple("attributes");

$tb2_attrib = new GUItable();
$tb2_attrib = $t2->getAttributes();
echo $tb2_attrib ->getListBoxMultiple("attributes2");

echo "<br><br>Conditions:";
$tb_attrib = new GUItable();
$tb_attrib = $t->getAttributes();
echo $tb_attrib ->getListBoxMultiple("condition1");
$tb2_attrib = new GUItable();
$tb2_attrib = $t2->getAttributes();
echo $tb2_attrib ->getListBoxMultiple("condition2");

$a = new Condition($_SESSION["tables"] . "." . $_SESSION["condition1"][0],"=",$_SESSION["tables2"] . "." . $_SESSION["condition2"][0]);

$tb_complex = new GUItable();
$tb_complex = $d->complexRead(array_merge($_SESSION["attributes"], $_SESSION["attributes2"]), [$_SESSION["tables"], $_SESSION["tables2"]], [$a]);
echo $tb_complex->getHTMLTable();


$query_input = new GUItable();
echo $query_input->getTextArea(10, 50, "run_query");

$q = new DatabaseConnection($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);
$tb4 = new GUItable();
$tb4 = $q->getQuery($_SESSION["run_query"], true);
echo $tb4->getHTMLTable();



echo "</form>";
?>


</div>
</body>
</html>
