<?
	$id = end($path);
	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	sqlquery("UPDATE bigtree_module_groups SET name = '$name' WHERE id = '$id'");

	$admin->growl("Developer","Updated Module Group");
	header("Location: ".$saroot."modules/groups/view/");
	die();
?>