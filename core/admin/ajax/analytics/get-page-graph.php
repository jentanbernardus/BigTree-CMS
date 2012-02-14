<?
	// Get the page's path
	$f = sqlfetch(sqlquery("SELECT path FROM bigtree_pages WHERE id = '".mysql_real_escape_string($_POST["page"])."'"));
	$route = "/".$f["path"]."/";
	
	$visits = $admin->getGAViewsByDateRange($_POST["start_date"],$_POST["end_date"],"pagePath==$route");
	
	$min = min($visits);
	$max = max($visits) - $min;

	$bar_height = 100;

	$x = 0;
	foreach ($visits as $date => $count) {
		$height = round($bar_height * ($count - $min) / $max) + 20;
		$x++;
?>
<bar<? if ($x == count($visits)) { ?> class="last"<? } ?> style="height: <?=$height?>px; margin-top: <?=(120-$height)?>px;">
	<date><?=date("n/j/y",strtotime($date))?></date>
	<?=$count?>
</bar>
<?
	}
?>