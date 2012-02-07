<?
	$calls = $_POST["calls"];
	$token = $_POST["token"];
	$response = array("success" => true,"calls" => array());
	foreach ($calls as $call) {
		$name = rtrim($call["name"],"/").".php";
		$_POST = $call["variables"];
		include bigtree_path("api/".$name);
		ob_clean();
		$response["calls"][] = array("call" => $call["name"],"response" => $last_api_data);
	}
	
	echo bigtree_api_encode($response);
?>