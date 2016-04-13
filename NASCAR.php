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



$adatabase = new DatabaseCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"]);
$resultsxdrivers = $adatabase->complexRead(["LastName","FirstName","Suffix","Place","Name"], ["Drivers", "RaceResults", "Schedule",  "Races"], [new Condition("Drivers.ID","=","DriverID"),new Condition("ScheduleID","=","Schedule.ID"),new Condition("RaceID","=","Races.ID")]);
echo $resultsxdrivers->getHTMLTable();


echo "<form action='' method='post'>";
$s = new SchemaCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);
$tb = new GUItable();
$tb = $s->read();
echo $tb->getListBox("database");
echo "<input type='submit' value='Select DB'></form>";


////add a race result
echo "<form action='' method='post'>";
echo "<br><br>Insert a Race Result: <br><br>";
echo "Race:"; //fk for schedule 1
$raceresults = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Schedule");
$tbSchedule = $raceresults->read();
echo $tbSchedule->getListBox("sch", 0);
save("sch");

echo "<br>Driver:"; //fk for driver 1
$drivers = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Drivers");
$tbDrivers = $drivers->read();
echo $tbDrivers->getListBox("Drivers", 0);
save("Drivers");


echo "<br>Place:"; //text box
echo "<input type='text' name='place'>";
save("place");

$raceresults = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "RaceResults");
$tbRaceResults = $raceresults->read();
echo $tbRaceResults->getHTMLTable();

if (isset($_SESSION["place"])){
$raceresults->create(["ScheduleID","DriverID","Place"], [$_SESSION["sch"], $_SESSION["Drivers"], $_SESSION["place"]]);
unset($_SESSION["place"]);
}



//$q->sendQuery("INSERT INTO `dbShawn`.`RaceResults` (`ScheduleID`, `DriverID`, `Place`) VALUES ('" . $_SESSION["sch"] ."', '" . $_SESSION["Drivers"] . "', '" . $_SESSION["place"]. "');", true);

echo "<input type='submit' value='Insert Race Result'></form>";




////add a driver
echo "<form action='' method='post'>";

echo "<br>Last Name:";
echo "<input type='text' name='lname'>";save("lname");

echo "<br>Firs Name:";
echo "<input type='text' name='fname'>";save("fname");

echo "<br>Suffix:";
echo "<input type='text' name='suffix'>";save("suffix");

echo "<br>DoB:";
echo "<input type='date' name='bday'>"; save("bday");

echo "<br>City:";
echo "<input type='text' name='city'>";save("city");

echo "<br>State:";
echo "<input type='text' name='state'>";save("state");


if (isset($_SESSION["lname"])){

$drivers->create(["LastName", "FirstName", "Suffix", "BirthDate", "City", "State"],[$_SESSION["lname"],$_SESSION["fname"],$_SESSION["suffix"],$_SESSION["bday"],$_SESSION["city"],$_SESSION["state"]]);

unset($_SESSION["lname"]);
}
echo "<br><input type='submit' value='Add a Driver'></form>";
?>


</div>
</body>
</html>
