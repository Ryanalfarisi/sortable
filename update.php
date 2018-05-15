<?php
$server 	= "localhost";
$username 	= "root";
$password 	= "root";
$dbname 	= "sortable";

//Create Connection
$conn = new mysql($server, $username, $password, $dbname);

//Check Connection
if ($conn->connect_error){
	die("Connection Failed: ". $conn->connect_error);
}

$action 	= $_POST['action'];
$column 	= $_POST['type'];
$items 		= $_POST['items'];

if($action == "on_update"){
	$extra = '';
	foreach($items as $key => $item){
		$extra .= "WHEN id=$item THEN $key";
	}	

	$id_lists  	= implode(",",$items);
	$sql 		= "UPDATE $column SET order_id = CASE $extra END WHERE id IN ($id_lists)";


}
if ($conn->query($sql)=== TRUE){
	echo 'berhasil update';
}else{
	echo 'error'.$conn->error;
}

$conn->close();
?>