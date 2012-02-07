<?
	// Get all the ancestors
	$bc = $cms->getBreadcrumbByPage($pdata);
	$ancestors = array();
	foreach ($bc as $item) {
		$ancestors[] = $item["id"];
	}
	
	function _local_drawNavLevel($parent,$depth,$ancestors) {
		global $permissions,$pdata,$page;
		$q = sqlquery("SELECT * FROM bigtree_pages WHERE parent = '$parent' AND archived != 'on' ORDER BY nav_title ASC");
		$r = sqlrows($q);
		if ($r) {
?>
<ul class="depth_<?=$depth?>"<? if ($depth > 2 && !in_array($parent,$ancestors)) { ?> style="display: none;"<? } ?>>
	<?
		$x = 0;
		while ($f = sqlfetch($q)) {
			$x++;
			$r = sqlrows(sqlquery("SELECT id FROM bigtree_pages WHERE parent = '".$f["id"]."' AND archived != 'on'"));
			
			if ($f["id"] != $page) {
	?>
	<li>
		<span class="depth"></span>
		<a class="title<? if ($f["id"] == $pdata["parent"]) { ?> active<? } ?><? if (in_array($f["id"],$ancestors)) { ?> expanded<? } ?>" href="#<?=$f["id"]?>"><?=$f["nav_title"]?></a>
		<? _local_drawNavLevel($f["id"],$depth + 1,$ancestors) ?>
	</li>
	<?
			}
		}
	?>
</ul>
<?
		}
	}
?>

<fieldset>
	<input type="hidden" name="parent" value="<?=$pdata["parent"]?>" id="page_parent" />
	<label>Select New Parent</label>
	<div class="move_page form_table">
		<div class="labels">
			<span class="page_label">Page</span>
		</div>
		<section>
			<ul class="depth_1">
				<li class="top">
					<span class="depth"></span>
					<a class="title expanded<? if ($pdata["parent"] == 0) { ?> active<? } ?>" href="#0">Top Level</a>
					<? _local_drawNavLevel(0,2,$ancestors,$pdata["parent"]) ?>
				</li>
		</section>
	</div>
</fieldset>


<script type="text/javascript">
	$(".move_page .title").click(function() {
		if ($(this).hasClass("disabled")) {
			return false;
		}
		
		$(".move_page .title").removeClass("active");
		$(this).addClass("active");
		
		id = $(this).attr("href").substr(1);
		$("#page_parent").val(id);
		
		if (id == 0) {
			return false;
		}
			
		if ($(this).hasClass("expanded")) {
			if ($(this).nextAll("ul")) {
				$(this).nextAll("ul").hide();
			}
			$(this).removeClass("expanded");
		} else {
			if ($(this).nextAll("ul").length) {
				if ($(this).nextAll("ul")) {
					$(this).nextAll("ul").show();
				}
				$(this).addClass("expanded");
			}
		}
		
		return false;
	});
</script>