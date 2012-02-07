<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));
	
	// Let's see if the ID has already been used.
	if ($admin->ModuleFieldTypes[$id] || sqlrows(sqlquery("SELECT * FROM bigtree_field_types WHERE id = '$id'"))) {
		$_SESSION["bigtree"]["admin_saved"] = $_POST;
		$_SESSION["bigtree"]["admin_error"] = true;
		header("Location: ../add/");
		die();
	}
	
	// If we're creating a new file, let's populate it with some convenience things to show what resources are available.
	$file_contents = '<?
	/*
		Resources Available:
';
	
	$resources = array();
	foreach ($_POST["resources"] as $resource) {
		if ($resource["id"]) {
			$options = json_decode($resource["options"],true);
			foreach ($options as $key => $val) {
				if ($key != "name" && $key != "id" && $key != "type")
					$resource[$key] = $val;
			}
			
			$file_contents .= '		$'.$resource["id"].' = '.$resource["name"].' - '.$admin->TemplateFieldTypes[$resource["type"]]."\n";
			
			$resource["id"] = htmlspecialchars($resource["id"]);
			$resource["name"] = htmlspecialchars($resource["name"]);
			$resource["subtitle"] = htmlspecialchars($resource["subtitle"]);
			unset($resource["options"]);
			$resources[] = $resource;
		}
	}
	
	$resources = mysql_real_escape_string(json_encode($resources));
	
	if ($_FILES["image"]["tmp_name"]) {
		$image = get_safe_filename($GLOBALS["server_root"]."custom/admin/images/templates/",$_FILES["image"]["name"]);
		move_uploaded_file($_FILES["image"]["tmp_name"],$GLOBALS["server_root"]."custom/admin/images/templates/".$image);
		chmod($GLOBALS["server_root"]."custom/admin/images/templates/".$image,0777);
		$image = mysql_real_escape_string($image);
	} elseif ($existing_image) {
		$image = $existing_image;
	} else {
		$image = "page.png";
	}
	
	$file_contents .= '	*/
?>';
	
	if ($is_module) {
		if (!file_exists($GLOBALS["server_root"]."templates/modules/".$id)) {
			mkdir($GLOBALS["server_root"]."templates/modules/".$id);
			chmod($GLOBALS["server_root"]."templates/modules/".$id,0777);
		}
		if (!file_exists($GLOBALS["server_root"]."templates/modules/".$id."/default.php")) {
			file_put_contents($GLOBALS["server_root"]."templates/modules/".$id."/default.php",$file_contents);
			chmod($GLOBALS["server_root"]."templates/modules/".$id."/default.php",0777);
		}
		$id = "module-".$id;
	} else {
		if (!file_exists($GLOBALS["server_root"]."templates/pages/".$id.".php")) {
			file_put_contents($GLOBALS["server_root"]."templates/pages/".$id.".php",$file_contents);
			chmod($GLOBALS["server_root"]."templates/pages/".$id.".php",0777);
		}
	}
	
	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	$description = mysql_real_escape_string(htmlspecialchars($_POST["description"]));
	
	sqlquery("INSERT INTO bigtree_templates (`id`,`name`,`module`,`resources`,`image`,`description`,`level`,`callouts_enabled`) VALUES ('$id','$name','$module','$resources','$image','$description','$level','$callouts_enabled')");
	
	$admin->growl("Developer","Created Template");
	header("Location: ".$saroot."templates/view/");
	die();
?>