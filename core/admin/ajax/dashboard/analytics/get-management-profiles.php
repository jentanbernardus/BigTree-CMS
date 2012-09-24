<?
	include "_setup.php";
	header("Content-type: text/json");
	
	$profiles = $analytics->management_profiles->listManagementProfiles($_POST["account"], $_POST["property"]);
	$response = array();
	
	foreach ($profiles->items as $item) {
		$response[] = array("name" => $item->name, "id" => $item->id);
	}
	echo json_encode($response);
?>