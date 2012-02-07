<?
	$breadcrumb[] = array("title" => "Edit Callout", "link" => "#");
	$callout = $cms->getCalloutById(end($path));
	
	bigtree_clean_globalize_array($callout);
	
	$resources = json_decode($callout["resources"],true);
?>
<h1><span class="icon_developer_callouts"></span>Edit Callout</h1>
<? include bigtree_path("admin/modules/developer/callouts/_nav.php") ?>

<div class="form_container">
	<form method="post" action="<?=$sroot?>update/" enctype="multipart/form-data" class="module">
		<input type="hidden" name="id" value="<?=$callout["id"]?>" />
		<? include bigtree_path("admin/modules/developer/callouts/_form-content.php") ?>
		<footer>
			<input type="submit" class="button blue" value="Update" />
		</footer>
	</form>
</div>

<? include bigtree_path("admin/modules/developer/callouts/_common-js.php") ?>
<script type="text/javascript">
	var resource_count = <?=$x?>;
</script>