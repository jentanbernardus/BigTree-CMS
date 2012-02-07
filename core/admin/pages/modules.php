<?
	$breadcrumb = array(array("link" => "modules/","title" => "Modules"));
	$module_title = "Modules";
?>
<h1><span class="modules"></span>Modules</h1>
<?
	$groups = $admin->getModuleGroups();
	foreach ($groups as $group) {
		$modules = $admin->getModulesByGroup($group["id"]);
		if (count($modules)) {
?>
<div class="table">
	<summary><h2><?=$group["name"]?></h2></summary>
	<section class="modules">
		<? foreach ($modules as $module) { ?>
		<p class="module">
			<? if ($admin->moduleActionExists($module["id"],"add")) { ?>
			<a href="<?=$aroot?><?=$module["route"]?>/add/" class="add"><span class="icon_small icon_small_add"></span></a>
			<? } ?>
			<a class="module_name" href="<?=$aroot?><?=$module["route"]?>/"><?=$module["name"]?></a>
		</p>
		<? } ?>
	</section>
</div>
<?
		}
	}
		
	$misc = $admin->getModulesByGroup(0);
	if (count($misc)) {
?>
<div class="table">
	<summary><h2>Ungrouped Modules</h2></summary>
	<section class="modules">
		<? foreach ($misc as $module) { ?>
		<p class="module">
			<? if ($admin->moduleActionExists($module["id"],"add")) { ?>
			<a href="<?=$aroot?><?=$module["route"]?>/add/" class="add"><span class="icon_small icon_small_add"></span></a>
			<? } ?>
			<a class="module_name" href="<?=$aroot?><?=$module["route"]?>/"><?=$module["name"]?></a>
		</p>
		<? } ?>
	</section>
</div>
<?
	}
?>