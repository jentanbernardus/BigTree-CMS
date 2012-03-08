<?
	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	
	$oroute = $cms->urlify($_POST["name"]);
	$route = $oroute;
	$x = 2;
	while (sqlrows(sqlquery("SELECT * FROM bigtree_module_groupe WHERE `name` = '".mysql_real_escape_string($name)."'"))) {
		$route = $oroute."-".$x;
		$x++;
	}
	
	sqlquery("INSERT INTO bigtree_module_groups (`name`, `route`) VALUES ('$name', '$route')");

	$admin->growl("Developer","Created Module Group");
	header("Location: ".$developer_root."modules/groups/view/");
	die();
?>