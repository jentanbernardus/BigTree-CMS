<? include bigtree_path("admin/layouts/_header.php") ?>
<div id="page">
	<div id="breadcrumb">
		<ul>
			<li><a href="<?=$aroot?>settings/">Settings</a></li>
			<li>&raquo;</li>
			<li><a href="<?=$aroot?>settings/" class="active"><? if ($path[2]) { ?><?=ucwords(str_replace("-"," ",$path[2]))?><? } else { ?>Landing<? } ?></a></li>
		</ul>
		<br class="clear" />
	</div>
	<div>
		<h2>Settings</h2>
		<br />
		<?=$content?>
	</div>
</div>
<? include bigtree_path("admin/layouts/_footer.php") ?>