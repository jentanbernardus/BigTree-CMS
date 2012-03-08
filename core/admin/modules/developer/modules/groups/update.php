<?
	$id = end($path);
	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	
	$oroute = $cms->urlify($_POST["name"]);
	$route = $oroute;
	$x = 2;
	while (sqlrows(sqlquery("SELECT * FROM bigtree_module_groupe WHERE `name` = '".mysql_real_escape_string($name)."' AND id != '" . $id . "'"))) {
		$route = $oroute."-".$x;
		$x++;
	}
	
	sqlquery("UPDATE bigtree_module_groups SET name = '$name', route = '$route' WHERE id = '$id'");

	$admin->growl("Developer","Updated Module Group");
	header("Location: ".$developer_root."modules/groups/view/");
	die();
?>