<?php

class Condition {
public $m_left;
public $m_right;
public $m_operator;
private $accepted_operators = array ("=","<",">", ">=","<=");
public function __construct($left, $operator, $right) {
   
    $this->m_left = $left;
    $this->m_right = $right;
   
    if (in_array($operator, $this->accepted_operators))
    $this->m_operator = $operator;
    else {
    $this->m_operator = NULL;
   
    $e = new Error("Operator [" . $operator . "] not allowed - will be set to null.", true);
   
    }
}//end constructor


} //end condition class


class DatabaseConnection {
    //sanitize here?
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
public function sendQuery($q, $displayError = false){

if ($this->m_conn->query($q)) {
//the query worked

}
else $e = new Error("Query Failed - [" . $q . "] - [" . $this->m_conn->error . "]", $displayError);
}//end sending a query

//get a query
public function getQuery($q, $displayError = false){
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
else $e = new Error("Query Failed - [" . $q . "] - [" . $this->m_conn->error . "]", $displayError);
return $table;
}//end getting a query



}//end DBconnection class

class Error {
private $m_error_str;

function __construct($error_string, $echo = false) {
$m_error_str = $error_string;

if ($echo) echo "<div style='background-color: #737CA1; border-radius: 25px; ' >" . $m_error_str . "</div>";

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

//$s = "<br><br><button type='button' class='btn btn-info' data-toggle='collapse' data-target='#" . $name ."'>Show/Hide</button><div id='" . $name ."' class='collapse'><table border='1'> <tr>";

	$s = "<table border='1'><tr>";
	
	
for ($i=0; $i<=sizeof($this->m_tb); $i++) {


if ($this->m_tb[$i] != $this->NEWLINE) $s .= "<td>" . $this->m_tb[$i] . "</td>";
else $s .= "</tr><tr>";
}

$s .= "</tr></table>"; //</div>
return $s;

}//end get table

public function getListBox($name, $foreignKey = 0){

////////$s = "<form action='' method='post'><select name='" . $name ."'>";
$s = "<select name='" . $name ."'>";

$s .= "<option value='" . $this->m_tb[0 + $foreignKey] . "'>" . $this->m_tb[0];

for ($i=1; $i<=sizeof($this->m_tb); $i++) {

if ($this->m_tb[$i] != $this->NEWLINE) $s .= " | " . $this->m_tb[$i];

else {

$i = $i + 1;
$s .= "</option>" . "<option value='" . $this->m_tb[$i + $foreignKey] . "'>" . $this->m_tb[$i];
}
             

}

///////$s .= "</select><input type='submit'></form>";
$s .= "</select>";
return $s;

}//end get list box

public function getListBoxMultiple($name){

$s = "<select name='" . $name ."[]' multiple>";

$s .= "<option value='" . $this->m_tb[0] . "'>" . $this->m_tb[0];

for ($i=1; $i<=sizeof($this->m_tb); $i++) {

if ($this->m_tb[$i] != $this->NEWLINE) $s .= " | " . $this->m_tb[$i];

else {

$i = $i + 1;
$s .= "</option>" . "<option value='" . $this->m_tb[$i] . "'>" . $this->m_tb[$i];
}
             

}

$s .= "</select>";
return $s;

}//end get list box multiple

public function getTextArea($rows, $cols, $name){

return "<textarea rows='" . $rows ."' cols='" . $cols ."' name='" . $name . "'></textarea>"
. "</form>";

}//end getTextArea


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

public function complexRead($attributes, $tables, $conditions){

$s = "SELECT ";

for ($i = 0; $i <= sizeOf($attributes)-2; $i = $i + 1){
$s .= $attributes[$i] . ",";
}
$s .= $attributes[sizeOf($attributes) - 1];

$s .= " FROM ";

for ($i = 0; $i <= sizeOf($tables)-2; $i = $i + 1){
$s .= $this->m_database . "." . $tables[$i] . ",";
}
$s .= $this->m_database . "." .  $tables[sizeOf($tables) - 1];

$s .= " WHERE ";

$s .= $conditions[0]->m_left . $conditions[0]->m_operator . $conditions[0]->m_right;

for ($i = 1; $i <= sizeOf($conditions)-1; $i = $i + 1){
    $s .= " AND " . $conditions[$i]->m_left . $conditions[$i]->m_operator . $conditions[$i]->m_right;
}

$s .= ";";


return $this->m_conn->getQuery($s);


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

public function create($attributes, $values){
	
$s = "INSERT INTO `" . $this->m_database ."`.`" . $this->m_table ."` (";
		

		for ($i = 0; $i <= sizeOf($attributes)-2; $i = $i + 1){
			$s .= "`" . $attributes[$i] . "`,";
		}
		$s .= "`" .$attributes[sizeOf($attributes) - 1] . "`)";
	
		$s .= " VALUES (";
	
		for ($i = 0; $i <= sizeOf($values)-2; $i = $i + 1){
			if ($values[$i] === null) $s .= "null,"; else $s .= "'" . $values[$i] . "',";
		}
		if ($values[sizeOf($values) - 1] === null) $s .= "null)"; else $s .=  "'" .  $values[sizeOf($values) - 1] . "')";
	
		$s .= ";";
	
$this->m_conn->sendQuery($s, true);
	
	
}

public function read(){

return $this->m_conn->getQuery("SELECT * FROM " . $this->m_database . "." . $this->m_table . ";");


}

public function getAttributes(){

return $this->m_conn->getQuery("SHOW COLUMNS FROM " . $this->m_database . "." . $this->m_table . ";");

}


}
?>
