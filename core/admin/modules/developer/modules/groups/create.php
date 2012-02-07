<?
	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	sqlquery("INSERT INTO bigtree_module_groups (`name`) VALUES ('$name')");

	$admin->growl("Developer","Created Module Group");
	header("Location: ".$saroot."modules/groups/view/");
	die();
?>