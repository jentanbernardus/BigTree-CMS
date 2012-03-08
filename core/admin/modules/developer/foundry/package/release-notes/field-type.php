<?
	// First we need to package the file so they can download it manually if they wish.
	$type = $admin->getFieldType($_POST["id"]);
	$files = $_POST["files"];
	
	// Clear anything that was previously in the packager directory...
	$dir = $server_root."cache/packager/";
	exec("rm -rf ".$dir);
	mkdir($server_root."cache/packager");
	
	$index_data = "";
	$saved_files = array();
	
	$x = 0;
	foreach ($files as $file) {
		$x++;
		copy($file,$dir.$x.".part.bpz");
		$index_data .= "File::||::$x.part.bpz::||::".str_replace($server_root,"",$file)."::||::\n";
		$saved_files[] = str_replace($server_root,"",$file);
	}
	
	$index_data = trim($index_data);
	file_put_contents($dir."index.bpz",$index_data);
	// Make the package
	exec("cd $dir; tar -zcf ".$server_root."cache/package.tar.gz *");
	bigtree_move($server_root."cache/package.tar.gz",$server_root."cache/types/".$type["id"].".tar.gz");
	
	// Update the files for the field type.
	sqlquery("UPDATE bigtree_field_types SET files = '".mysql_real_escape_string(json_encode($saved_files))."' WHERE id = '".$type["id"]."'");
	
	// Ok, now let's get to the step where we can submit it to the foundry.
	bigtree_clean_globalize_array($type,array("htmlspecialchars"));
?>
<h3 class="foundry">Package Field Type: Details</h3>
<form method="post" action="<?=$admin_root?>developer/foundry/package/submit/field-type/" class="module">
	<input type="hidden" name="id" value="<?=$type["id"]?>" />
	<fieldset>
		<label class="required">Name</label>
		<input type="text" class="required" name="name" value="<?=$name?>" />
	</fieldset>
	<fieldset>
		<label>Private <small>(if you wish to only make this module accessible when logged into your Foundry account check this box)</small></label>
		<input type="checkbox" name="private" <? if ($private) { ?>checked="checked" <? } ?>/>
	</fieldset>
	<fieldset>
		<label class="required">Allow This Field Type To Be Used For:</label>
		<input type="checkbox" name="pages"<? if ($pages) { ?> checked="checked"<? } ?> /> Pages<br />
		<input type="checkbox" name="modules"<? if ($modules) { ?> checked="checked"<? } ?> /> Modules<br />
		<input type="checkbox" name="callouts"<? if ($callouts) { ?> checked="checked"<? } ?> /> Callouts<br />
	</fieldset>
	<fieldset>
		<label>Version</label>
		<input type="text" name="primary_version" value="<?=$primary_version?>" class="field_type_version" />.<input type="text" name="secondary_version" value="<?=$secondary_version?>" class="field_type_version" />.<input type="text" name="tertiary_version" value="<?=$tertiary_version?>" class="field_type_version" />
	</fieldset>
	<fieldset>
		<label>Description</label>
		<textarea name="description"><?=$description?></textarea>
	</fieldset>
	<fieldset>
		<label>Release Notes</label>
		<textarea name="release_notes"><?=$release_notes?></textarea>
	</fieldset>
	
	<div id="form_error" style="display: none;">
		<p>Please check the marked fields for errors.</p>
	</div>
	<input type="submit" class="button white" value="Submit to Foundry" />
</form>