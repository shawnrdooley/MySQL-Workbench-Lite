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
  <title>NASCAR App</title></head>

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
save("server");
save("username");
save("password");
save("database");
echo "<form action='' method='post'><input type='submit' value='SEND!'>";

$s = new SchemaCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);
$tb = new GUItable();
$tb = $s->read();
echo $tb->getListBox("database");



echo "Insert a Race Result: <br>";
echo "Race:"; //fk for schedule 1
$raceresults = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Schedule");
$tbSchedule = $raceresults->read();
echo $tbSchedule->getListBox("sch", 0);
save("sch");

echo "Driver:"; //fk for driver 1
$drivers = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Drivers");
$tbDrivers = $drivers->read();
echo $tbDrivers->getListBox("Drivers", 0);
save("Drivers");


echo "Place:"; //text box
echo "<input type='text' name='place'>";
save("place");

$q = new DatabaseConnection($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);

//if ($_SESSION["place"] != 0){

$q->sendQuery("INSERT INTO `dbShawn`.`RaceResults` (`ScheduleID`, `DriverID`, `Place`) VALUES ('" . $_SESSION["sch"] ."', '" . $_SESSION["Drivers"] . "', '" . $_SESSION["place"]. "');", true);

unset($_SESSION["place"] , $_SESSION["sch"] , $_SESSION["Drivers"] );

//}

echo "</form>";
?>


</div>
</body>
</html>
