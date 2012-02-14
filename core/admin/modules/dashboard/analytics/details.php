<?
	$user = $cms->getSetting("google-analytics-email");
	$pass = $cms->getSetting("google-analytics-password");
	$profile = $cms->getSetting("google-analytics-profile");
	
	if (!$user || !$pass) {
		header("Location: ".$mroot."setup/");
		die();
	}
	
	if (!$profile) {
		header("Location: ".$mroot."choose-profile/");
		die();
	}
	
	$tw_start = date("Y-m-d",strtotime("-14 days"));
	$tw_end = date("Y-m-d",strtotime("-1 day"));

	$quarter_start = date("Y-m-d",strtotime(date("Y")."-".(date("m") - (date("m") % 3))."-01"));
	$quarter_end = date("Y-m-d");
	
	$year_start = date("Y-01-01");
	$year_end = date("Y-m-d");
	
	$page = $cms->getPage(end($path),false);
?>
<h3>Page Report for: &ldquo;<?=$page["nav_title"]?>&rdquo;</h3>
<graph>
	<header>Two Week Heads-Up <small>(page views)</small></header>
	<data id="graph_data">
		<loader>Loading...</loader>
	</data>
</graph>

<ul class="analytics_columns">
	<li id="ga_current_month">
		<h4>Current Month <small>(<?=date("n/1/Y")?> &mdash; <?=date("n/j/Y")?>)</small></h4>
		<loader>Loading...</loader>
	</li>
	<li id="ga_current_quarter">
		<h4>Current Quarter <small>(<?=date("n/j/Y",strtotime($quarter_start))?> &mdash; <?=date("n/j/Y",strtotime($quarter_end))?>)</small></h4>
		<loader>Loading...</loader>
	</li>
	<li id="ga_current_year">
		<h4>Current Year <small>(<?=date("n/j/Y",strtotime($year_start))?> &mdash; <?=date("n/j/Y",strtotime($year_end))?>)</small></h4>
		<loader>Loading...</loader>
	</li>
</ul>

<script type="text/javascript">
	$("#graph_data").load("<?=$aroot?>ajax/analytics/get-page-graph/", { page: <?=end($path)?>, start_date: "<?=$tw_start?>", end_date: "<?=$tw_end?>" });
	$("#ga_current_month").load("<?=$aroot?>ajax/analytics/get-page-date-range/", { page: <?=end($path)?>, title: "Current Month", start_date: "<?=date("Y-m-01")?>", end_date: "<?=date("Y-m-d")?>" });
	$("#ga_current_quarter").load("<?=$aroot?>ajax/analytics/get-page-date-range/", { page: <?=end($path)?>, title: "Current Quarter", start_date: "<?=$quarter_start?>", end_date: "<?=$quarter_end?>" });
	$("#ga_current_year").load("<?=$aroot?>ajax/analytics/get-page-date-range/", { page: <?=end($path)?>, title: "Current Year", start_date: "<?=$year_start?>", end_date: "<?=$year_end?>" });
</script>