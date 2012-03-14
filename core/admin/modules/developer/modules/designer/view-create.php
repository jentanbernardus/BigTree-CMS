<?
	BigTree::globalizePOSTVars(array("htmlspecialchars","mysql_real_escape_string"));
	
	foreach ($actions as $action => $state) {
		if ($action == "approve") {
			sqlquery("ALTER TABLE `$table` ADD COLUMN approved CHAR(2) NOT NULL");
		} elseif ($action == "feature") {
			sqlquery("ALTER TABLE `$table` ADD COLUMN featured CHAR(2) NOT NULL");
		} elseif ($action == "archive") {
			sqlquery("ALTER TABLE `$table` ADD COLUMN archived CHAR(2) NOT NULL");
		}
	}
	
	
	if ($type == "draggable") {
		sqlquery("ALTER TABLE `$table` ADD COLUMN position INT(11) NOT NULL");
	}	
	
	// Let's create the view

	$actions = mysql_real_escape_string(json_encode($actions));
	$fields = mysql_real_escape_string(json_encode($fields));
	$options = mysql_real_escape_string($_POST["options"]);
	
	sqlquery("INSERT INTO bigtree_module_views (`title`,`description`,`type`,`fields`,`actions`,`table`,`options`,`suffix`) VALUES ('$title','$description','$type','$fields','$actions','$table','$options','$suffix')");
		
	$view_id = sqlid();
	
	$admin->createModuleAction($module,"View $title",$route,"on","icon_small_home",0,$view_id);
		
	header("Location: ../complete/$module/");
	die();
?>