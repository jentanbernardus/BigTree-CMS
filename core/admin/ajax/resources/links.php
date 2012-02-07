<?
	$terms = explode(" ",$_POST["query"]);
	
	foreach ($terms as $term) {
		$term = mysql_real_escape_string(strtolower($term));
		$qpart[] = "(LOWER(nav_title) LIKE '%$term%' OR LOWER(title) LIKE '%$term%')";
	}
	
	$q = sqlquery("SELECT * FROM bigtree_pages WHERE ".implode(" AND ",$qpart)." ORDER BY nav_title LIMIT 10");
	while ($f = sqlfetch($q)) {
?>
<a href="<?=str_replace($www_root,$resource_root,$cms->getLink($f["id"]))?>" title="<?=$f["title"]?>"><?=$f["nav_title"]?></a>
<?
	}
?>