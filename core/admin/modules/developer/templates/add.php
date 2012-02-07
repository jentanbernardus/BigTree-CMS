<?
	$resources = array();
?>
<h1><span class="icon_developer_templates"></span>Add Template</h1>
<? include bigtree_path("admin/modules/developer/templates/_nav.php") ?>

<div class="form_container">
	<form method="post" action="<?=$saroot?>templates/create/" enctype="multipart/form-data" class="module">
		<? include bigtree_path("admin/modules/developer/templates/_form-content.php") ?>
		<footer>
			<input type="submit" class="button blue" value="Create" />
		</footer>
	</form>
</div>
<? include bigtree_path("admin/modules/developer/templates/_common-js.php") ?>