<?
	include bigtree_path("admin/layouts/_header.php");
	
	$subpath = bigtree_path("admin/modules/developer/".$path[2]."/_subnav.php");
	if (file_exists($subpath)) {
		include $subpath;
	}
?>
<div id="page">
	<div id="breadcrumb">
		<ul>
			<li><a href="<?=$aroot?>">Developer</a></li> 
			<li>&raquo;</li>
			<? if ($path[2]) { ?>
			<li><a class="active" href="#"><?=ucwords(str_replace("-"," ",$path[2]))?></a></li>
			<? } else { ?>
			<li><a class="active" href="#">Landing</a></li>
			<? } ?>
		</ul>
		<br class="clear" />
	</div>
	<div>
		<h2>Developer</h2>
		<div class="add_level">
			<a href="<?=$aroot?>developer/" class="home">Home</a>
			<a href="<?=$aroot?>developer/templates/view/" class="templates">Templates</a>
			<a href="<?=$aroot?>developer/modules/view/" class="modules">Modules</a>
			<a href="<?=$aroot?>developer/modules/groups/view/" class="groups">Module Groups</a>
			<a href="<?=$aroot?>developer/field-types/view/" class="page">Field Types</a>
			<a href="<?=$aroot?>developer/callouts/view/" class="callouts">Callouts</a>
			<a href="<?=$aroot?>developer/settings/view/" class="settings">Settings</a>
			<a href="<?=$aroot?>developer/feeds/view/" class="rss">Feeds</a>
			<a href="<?=$aroot?>developer/foundry/view/" class="foundry">Foundry</a>
		</div>
		<?=$content?>
	</div>
</div>
<? include bigtree_path("admin/layouts/_footer.php") ?>