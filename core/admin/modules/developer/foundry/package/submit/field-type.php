<?
	// We might be creating a foundry login via POST on this page as well, so make sure there's a field ID
	if ($_POST["id"]) {
		bigtree_process_post_vars(array("mysql_real_escape_string"));

		sqlquery("UPDATE bigtree_field_types SET name = '$name', pages = '$pages', modules = '$modules', callouts = '$callouts', primary_version = '$primary_version', secondary_version = '$secondary_version', tertiary_version = '$tertiary_version', description = '$description', release_notes = '$release_notes', private = '$private', last_updated = NOW() WHERE id = '$id'");
	}
	$js[] = "foundry.js";
	$type = $admin->getFieldType($id);
?>
<h3 class="foundry">Submit To Foundry</h3>
<p>By submitting your code to the BigTree Foundry, you agree to the <a href="http://developer.bigtreecms.com/foundry/terms-of-service/">Terms of Service</a>.  You may include and/or link to a license agreement in your description but by uploading to the BigTree Foundry you grant Fastspot the license to redistribute, for free, your code and any associated images.</p>
<?
	if ($type["foundry_id"]) {
?>
<h4>Existing Foundry Entry</h4>
<p>BigTree has detected that this field type has already been submitted to Foundry.  If you are submitting an update, please include release notes and a higher version number.</p>
<?
	}

	include bigtree_path("admin/layouts/_foundry_login.php");
?>
<div id="foundry_continue"<? if (!$logged_in) { ?> style="display: none;"<? } ?>>
	<a class="button orange" href="../../process/field-type/<?=$type["id"]?>/">Continue</a>
</div>