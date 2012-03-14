<?
	BigTree::globalizePOSTVars(array("htmlspecialchars","mysql_real_escape_string"));

	$module = end($path);

	$subroute = $suffix ? mysql_real_escape_string($_POST["suffix"]) : "";
			
	if ($subroute) {
		$editroute = "edit-".$subroute;
		$addroute = "add-".$subroute;
	} else {
		$editroute = "edit";
		$addroute = "add";
	}
	
	
	foreach ($_POST["type"] as $key => $val) {
		$field = json_decode($_POST["options"][$key],true);
		$field["type"] = $val;
		$field["title"] = htmlspecialchars($_POST["titles"][$key]);
		$field["subtitle"] = htmlspecialchars($_POST["subtitles"][$key]);
		$fields[$key] = $field;
	}
	
	$fields = mysql_real_escape_string(json_encode($fields));
	
	sqlquery("INSERT INTO bigtree_module_forms (`title`,`javascript`,`css`,`callback`,`table`,`fields`,`default_position`) VALUES ('$title','$javascript','$css','$callback','$table','$fields','$default_position')");
	
	$form_id = sqlid();

	$admin->createModuleAction($module,"Add $title",$addroute,"on","icon_small_add",$form_id);
	$admin->createModuleAction($module,"Edit $title",$editroute,"","icon_small_edit",$form_id);
			
	$mod = $admin->getModule($module);
?>
<h1><span class="icon_developer_modules"></span>Created Form</h1>
<? include BigTree::path("admin/modules/developer/modules/_nav.php"); ?>
<div class="form_container">
	<section>
		<h3 class="action_title">Add/Edit <?=$title?></h3>
		<p>Your form has been created. If you were creating a module from scratch, the process is now complete.</p>
	</section>
	<footer>
		<a href="<?=$admin_root?><?=$mod["route"]?>/" class="button white">View Module</a>
		<a href="<?=$admin_root?><?=$mod["route"]?>/<?=$addroute?>/" class="button blue">View Form</a>
	</footer>
</div>