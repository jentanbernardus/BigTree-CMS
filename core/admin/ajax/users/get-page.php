<?
	$admin->requireLevel(1);
	
	$query = isset($_GET["query"]) ? $_GET["query"] : "";
	$page = isset($_GET["page"]) ? $_GET["page"] : 0;

	$pages = $admin->getUsersPageCount($query);
	$results = $admin->getPageOfUsers($page,$query);
	
	foreach ($results as $item) {
?>
<li>
	<section class="users_name"><?=$item["name"]?></section>
	<section class="users_email"><?=$item["email"]?></section>
	<section class="users_company"><?=$item["company"]?></section>
	<section class="view_action">
		<? if ($admin->Level >= $item["level"]) { ?>
		<a href="<?=$aroot?>users/edit/<?=$item["id"]?>/" class="icon_edit"></a>
		<? } else { ?>
		<span class="icon_disabled tooltip" tooltip="You may not edit users with higher permission levels than you."></span>
		<? } ?>
	</section>
	<section class="view_action">
		<? if ($admin->Level >= $item["level"]) { ?>
		<a href="#<?=$item["id"]?>" class="icon_delete"></a>
		<? } else { ?>
		<span class="icon_disabled tooltip" tooltip="You may not delete users with higher permission levels than you."></span>
		<? } ?>
	</section>
</li>
<?
	}
?>
<script type="text/javascript">
	BigTree.SetPageCount(<?=$pages?>,<?=$page?>);
</script>