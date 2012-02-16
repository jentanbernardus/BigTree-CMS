<?
	include bigtree_path($relative_path."_check.php");
	
	$breadcrumb[] = array("link" => "dashboard/analytics/", "title" => "Traffic Report");
	
	$cache = $cms->getSetting("bigtree-internal-google-analytics-cache");
	
	$two_week_visits = $cache["two_week"];
	$graph_min = min($two_week_visits);
	$graph_max = max($two_week_visits) - $graph_min;
	$graph_bar_height = 70;
?>
<h1><span class="analytics"></span>Analytics</h1>
<? include bigtree_path($relative_path."_nav.php"); ?>
<div class="table">
	<summary>
		<h2>Two Week Heads-Up <small>(visits)</small></h2>
	</summary>
	<section>
		<div class="graph">
			<?
				$x = 0;
			    foreach ($two_week_visits as $date => $count) {
			    	$height = round($graph_bar_height * ($count - $graph_min) / $graph_max) + 12;
			    	$x++;
			?>
			<section class="bar<? if ($x == 14) { ?> last<? } elseif ($x == 1) { ?> first<? } ?>" style="height: <?=$height?>px; margin-top: <?=(82-$height)?>px;">
			    <?=$count?>
			</section>
			<?
			    }
			    
			    $x = 0;
			    foreach ($two_week_visits as $date => $count) {
			    	$x++;
			?>
			<section class="date<? if ($x == 14) { ?> last<? } elseif ($x == 1) { ?> first<? } ?>"><?=date("n/j/y",strtotime($date))?></section>
			<?
			    }
			?>
		</div>
	</section>
</div>

<ul class="analytics_columns">
	<li id="ga_current_month">
		<summary>Current Month <small>(<?=date("n/1/Y")?> &mdash; <?=date("n/j/Y")?>)</small></summary>
		<loader>Loading...</loader>
	</li>
	<li id="ga_current_quarter">
		<summary>Current Quarter <small>(<?=date("n/j/Y",strtotime($quarter_start))?> &mdash; <?=date("n/j/Y",strtotime($quarter_end))?>)</small></summary>
		<loader>Loading...</loader>
	</li>
	<li id="ga_current_year">
		<summary>Current Year <small>(<?=date("n/j/Y",strtotime($year_start))?> &mdash; <?=date("n/j/Y",strtotime($year_end))?>)</small></summary>
		<loader>Loading...</loader>
	</li>
</ul>

<script type="text/javascript">
	$("#graph_data").load("<?=$aroot?>ajax/analytics/get-graph/", { start_date: "<?=$tw_start?>", end_date: "<?=$tw_end?>" });
	$("#ga_current_month").load("<?=$aroot?>ajax/analytics/get-date-range/", { title: "Current Month", start_date: "<?=date("Y-m-01")?>", end_date: "<?=date("Y-m-d")?>" });
	$("#ga_current_quarter").load("<?=$aroot?>ajax/analytics/get-date-range/", { title: "Current Quarter", start_date: "<?=$quarter_start?>", end_date: "<?=$quarter_end?>" });
	$("#ga_current_year").load("<?=$aroot?>ajax/analytics/get-date-range/", { title: "Current Year", start_date: "<?=$year_start?>", end_date: "<?=$year_end?>" });
</script>