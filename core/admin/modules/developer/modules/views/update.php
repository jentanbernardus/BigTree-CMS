<?	
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));
	
	// Clean up actions
	$clean_actions = array();
	foreach ($actions as $key => $val) {
		if ($val) {
			$clean_actions[$key] = $val;
		}
	}
	$actions = $clean_actions;
	
	$old_view = BigTreeAutoModule::getView(end($path));
	
	$columns = sqlcolumns($table);
	
	// If we've switched from searchable -> anything else or vice versa, wipe the width columns.
	// Also wipe them if we have added or removed a column.
	$keys_match = true;
	foreach ($old_view["fields"] as $key => $field) {
		if (!$fields[$key]) {
			$keys_match = false;
		}
	}
	foreach ($fields as $key => $field) {
		if (!$old_view["fields"][$key]) {
			$keys_match = false;
		}
	}
	// Check actions
	if (count($old_view["actions"]) != count($actions)) {
		$keys_match = false;
	}
	if (!$keys_match || ($old_view["type"] == "searchable" && $type != "searchable") || ($type == "searchable" && $old_view["type"] != "searchable")) {
		foreach ($fields as $key => $field) {
			unset($fields[$key]["width"]);
		}
	}
	
	$errors = array();
	// Check for errors
	if (($type == "draggable" || $type == "draggable-group" || $type == "images" || $type == "images-group") && !$columns["position"]) {
		$errors[] = "Sorry, but you can't create a draggable view without a 'position' column in your table.  Please create a position column (integer) in your table and try again.";
	}
	
	if ($actions["archive"] && !(($columns["archived"]["type"] == "char" || $columns["archived"]["type"] == "varchar") && $columns["archived"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'archived' that is char(2) in order to use the archive function.";
	}
	if ($actions["approve"] && !(($columns["approved"]["type"] == "char" || $columns["approved"]["type"] == "varchar") && $columns["approved"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'approved' that is char(2) in order to use the approve function.";
	}
	if ($actions["feature"] && !(($columns["featured"]["type"] == "char" || $columns["featured"]["type"] == "varchar") && $columns["featured"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'featured' that is char(2) in order to use the feature function.";
	}
	
	if (count($errors)) {
		echo "<h3>Editing Module View</h3>";
		foreach ($errors as $error) {
			echo "<p>".$error."</p>";
		}
	} else {
		// Let's update the view
		$actions = mysql_real_escape_string(json_encode($actions));
		$fields = mysql_real_escape_string(json_encode($fields));
		$options = mysql_real_escape_string($_POST["options"]);
		
		sqlquery("UPDATE bigtree_module_views SET `table` = '$table', `title` = '$title', `description` = '$description', `type` = '$type', `options` = '$options', `actions` = '$actions', `fields` = '$fields', `suffix` = '$suffix', `uncached` = '$uncached', `preview_url` = '$preview_url' WHERE id = '".end($path)."'");
		
		$action = $admin->getModuleActionForView(end($path));
		
		$admin->growl("Developer","Updated View");
		header("Location: ".$developer_root."modules/edit/".$action["module"]."/");
		BigTreeAutoModule::clearCache($view);
		die();
	}
?>