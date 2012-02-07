<h1><span class="modules"></span>Add <?=$form["title"]?></h1>
<?
	include bigtree_path("admin/auto-modules/_nav.php");
	$tags = array();
	$permission_level = $admin->getAccessLevel($module);
	include bigtree_path("admin/auto-modules/forms/_form.php");
?>