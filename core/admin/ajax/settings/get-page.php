<?
	$admin->requireLevel(1);
	
	$query = isset($_GET["query"]) ? $_GET["query"] : "";
	$page = isset($_GET["page"]) ? $_GET["page"] : 0;

	$pages = $admin->getSettingsPageCount($query);
	$results = $admin->getPageOfSettings($page,$query);
	
	foreach ($results as $item) {
?>
<li>
	<section class="settings_name"><?=$item["name"]?></section>
	<section class="settings_value"><?=smarter_trim(strip_tags($item["value"]),100)?></section>
	<section class="view_action"><a href="<?=$aroot?>settings/edit/<?=$item["id"]?>/" class="icon_edit"></a></section>
</li>
<?
	}
?>
<script type="text/javascript">
	BigTree.SetPageCount("#view_paging",<?=$pages?>,<?=$page?>);
</script>