<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));

	$resources = array();
	foreach ($_POST["resources"] as $resource) {
		if ($resource["id"]) {
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
	
	$template = $cms->getTemplateById($id);

	if ($_FILES["image"]["tmp_name"]) {
		$image = get_safe_filename($GLOBALS["server_root"]."custom/admin/images/templates/",$_FILES["image"]["name"]);
		move_uploaded_file($_FILES["image"]["tmp_name"],$GLOBALS["server_root"]."custom/admin/images/templates/".$image);
		chmod($GLOBALS["server_root"]."custom/admin/images/templates/".$image,0777);
		$image = mysql_real_escape_string($image);
	} elseif ($existing_image) {
		$image = $existing_image;
	} else {
		$image = $template["image"];
	}
	
	$resources = mysql_real_escape_string(json_encode($resources));

	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	$description = mysql_real_escape_string(htmlspecialchars($_POST["description"]));

	sqlquery("UPDATE bigtree_templates SET resources = '$resources', image = '$image', name = '$name', module = '$module', description = '$description', level = '$level', callouts_enabled = '$calloutss_enabled' WHERE id = '$id'");

	$admin->growl("Developer","Updated Template");
	header("Location: ".$saroot."templates/view/");
	die();
?>