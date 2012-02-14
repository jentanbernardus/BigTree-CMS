<?
	$breadcrumb[] = array("title" => "Edit Feed", "link" => "#");
	
	$item = $cms->getFeed(end($commands));
	bigtree_clean_globalize_array($item);
?>
<h1><span class="icon_developer_feeds"></span>Edit Feed</h1>
<? include bigtree_path("admin/modules/developer/feeds/_nav.php") ?>

<div class="form_container">
	<form method="post" action="<?=$saroot?>feeds/update/<?=$id?>/" class="module">
		<? include bigtree_path("admin/modules/developer/feeds/_form-content.php") ?>
		<footer>
			<input type="submit" class="button blue" value="Update" />
		</footer>
	</form>
</div>

<? include bigtree_path("admin/modules/developer/feeds/_common-js.php") ?>