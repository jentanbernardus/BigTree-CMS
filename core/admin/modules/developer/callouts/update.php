<?
	BigTree::globalizePOSTVars(array("htmlspecialchars","mysql_real_escape_string"));

	$resources = array();
	foreach ($_POST["resources"] as $resource) {
		if ($resource["id"] && $resource["id"] != "type") {
			$options = json_decode($resource["options"],true);
			foreach ($options as $key => $val) {
				if ($key != "name" && $key != "id" && $key != "type")
					$resource[$key] = $val;
			}
			$resource["id"] = htmlspecialchars($resource["id"]);
			$resource["name"] = htmlspecialchars($resource["name"]);
			$resource["subtitle"] = htmlspecialchars($resource["subtitle"]);
			unset($resource["options"]);
			$resources[] = $resource;
		}
	}
	
	$resources = mysql_real_escape_string(json_encode($resources));

	sqlquery("UPDATE bigtree_callouts SET resources = '$resources', name = '$name', description = '$description', level = '$level' WHERE id = '$id'");

	$admin->growl("Developer","Updated Callout");
	header("Location: ".$developer_root."callouts/view/");
	die();
?>