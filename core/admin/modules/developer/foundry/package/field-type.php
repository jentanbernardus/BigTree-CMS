<?
	$field_types = $admin->getFieldTypes();
?>
<h3 class="foundry">Package Field Type: Choose Files</h3>
<p>Please choose an existing field type to package below.</p>

<dl class="table" id="sort_table">
	<dt>
		<span class="foundry_name">Name</span>
		<span class="foundry_version">Version</span>
		<span class="foundry_author_wider">Author</span>
		<span class="foundry_company_wider">Company</span>
		<span class="foundry_updated">Last Updated</span>
		<span class="action">Package</span>
	</dt>
	<?
		foreach ($field_types as $item) {
			if ($item["downloaded"] != "on") {
	?>
	<dd>
		<ul>
			<li class="foundry_name"><?=$item["name"]?></li>
			<li class="foundry_version"><?=$item["primary_version"]?>.<?=$item["secondary_version"]?>.<?=$item["tertiary_version"]?></li>
			<li class="foundry_author_wider"><?=$item["author"]?></li>
			<li class="foundry_company_wider"><?=$item["company"]?></li>
			<li class="foundry_updated"><?=date("F j, Y",strtotime($item["last_updated"]))?></li>
			<li class="action">
				<a href="../choose-files/field-type/<?=$item["id"]?>/" class="button_package"></a>
			</li>
		</ul>
	</dd>
	<?
			}
		}
	?>
</dl>