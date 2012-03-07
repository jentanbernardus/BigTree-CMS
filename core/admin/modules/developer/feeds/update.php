<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));
	
	$options = json_decode($_POST["options"],true);
	foreach ($options as &$option) {
		$option = str_replace($www_root,"{wwwroot}",$option);
	}
	
	$fields = mysql_real_escape_string(json_encode($_POST["fields"]));
	$options = mysql_real_escape_string(json_encode($options));
	
	sqlquery("UPDATE bigtree_feeds SET name = '$name', description = '$description', `table` = '$table', type = '$type', fields = '$fields', options = '$options' WHERE id = '".mysql_real_escape_string(end($commands))."'");
	
	$admin->growl("Developer","Updated Feed");
	header("Location: ".$developer_root."feeds/view/");
	die();
?>