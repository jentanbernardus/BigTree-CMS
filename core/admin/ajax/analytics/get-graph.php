<?
	$visits = $admin->getGAVisitsByDateRange($_POST["start_date"],$_POST["end_date"]);
	
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