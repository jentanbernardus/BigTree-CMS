<?
	$count = $_POST["count"];
	$items = array();
	$q = sqlquery("SELECT * FROM bigtree_callouts ORDER BY position DESC, id ASC");
	while ($f = sqlfetch($q)) {
		$items[] = $f;
	}
	
	$type = $items[0]["id"];
?>
<div id="callout_type">
	<fieldset>
		<label>Callout Type</label>
		<? if (count($items) > 0) { ?>
		<select name="callouts[<?=$count?>][type]">
			<? foreach ($items as $item) { ?>
			<option value="<?=htmlspecialchars($item["id"])?>"><?=htmlspecialchars($item["name"])?></option>
			<? } ?>
		</select>
		<? } else { ?>
		<p>(No callouts available)</p>
		<? } ?>
	</fieldset>
</div>
<div id="callout_resources">
	<? include bigtree_path("admin/ajax/pages/callout-resources.php") ?>
</div>

<script type="text/javascript">
	BigTreeCustomControls();
	
	$("#callout_type select").bind("select:changed",function(event,data) {
		$("#callout_resources").load("<?=$admin_root?>ajax/pages/callout-resources/", { type: data.value, count: <?=$count?> });
	});
</script>