<?
	$groups = $admin->getModuleGroups();
?>
<h1><span class="icon_developer_modules"></span>Groups</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>
<div class="table">
	<summary>
		<h2>Module Groups</h2>
	</summary>
	<header>
		<span class="developer_templates_name">Group Name</span>
		<span class="view_action">Edit</span>
		<span class="view_action">Delete</span>
	</header>
	<ul id="groups">
		<? foreach ($groups as $item) { ?>
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
	$("#groups").sortable({ items: "li", handle: ".icon_sort", update: function() {
		$.ajax("<?=$aroot?>ajax/developer/order-module-groups/?sort=" + escape($("#groups").sortable("serialize"))); 
	}});

	$(".icon_delete").click(function() {
		new BigTreeDialog("Delete Module Group",'<p class="confirm">Are you sure you want to delete this module group?<br /><br />Modules in this group will become uncategorized.</p>',$.proxy(function() {
			document.location.href = $(this).attr("href");
		},this),"delete",false,"OK");
		
		return false;
	});
</script>