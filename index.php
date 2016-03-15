<?php
session_start();
?>

<html>
<head><title>Lite</title></head>

<form action="" method="post">
	<input type="text" name="server" value="xxxxx"><br>
    <input type="text" name="user" value="xxxxx"><br>
    <input type="password" name="password" value="xxxxx"><br>
    <input type="hidden" name="action" value="log_in">
    <input type="submit" value="Log In">
</form>
<?php

if($_SESSION["server"]=="")   $_SESSION["server"] = $_POST["server"];
if($_SESSION["username"]=="") $_SESSION["username"] = $_POST["user"];
if($_SESSION["password"]=="") $_SESSION["password"] = $_POST["password"];
if($_SESSION["database"]=="") $_SESSION["database"] = $_POST["list_of_databases"];



$s = new SchemaCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"]);
$tb = new GUItable();
$tb = $s->read();
echo $tb->getListBox("list_of_databases");


$d = new DatabaseCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"]);
$tb2 = new GUItable();
$tb2 = $d->read();
echo $tb2->getListBox("list_of_tables");

$t = new TableCRUD($_SESSION["server"], $_SESSION["username"], $_SESSION["password"], $_SESSION["database"], $_POST["list_of_tables"]);
$tb3 = new GUItable();
$tb3 = $t->read();
echo $tb3->getHTMLTable();

?>
</html>



<?php
class DatabaseConnection {
	private $m_server;
	private $m_username;
	private $m_password;
	private $m_conn;

	//open a connection
	public function __construct($server, $username, $password) {
		$this->m_server = $server;
		$this->m_username = $username;
		$this->m_password = $password;

		// Create connection
		$this->m_conn = new mysqli($this->m_server, $this->m_username, $this->m_password);
		
		
		// Check connection
		if ($this->m_conn->connect_error) {
			$e = new Error("Could not connect to database.");
		}

	}//end opening a connection

	//send a query
	public function sendQuery($q){

		if ($this->m_conn->query($q)) {
			//the query worked
				
		}
		else $e = new Error("Query Failed - [" . $q . "]");
	}//end sending a query

	//get a query
	public function getQuery($q){
		$table = new GUItable();

		if ($this->m_conn->query($q)) {
			$result = $this->m_conn->query($q);
			$row = $result->fetch_array(MYSQLI_NUM);
			$row_count = $result->num_rows;

			for ($i = 0; $i < $row_count; $i++){
					
				foreach ($row as $value) {$table->addItem($value);}
				$table->addItem($table->NEWLINE);
				$row = $result->fetch_array(MYSQLI_NUM);
			}

		}
		else $e = new Error("Query Failed - [" . $q . "]");
		return $table;
	}//end getting a query



}//end DBconnection class

class Error {
	private $m_error_str;
	
	function __construct($error_string) {
	$m_error_str = $error_string;
	
	echo $m_error_str;
	
	}
}//end error class

class GUItable {
	public $NEWLINE = "NEWLINE";
	private $m_tb = array();
	private $index = 0;
	
	public function addItem($item){
		
		$this->m_tb[$this->index] = $item;
		$this->index = $this->index + 1;
		
		
	}//end add item
	
	public function getHTMLTable(){
		
		$s = "<table border='1'> <tr>";
		
	for ($i=0; $i<=sizeof($this->m_tb); $i++) { 
		
		
		if ($this->m_tb[$i] != $this->NEWLINE) $s .= "<td>" . $this->m_tb[$i] . "</td>"; 
		else $s .= "</tr><tr>";
	}
		
	$s .= "</tr></table>";
		return $s;
		
	}//end get table
	
	public function getListBox($name){
		
		$s = "<form action='' method='post'><select name='" . $name ."'>";
	
		for ($i=0; $i<=sizeof($this->m_tb); $i++) {
		
		
			if ($this->m_tb[$i] != $this->NEWLINE) $s .= "<option value='" . $this->m_tb[$i] . "'>". $this->m_tb[$i] ."</option>";                
			
		}
		
		$s .= "</select><input type='submit'></form>";
		return $s;
		
	}//end get list box
	
	
}//end table class

class SchemaCRUD{

	private $m_conn;
	
//open a connection
	public function __construct($server, $username, $password) {

		
		$this->m_conn = new DatabaseConnection($server, $username, $password);
		

	}//end opening a connection
	
	public function read(){
		
		return $this->m_conn->getQuery("Show Databases;");
		
		
	}
	
	
}

class DatabaseCRUD{


	private $m_database;
	private $m_conn;

	//open a connection
	public function __construct($server, $username, $password, $database) {

		$this->m_database = $database;
		
		$this->m_conn = new DatabaseConnection($server, $username, $password);
		


	}//end opening a connection

	public function read(){

		return $this->m_conn->getQuery("show tables from " . $this->m_database . ";");


	}


}




class TableCRUD{

	private $m_database;
	private $m_conn;
	private $m_table;

	//open a connection
	public function __construct($server, $username, $password, $database, $table) {

		$this->m_database = $database;
		$this->m_table = $table;

		$this->m_conn = new DatabaseConnection($server, $username, $password);


	}//end opening a connection

	public function read(){

		return $this->m_conn->getQuery("SELECT * FROM " . $this->m_database . "." . $this->m_table . ";");


	}


}




echo "END OF FILE";




//OLD TEST CODE
// /**
//  * Created by PhpStorm.
//  * User: sdooley
//  * Date: 1/12/16
//  * Time: 6:28 PM
//  */

// //http://www.w3schools.com/php/php_mysql_connect.asp
// $servername = "localhost";
// $username = "root";
// $password = "1";
// $q = "SELECT CLASS, numGuns FROM new_schema.Classes;";

// // Create connection
// $conn = new mysqli($servername, $username, $password);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } 
// echo "Connection! \n";


// if ($conn->query($q) == TRUE) {
//     $result = $conn->query($q);
//     $row = $result->fetch_array(MYSQLI_NUM);
//     $row_count = $result->num_rows;

// for ($i = 0; $i < $row_count; $i++){
//      foreach ($row as $value) {echo $value . "| ";}
// echo "NEWLINE";	
// $row = $result->fetch_array(MYSQLI_NUM);
// }

//     echo $q . "worked";
// }
// else  echo $q . "failed.";



// $result->close();
// $conn->close();
?>