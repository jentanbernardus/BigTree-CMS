<h3 class="foundry">Download Modules</h3>

<div class="search_bar">
	<input type="text" name="query" id="query" value="Search" class="default" autocomplete="off" />
</div>
<div id="results">
	<? include bigtree_path("admin/ajax/developer/foundry/module-page.php") ?>
</div>
<script type="text/javascript">
	var deleteConfirm,deleteTimer,deleteId;
	var searchtimer;
	var mpage = 0;
	var sort = "<?=$view["options"]["sort_column"]?>";
	var sortdir = "<?=$view["options"]["sort_direction"]?>";
	var search = "";
	
	function swapSearch() {
		search = escape($("#query").val());
		$("#results").load("<?=$aroot?>ajax/developer/foundry/module-page/?sort=" + escape(sort) + "&sort_direction=" + escape(sortdir) + "&page=0&search=" + search);
	}
	
	function swapPage(page) {
		mpage = page;
		$("#results").load("<?=$aroot?>ajax/developer/foundry/module-page/?sort=" + escape(sort) + "&sort_direction=" + escape(sortdir) + "&search=" + escape(search) + "&page=" + mpage);
	}
	
	$(".page_numbers a").click(function() {
		ev.stop();
		mpage = BigTree.CleanHref($(this).attr("href"));
		if ($(this).hasClass("active") || $(this).hasClass("disabled")) {
			return;
		}
		
		$("#results").load("<?=$aroot?>ajax/developer/foundry/module-page/?sort=" + escape(sort) + "&sort_direction=" + escape(sortdir) + "&search=" + escape(search) + "&page=" + mpage);
	});
	
	$(".sort_column").click(function() {
		sortdir = BigTree.CleanHref($(this).attr("href"));
		sort = $(this).attr("name");
		mpage = 0;
		$("#results").load("<?=$aroot?>ajax/developer/foundry/module-page/?sort=" + escape(sort) + "&sort_direction=" + escape(sortdir) + "&search=" + escape(search) + "&page=" + mpage);
		
		return false;
	});
	
	$(".button_view").click(function() {
		$.ajax("<?=$aroot?>ajax/developer/foundry/get-module-details/?id=" + BigTree.CleanHref($(this).attr("href")), { complete: function(r) {
			new BigTreeDialog("Field Type Details",r.responseText, function() { },false,true);
		}});
		
		return false;
	});
	
	$(".button_update").click(function() {
		document.location.href = "<?=$aroot?>developer/foundry/update/module/" + BigTree.CleanHref($(this).attr("href")) + "/";
		
		return false;
	});
	
	$(".button_download").click(function() {
		document.location.href = "<?=$aroot?>developer/foundry/install/module/" + BigTree.CleanHref($(this).attr("href")) + "/";
		
		return false;
	});
	
	$("#query").keyup(function() {
		if (searchtimer) {
			clearTimeout(searchtimer);
		}
		searchtimer = setTimeout("swapSearch()",500);
	});
</script>