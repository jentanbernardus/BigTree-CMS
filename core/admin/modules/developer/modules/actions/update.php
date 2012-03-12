<?
	$item = $admin->getModuleAction(end($path));

	BigTree::globalizePOSTVars(array("htmlspecialchars","mysql_real_escape_string"));
		
	$oroute = $route;
	$x = 2;
	while ($f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '".$item["module"]."' AND route = '$route' AND id != '".end($path)."'"))) {
		$route = $oroute."-".$x;
		$x++;
	}
	
	sqlquery("UPDATE bigtree_module_actions SET name = '$name', route = '$route', class = '$class', in_nav = '$in_nav' WHERE id = '".end($path)."'");

	$admin->growl("Developer","Updated Action");
	header("Location: ".$developer_root."modules/edit/".$item["module"]."/");
	die();
?>