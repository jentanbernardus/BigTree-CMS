<?	
	bigtree_clean_globalize_array($view);
		
	$m = BigTreeAutoModule::getModuleForView($view);
	$perm = $admin->getAccessLevel($m);
	
	$suffix = $suffix ? "-".$suffix : "";
?>
<div class="table">
	<summary>
		<input type="search" class="form_search" id="search" placeholder="Search" />
	</summary>
	<header>
		<?
			$x = 0;
			foreach ($fields as $key => $field) {
				$x++;
		?>
		<span class="view_column" style="width: <?=$field["width"]?>px;"><?=$field["title"]?></span>
		<?
			}
			foreach ($actions as $action => $status) {
		?>
		<span class="view_action"><?=$action?></span>
		<?
			}
		?>
	</header>
	<ul id="sort_table">
		<? include bigtree_path("admin/ajax/auto-modules/views/draggable.php") ?>
	</ul>
</div>

<? include bigtree_path("admin/auto-modules/views/_common-js.php") ?>
<script type="text/javascript">
	function reSearch() {
		$("#sort_table").load("<?=$aroot?>ajax/auto-modules/views/draggable/", { view: <?=$view["id"]?>, search: $("#search").val() }, _local_createSortable);
	}
	
	function _local_createSortable() {
		<? if ($perm == "p") { ?>
		if ($("#search").val() == "") {
			$("#sort_table").sortable({ items: "li", handle: ".icon_sort", update: function() {
				$.ajax("<?=$aroot?>ajax/auto-modules/views/order/?view=<?=$view["id"]?>&sort=" + escape($("#sort_table").sortable("serialize")));
			}});
		}
		<? } ?>
	}
	
	_local_createSortable();
</script>