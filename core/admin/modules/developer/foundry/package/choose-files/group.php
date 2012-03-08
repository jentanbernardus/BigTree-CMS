<?
	include bigtree_path("admin/modules/developer/foundry/package/choose-files/_file_chooser_header.php");
	
	$group = $admin->getModuleGroup(end($commands));
	$modules = $admin->getModulesByGroup($group);
	foreach ($modules as $m) {
		gatherModuleInformation($m["id"]);
	}

	if (file_exists($server_root."custom/inc/required/".$cms->urlify($group["name"]).".php")) {
		$required_files[] = "custom/inc/required/".$cms->urlify($group["name"]).".php";
	}
?>
<h3 class="foundry">Package Module Group: Choose Files</h3>
<p>Please select all the files required for the Module Group &ldquo;<?=$group["name"]?>&rdquo;</p>
<br />
<h4>Package Information</h4>
<form method="post" action="<?=$admin_root?>developer/foundry/package/release-notes/module/" class="module">
	<input type="hidden" name="group" value="<?=$group["id"]?>" />
	<? include bigtree_path("admin/modules/developer/foundry/package/choose-files/_file_chooser_footer.php") ?>