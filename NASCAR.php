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

if ( isset($_POST[$v]) ) $_SESSION[$v] = $_POST[$v];
if ( isset($_POST[$v]) && $_POST[$v]  == "") $_SESSION[$v] = null;

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

echo "<form action='' method='post'>";
$s = new SchemaCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);
$tb = new GUItable();
$tb = $s->read();
echo $tb->getListBox("database");
echo "<input type='submit' value='Select DB'></form>";




//track type
echo "<form action='' method='post'>";
echo "<br><br>Add a Track Type:<br>";
$trackType = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "TrackTypes");

echo "<br>Name:";
echo "<input type='text' name='trackTypeName'>";save("trackTypeName");
echo "<br>Description:";
echo "<input type='text' name='trackTypeDesc'>";save("trackTypeDesc");

if (isset($_SESSION["trackTypeName"])){
$trackType->create(["Name","Description"], [$_SESSION["trackTypeName"], $_SESSION["trackTypeDesc"]]);

unset($_SESSION["trackTypeName"]);
}
echo "<br><input type='submit' value='Add'></form>";




//add track
echo "<br><br><form action='' method='post'>";
echo "Add a Track:<br>";
$track = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Tracks");

echo "<br>Name:";
echo "<input type='text' name='trackName'>";save("trackName");
echo "<br>City:";
echo "<input type='text' name='trackCity'>";save("trackCity");
echo "<br>State:";
echo "<input type='text' name='trackState'><br>";save("trackState");
echo $trackType->read()->getListBox("track_type"); save("track_type");
echo "<br>Length:";
echo "<input type='text' name='trackLength'>";save("trackLength");
echo "<br>Capacity:";
echo "<input type='text' name='trackCapacity'>";save("trackCapacity");


if (isset($_SESSION["trackName"])){
$track->create(["Name", "City", "State", "TrackType", "Length", "Capacity"],
 [$_SESSION["trackName"], $_SESSION["trackCity"], $_SESSION["trackState"], $_SESSION["track_type"], $_SESSION["trackLength"], $_SESSION["trackCapacity"]]);

unset($_SESSION["trackName"]);
}
echo "<br><input type='submit' value='Add'></form>";




//add race
echo "<form action='' method='post'>";
echo "<br><br>Add a Race:<br>";
$race = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Races");

echo "<br>Name:";
echo "<input type='text' name='raceName'><br>";save("raceName");
echo $track->read()->getListBox("track_id"); save("track_id");
echo "<br>Distance:";
echo "<input type='text' name='raceDist'>";save("raceDist");



if (isset($_SESSION["raceName"])){
$race->create(["Name", "TrackID", "Distance"],
 [$_SESSION["raceName"], $_SESSION["track_id"], $_SESSION["raceDist"]]);

unset($_SESSION["raceName"]);
}
echo "<br><input type='submit' value='Add'></form>";




//schedule
echo "<form action='' method='post'>";
echo "<br><br>Add a Schedule:<br>";
$schedule = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Schedule");

echo "<br>Year:";
echo "<input type='text' name='schYear'><br>";save("schYear");
echo $race->read()->getListBox("race_id"); save("race_id");
echo "<br>Date:";
echo "<input type='date' name='schDate'>";save("schDate");
echo "<br>Time:";
echo "<input type='time' name='schTime'>";save("schTime");



if (isset($_SESSION["schYear"])){
$schedule->create(["Year", "RaceID", "DateOfRace", "TimeOfRace"],
 [$_SESSION["schYear"], $_SESSION["race_id"], $_SESSION["schDate"], $_SESSION["schTime"]]);

unset($_SESSION["schYear"]);
}
echo "<br><input type='submit' value='Add'></form>";




//add a driver
$drivers = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Drivers");
echo "<form action='' method='post'>";

echo "<br><br><br>Last Name:";
echo "<input type='text' name='lname'>";save("lname");

echo "<br>First Name:";
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




//add a race result
echo "<form action='' method='post'>";
echo "<br><br>Insert a Race Result: <br><br>";
echo "Race:"; //fk for schedule 1
//was: $raceresults = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "Schedule");
$adatabase = new DatabaseCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"]);
echo $adatabase->complexRead(["Schedule.ID", "Year", "Name"],["Schedule","Races"],[new Condition("RaceID","=","Races.ID")])->getListBox("sch", 0);
save("sch");

echo "<br>Driver:"; //fk for driver 1
$tbDrivers = $drivers->read();
echo $tbDrivers->getListBox("Drivers", 0);
save("Drivers");


echo "<br>Place:";
echo "<input type='text' name='place'>";
save("place");

$raceresults = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], "RaceResults");
$tbRaceResults = $raceresults->read();
echo $tbRaceResults->getHTMLTable();

if (isset($_SESSION["place"])){
$raceresults->create(["ScheduleID","DriverID","Place"], [$_SESSION["sch"], $_SESSION["Drivers"], $_SESSION["place"]]);
unset($_SESSION["place"]);
}



//old: $q->sendQuery("INSERT INTO `dbShawn`.`RaceResults` (`ScheduleID`, `DriverID`, `Place`) VALUES ('" . $_SESSION["sch"] ."', '" . $_SESSION["Drivers"] . "', '" . $_SESSION["place"]. "');", true);

echo "<input type='submit' value='Insert Race Result'></form>";




$resultsxdrivers = $adatabase->complexRead(["LastName","FirstName","Suffix","Place","Name"], ["Drivers", "RaceResults", "Schedule",  "Races"], [new Condition("Drivers.ID","=","DriverID"),new Condition("ScheduleID","=","Schedule.ID"),new Condition("RaceID","=","Races.ID")]);
echo $resultsxdrivers->getHTMLTable();


?>


</div>
</body>
</html>
