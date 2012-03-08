<?
	include bigtree_path("admin/modules/developer/foundry/package/choose-files/_file_chooser_header.php");
	
	$module = $admin->getModule(end($commands));	
	gatherModuleInformation($module["id"]);
?>
<h3 class="foundry">Package Module: Choose Files</h3>
<p>Please select all the files required for the Module &ldquo;<?=$module["name"]?>&rdquo;</p>
<br />
<h4>Package Information</h4>
<form method="post" action="<?=$admin_root?>developer/foundry/package/release-notes/module/" class="module">
	<input type="hidden" name="module" value="<?=$module["id"]?>" />
	<? include bigtree_path("admin/modules/developer/foundry/package/choose-files/_file_chooser_footer.php") ?>