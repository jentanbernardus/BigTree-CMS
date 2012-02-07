<?
	$view = $admin->getModuleView(end($path));
	
	$fields = $view["fields"];
	$x = 0;
	foreach ($fields as $key => $field) {
		$fields[$key]["width"] = $_POST[$key];
	}
		
	// Let's create the view
	$fields = mysql_real_escape_string(json_encode($fields));
	
	sqlquery("UPDATE bigtree_module_views SET `fields` = '$fields' WHERE id = '".end($path)."'");

	$action = $admin->getModuleActionForView(end($path));

	$admin->growl("Developer","Updated View Styles");
	header("Location: ".$saroot."modules/edit/".$action["module"]."/");
	die();
?>