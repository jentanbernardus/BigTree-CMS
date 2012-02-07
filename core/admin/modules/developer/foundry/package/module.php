<?
	$packages = $admin->getModulePackages();
	$groups = $admin->getModuleGroups();
	foreach ($groups as &$group) {
		$group["modules"] = $admin->getModulesByGroup($group["id"]);
	}
?>
<h3 class="foundry">Package Module</h3>
<p>Please choose an existing package to re-package or a new module or group to package.</p>

<h4>Existing Packages</h4>
<dl class="table">
	<dt>
		<span class="foundry_name">Name</span>
		<span class="foundry_version">Version</span>
		<span class="foundry_author_wider">Author</span>
		<span class="foundry_company_wider">Company</span>
		<span class="foundry_updated">Last Updated</span>
		<span class="action">Package</span>
	</dt>
	<?
		foreach ($packages as $item) {
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
				<a href="../choose-files/existing/<?=$item["id"]?>/" class="button_package"></a>
			</li>
		</ul>
	</dd>
	<?
			}
		}
	?>
</dl>

<h4>Modules</h4>
<dl class="table">
	<dt>
		<span class="module_name_wide">Name</span>
		<span class="action">Package</span>
	</dt>
	<?
		foreach ($groups as $group) {
	?>
	<dd>
		<ul>
			<li class="module_name_wide"><strong><?=$group["name"]?></strong></li>
			<li class="action"><a href="../choose-files/group/<?=$group["id"]?>/" class="button_package"></a></li>
		</ul>
	</dd>
	<?
			foreach ($group["modules"] as $module) {
	?>
	<dd>
		<ul>
			<li class="module_name_wide">&mdash; <?=$module["name"]?></li>
			<li class="action"><a href="../choose-files/module/<?=$module["id"]?>/" class="button_package"></a></li>
		</ul>
	</dd>
	<?
			}
		}
	?>
	<dd>
		<ul>
			<li class="module_name_wide"><strong>Ungrouped Modules</strong></li>
			<li class="action">&mdash;</li>
		</ul>
	</dd>
	<?
		$q = sqlquery("SELECT * FROM bigtree_modules WHERE `group` = '0' ORDER BY name");
		while ($module = sqlfetch($q)) {
	?>
	<dd>
		<ul>
			<li class="module_name_wide">&mdash; <?=$module["name"]?></li>
			<li class="action"><a href="../choose-files/module/<?=$module["id"]?>/" class="button_package"></a></li>
		</ul>
	</dd>
	<?	
		}
	?>
</dl>