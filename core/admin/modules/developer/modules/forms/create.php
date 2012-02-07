<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));

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
	
	$aid = sqlid();

	sqlquery("INSERT INTO bigtree_module_actions (`module`,`in_nav`,`name`,`route`,`form`,`class`) VALUES ('".end($path)."','on','Add $title','$addroute','$aid','icon_small_add')");
	sqlquery("INSERT INTO bigtree_module_actions (`module`,`in_nav`,`name`,`route`,`form`,`class`) VALUES ('".end($path)."','','Edit $title','$editroute','$aid','icon_small_edit')");
			
	$mod = $admin->getModule(end($path));
?>
<h1><span class="icon_developer_modules"></span>Created Form</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>
<div class="form_container">
	<header>
		<p>Add/Edit <?=$title?></p>
	</header>
	<section>
		<p>Your form is now created.  If you were creating a module from scratch, the process is now complete.</p>
	</section>
	<footer>
		<a href="<?=$aroot?><?=$mod["route"]?>/" class="button white">View Module</a>
		<a href="<?=$aroot?><?=$mod["route"]?>/<?=$addroute?>/" class="button blue">View Form</a>
	</footer>
</div>