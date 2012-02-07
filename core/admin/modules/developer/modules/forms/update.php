<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));

	$fields = array();	
	
	foreach ($_POST["type"] as $key => $val) {
		$field = json_decode($_POST["options"][$key],true);
		$field["type"] = $val;
		$field["title"] = htmlspecialchars($_POST["titles"][$key]);
		$field["subtitle"] = htmlspecialchars($_POST["subtitles"][$key]);
		$fields[$key] = $field;
	}
	
	$fields = mysql_real_escape_string(json_encode($fields));	
	
	sqlquery("UPDATE bigtree_module_forms SET `fields` = '$fields', `table` = '$table', title = '$title', javascript = '$javascript', css = '$css', callback = '$callback', default_position = '$default_position' WHERE id = '".end($path)."'");
	
	$action = $admin->getModuleActionForForm(end($path));	
	$oroute = str_replace(array("add-","edit-","add","edit"),"",$action["route"]);
	if ($suffix != $oroute) {
		sqlquery("UPDATE bigtree_module_actions SET route = 'add-$suffix' WHERE module = '".$action["module"]."' AND route = 'add-$oroute'");
		sqlquery("UPDATE bigtree_module_actions SET route = 'edit-$suffix' WHERE module = '".$action["module"]."' AND route = 'edit-$oroute'");
	}
	
	$admin->growl("Developer","Updated Module Form");
	header("Location: ".$saroot."modules/edit/".$action["module"]."/");
	die();
?>