<?php

$conn=mysqli_connect("localhost", "root","","android_api");

if(mysqli_connect_error($conn))
{
	echo "Failed to connect";
}
$stmt = $conn->prepare("SELECT id, name, latitude, longitude FROM shops");

$stmt ->execute();
$stmt -> bind_result($id, $shop_name, $lattitude, $longitude);

$shops = array();

while($stmt ->fetch()){

    $temp = array();
	
	$temp['id'] = $id;
	$temp['shop_name'] = $shop_name;
	$temp['lattitude'] = $lattitude;
	$temp['longitude'] = $longitude;

	array_push($shops,$temp);
	}
	echo json_encode($shops);
?>


