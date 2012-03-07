<?
	$admin->requireLevel(1);
	
	$query = isset($_GET["query"]) ? $_GET["query"] : "";
	$page = isset($_GET["page"]) ? $_GET["page"] : 0;

	$pages = $admin->getTokensPageCount($query);
	$results = $admin->getPageOfTokens($page,$query);
	
	foreach ($results as $item) {
?>
<li>
	<section class="users_name"><?=$item["user"]["email"]?></section>
	<section class="users_api_type">
		<? if ($item["temporary"]) { ?>Temporary<? } else { ?>No Expiration<? } ?>
		/ <? if ($item["readonly"]) { ?>Read Only<? } else { ?>Full Access<? } ?>
	</section>
	<section class="users_api_token"><?=$item["token"]?></section>
	<section class="view_action"><a href="<?=$admin_root?>users/edit-token/<?=$item["id"]?>/" class="icon_edit"></a></section>
	<section class="view_action"><a href="#<?=$item["id"]?>" class="icon_delete"></a></section>
</li>
<?
	}
?>
<script type="text/javascript">
	BigTree.SetPageCount("#view_paging",<?=$pages?>,<?=$page?>);
</script>