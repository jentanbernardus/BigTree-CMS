<?
	BigTree::globalizePOSTVars();

	$module = end($bigtree["path"]);

	$suffix = isset($suffix) ? "-".$suffix : "";
	$default_position = isset($default_position) ? $default_position : "";

	$fields = array();
	foreach ($_POST["type"] as $key => $val) {
		$field = json_decode(str_replace(array("\r","\n"),array('\r','\n'),$_POST["options"][$key]),true);
		$field["type"] = $val;
		$field["title"] = htmlspecialchars($_POST["titles"][$key]);
		$field["subtitle"] = htmlspecialchars($_POST["subtitles"][$key]);
		$fields[$key] = $field;
	}

	$form_id = $admin->createModuleForm($title,$table,$fields,$preprocess,$callback,$default_position,$return_view);
	$admin->createModuleAction($module,"Add $title","add".$suffix,"on","add",$form_id);
	$admin->createModuleAction($module,"Edit $title","edit".$suffix,"","edit",$form_id);

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
		<a href="<?=ADMIN_ROOT?><?=$mod["route"]?>/" class="button white">View Module</a>
		<a href="<?=ADMIN_ROOT?><?=$mod["route"]?>/add<?=$suffix?>/" class="button blue">View Form</a>
	</footer>
</div>