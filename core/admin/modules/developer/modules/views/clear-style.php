<?
	$view = $admin->getModuleView(end($path));
	
	$fields = $view["fields"];
	foreach ($fields as $key => $field) {
		$fields[$key]["width"] = 0;
	}
		
	// Let's create the view
	$fields = mysql_real_escape_string(json_encode($fields));
	
	sqlquery("UPDATE bigtree_module_views SET `fields` = '$fields' WHERE id = '".end($path)."'");

	$action = $admin->getModuleActionForView(end($path));

	$admin->growl("Developer","Reset View Styles");
	header("Location: ".$developer_root."modules/edit/".$action["module"]."/");
	die();
?>