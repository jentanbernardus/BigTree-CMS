<?	
	include bigtree_path("admin/modules/dashboard/analytics/_check.php");
	
	$tw_start = date("Y-m-d",strtotime("-14 days"));
	$tw_end = date("Y-m-d",strtotime("-1 day"));

	$quarter_start = date("Y-m-d",strtotime(date("Y")."-".(date("m") - (date("m") % 3))."-01"));
	$quarter_end = date("Y-m-d");
	
	$year_start = date("Y-01-01");
	$year_end = date("Y-m-d");
	
	$breadcrumb[] = array("link" => "dashboard/analytics/", "title" => "Dashboard");
?>
<h1><span class="analytics"></span>Analytics</h1>
<div class="table">
	<summary>
		<h2>Two Week Heads-Up <small>(visits)</small></h2>
	</summary>
	<section>
		<graph>
			<data id="graph_data">
				<loader>Loading...</loader>
			</data>
		</graph>
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