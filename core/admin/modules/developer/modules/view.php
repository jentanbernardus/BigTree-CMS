<?
	$groups = $admin->getModuleGroups();
	foreach ($groups as &$group) {
		$group["modules"] = $admin->getModulesByGroup($group["id"]);
	}
	
	$ungrouped_modules = $admin->getModulesByGroup(0);
?>
<h1><span class="icon_developer_modules"></span>Modules</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>
<? foreach ($groups as $group) { ?>
<div class="table">
	<summary>
		<h2><?=$group["name"]?></h2>
	</summary>
	<header>
		<span class="developer_templates_name">Module Name</span>
		<span class="view_action">Edit</span>
		<span class="view_action">Delete</span>
	</header>
	<ul id="group_<?=$group["id"]?>">
		<? foreach ($group["modules"] as $item) { ?>
		<li id="row_<?=$item["id"]?>">
			<section class="developer_templates_name">
				<span class="icon_sort"></span>
				<?=$item["name"]?>
			</section>
			<section class="view_action">
				<a href="<?=$sroot?>edit/<?=$item["id"]?>/" class="icon_edit"></a>
			</section>
			<section class="view_action">
				<a href="<?=$sroot?>delete/<?=$item["id"]?>/" class="icon_delete"></a>
			</section>
		</li>
		<? } ?>
	</ul>
</div>

<script type="text/javascript">
	$("#group_<?=$group["id"]?>").sortable({ items: "li", handle: ".icon_sort", update: function() {
		$.ajax("<?=$aroot?>ajax/developer/order-modules/?sort=" + escape($("#group_<?=$group["id"]?>").sortable("serialize")));
	}});
</script>
<? } ?>

<div class="table">
	<summary>
		<h2>Ungrouped Modules</h2>
	</summary>
	<header>
		<span class="developer_templates_name">Module Name</span>
		<span class="view_action">Edit</span>
		<span class="view_action">Delete</span>
	</header>
	<ul id="group_0">
		<? foreach ($ungrouped_modules as $item) { ?>
		<li id="row_<?=$item["id"]?>">
			<section class="developer_templates_name">
				<span class="icon_sort"></span>
				<?=$item["name"]?>
			</section>
			<section class="view_action">
				<a href="<?=$sroot?>edit/<?=$item["id"]?>/" class="icon_edit"></a>
			</section>
			<section class="view_action">
				<a href="<?=$sroot?>delete/<?=$item["id"]?>/" class="icon_delete"></a>
			</section>
		</li>
		<? } ?>
	</ul>
</div>

<script type="text/javascript">
	$("#group_0").sortable({ items: "li", handle: ".icon_sort", update: function() {
		$.ajax("<?=$aroot?>ajax/developer/order-modules/?sort=" + escape($("#group_0").sortable("serialize")));
	}});

	$(".icon_delete").click(function() {
		new BigTreeDialog("Delete Module",'<p class="confirm">Are you sure you want to delete this module?',$.proxy(function() {
			document.location.href = $(this).attr("href");
		},this),"delete",false,"OK");
		
		return false;
	});
</script>