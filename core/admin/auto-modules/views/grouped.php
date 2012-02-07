<div class="table">
	<summary>
		<input type="search" class="form_search" id="search" placeholder="Search" />
	</summary>
	<article class="table" id="table_contents">
		<? include bigtree_path("admin/ajax/auto-modules/views/grouped.php") ?>
	</article>
</div>

<? include bigtree_path("admin/auto-modules/views/_common-js.php") ?>
<script type="text/javascript">
	function reSearch() {
		$("#table_contents").load("<?=$aroot?>ajax/auto-modules/views/grouped/", { view: <?=$view["id"]?>, search: $("#search").val() }, _local_refreshSort);
	}

	function _local_refreshSort() {
		<? if ($perm == "p" && $o["draggable"]) { ?>
		$("#table_contents ul").each(function() {
			if ($("#search").val() == "") {
				$(this).sortable({ items: "li", handle: ".icon_sort", update: $.proxy(function() {
					$.ajax("<?=$aroot?>ajax/auto-modules/views/order/?view=<?=$view["id"]?>&table_name=" + $(this).attr("id") + "&sort=" + escape($(this).sortable("serialize")));
				},this) });
			}
		});
		<? } ?>
	}
</script>