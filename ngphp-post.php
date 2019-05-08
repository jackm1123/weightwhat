<?php


// try commenting out the header setting to experiment how the back end refuses the request
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding');

// $data = (int) $_SERVER['CONTENT_LENGTH']; 


// retrieve data from the request
$postdata = file_get_contents("php://input");

// process data 
// (this example simply extracts the data and restructures them back) 
$request = json_decode($postdata);

$data = [];
foreach ($request as $k => $v)
{
  $data[0][$k] = $v;
}

// sent response (in json format) back to the front end
$username = $data[0]['username'];
$password = $data[0]['password'];
$password2 = $data[0]['password2'];

if( !empty($username) && !empty($password) && ($password == $password2)){

	$hostname = 'localhost:3306';
	$dbname = 'cs4640';
	$dsn = "mysql:host=$hostname;dbname=$dbname";
	$db = new PDO($dsn, 'cs4640', 'cs4640');
	$hashed_pass = md5($password);

	$query = "INSERT INTO users (username,password) VALUES ('$username','$hashed_pass')";
	$statement = $db->prepare($query); 
	$statement->execute();
	$statement->closeCursor();

	echo json_encode('received');
}





?>