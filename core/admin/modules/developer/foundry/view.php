<?
	$modules = $admin->getModulePackages("last_updated DESC");
	$field_types = $admin->getFieldTypes("last_updated DESC");
?>
<h3 class="foundry">Installed Items</h3>
<p>Below is a list of modules and field types you've installed from the BigTree Foundry.<br />To install additional modules, <a href="../modules/">click here</a>.  To install additional field types, <a href="../field-types/">click here</a>.</p>
<br />
<h4>Modules</h4>
<dl class="table" id="sort_table">
	<dt>
		<span class="foundry_name">Name</span>
		<span class="foundry_version">Version</span>
		<span class="foundry_author">Author</span>
		<span class="foundry_company">Company</span>
		<span class="foundry_updated">Last Updated</span>
		<span class="action">Edit</span>
		<span class="action">Uninstall</span>
	</dt>
	<? foreach ($modules as $item) { ?>
	<dd>
		<ul>
			<li class="foundry_name"><?=$item["name"]?></li>
			<li class="foundry_version"><?=$item["primary_version"]?>.<?=$item["secondary_version"]?>.<?=$item["tertiary_version"]?></li>
			<li class="foundry_author"><?=$item["author"]?></li>
			<li class="foundry_company"><?=$item["company"]?></li>
			<li class="foundry_updated"><?=date("F j, Y",strtotime($item["last_updated"]))?></li>
			<li class="action">
				<? if ($item["downloaded"]) { ?>
				<span class="icon_no"></span>
				<? } else { ?>
				<a href="../package/choose-files/existing/<?=$item["id"]?>/" class="button_edit"></a>
				<? } ?>
			</li>
			<li class="action">
				<a href="#<?=$item["id"]?>" class="button_delete uninstall_module"></a>
			</li>
		</ul>
	</dd>
	<? } ?>
</dl>

<h4>Field Types</h4>
<dl class="table" id="sort_table">
	<dt>
		<span class="foundry_name">Name</span>
		<span class="foundry_version">Version</span>
		<span class="foundry_author">Author</span>
		<span class="foundry_company">Company</span>
		<span class="foundry_updated">Last Updated</span>
		<span class="action">Edit</span>
		<span class="action">Uninstall</span>
	</dt>
	<? foreach ($field_types as $item) { ?>
	<dd>
		<ul>
			<li class="foundry_name"><?=$item["name"]?></li>
			<li class="foundry_version"><?=$item["primary_version"]?>.<?=$item["secondary_version"]?>.<?=$item["tertiary_version"]?></li>
			<li class="foundry_author"><?=$item["author"]?></li>
			<li class="foundry_company"><?=$item["company"]?></li>
			<li class="foundry_updated"><?=date("F j, Y",strtotime($item["last_updated"]))?></li>
			<li class="action">
				<? if ($item["downloaded"]) { ?>
				<span class="icon_no"></span>
				<? } else { ?>
				<a href="../package/choose-files/field-type/<?=$item["id"]?>/" class="button_edit"></a>
				<? } ?>
			</li>
			<li class="action">
				<a href="#<?=$item["id"]?>" class="button_delete uninstall_field_type"></a>
			</li>
		</ul>
	</dd>
	<? } ?>
</dl>

<h3 class="foundry">BigTree Version</h3>
<?
	$update = bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/is-update-available/?version=".$GLOBALS["bigtree"]["version"]);
	if ($update) {
?>
<p>An update is available!</p>
<?=$update?>
<a href="../update/bigtree/" class="button white">Download Update</a>
<?
	} else {
?>
<p>No updates are available.</p>
<?	
	}
?>

<script type="text/javascript">
	$(".uninstall_module").click(function() {
		if (confirm("Are you sure you want to uninstall this module?\n It will also remove any tables, templates, callouts, feeds, settings, and files associated with this module and their data will not be recoverable.")) {
			document.location.href = "../uninstall/module/" + BigTree.CleanHref($(this).attr("href")) + "/";		
		}
		
		return false;
	});
	
	$(".uninstall_field_type").click(function() {
		if (confirm("Are you sure you want to uninstall this field type?\n It will also remove any files associated with this field type and changes will not be recoverable.")) {
			document.location.href = "../uninstall/field-type/" + BigTree.CleanHref($(this).attr("href")) + "/";
		}
		
		return false;
	});
</script>