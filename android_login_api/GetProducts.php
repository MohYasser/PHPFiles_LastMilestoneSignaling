<?php

$conn=mysqli_connect("localhost", "root","","android_api");

if(mysqli_connect_error($conn))
{
	echo "Failed to connect";
}
$stmt = $conn->prepare("SELECT id, item_name, description, image_url FROM items");

$stmt ->execute();
$stmt -> bind_result($id, $item_name, $description, $image_url);

$items = array();

while($stmt ->fetch()){

    $temp = array();
	
	$temp['id'] = $id;
	$temp['item_name'] = $item_name;
	$temp['description'] = $description;
	$temp['image_url'] = $image_url;

	array_push($items,$temp);
	}
	echo json_encode($items);
?>


