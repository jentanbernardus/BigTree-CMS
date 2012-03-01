<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));
	
	// If we're creating a new file, let's populate it with some convenience things to show what resources are available.
	$file_contents = '<?
	/*
		Resources Available:
';
	
	$cached_types = $admin->getCachedFieldTypes();
	$types = $cached_types["callout"];
	
	$resources = array();
	foreach ($_POST["resources"] as $resource) {
		if ($resource["id"] && $resource["id"] != "type") {
			$options = json_decode($resource["options"],true);
			foreach ($options as $key => $val) {
				if ($key != "name" && $key != "id" && $key != "type")
					$resource[$key] = $val;
			}
			
			$file_contents .= '		$'.$resource["id"].' = '.$resource["name"].' - '.$types[$resource["type"]]."\n";
			
			$resource["id"] = htmlspecialchars($resource["id"]);
			$resource["name"] = htmlspecialchars($resource["name"]);
			$resource["subtitle"] = htmlspecialchars($resource["subtitle"]);
			unset($resource["options"]);
			$resources[] = $resource;
		}
	}
	
	$file_contents .= '	*/
?>';
	
	$resources = mysql_real_escape_string(json_encode($resources));
	
	if (!file_exists($GLOBALS["server_root"]."templates/callouts/".$id.".php")) {
		file_put_contents($GLOBALS["server_root"]."templates/callouts/".$id.".php",$file_contents);
		chmod($GLOBALS["server_root"]."templates/callouts/".$id.".php",0777);
	}
	
	sqlquery("INSERT INTO bigtree_callouts (`id`,`name`,`description`,`resources`,`level`) VALUES ('$id','$name','$description','$resources','$level')");

	
	$admin->growl("Developer","Created Callout");
	header("Location: ".$saroot."callouts/view/");
	die();		
?>