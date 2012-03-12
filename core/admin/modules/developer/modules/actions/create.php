<?
	BigTree::globalizePOSTVars(array("htmlspecialchars","mysql_real_escape_string"));
	
	$oroute = $route;
	$x = 2;
	while ($f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '".end($path)."' AND route = '$route'"))) {
		$route = $oroute."-".$x;
		$x++;
	}
	
	sqlquery("INSERT INTO bigtree_module_actions (`module`,`name`,`route`,`in_nav`,`class`) VALUES ('".end($path)."','$name','$route','$in_nav','$class')");
	
	$admin->growl("Developer","Created Action");
	header("Location: ".$developer_root."modules/edit/".end($path)."/");
	die();
?>