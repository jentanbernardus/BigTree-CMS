<?
	include bigtree_path("admin/modules/dashboard/analytics/_check.php");
	$breadcrumb[] = array("link" => "dashboard/analytics/traffic-sources/", "title" => "Traffic Sources");
?>
<h1><span class="analytics"></span>Traffic Sources</h1>
<div class="table">
	<summary>
		<p>This report shows the traffic sources for your visitors in the past 30 days.</p>
	</summary>
	<header>
		<span class="analytics_metric_name">Referrer</span>
		<span class="analytics_visit_count">Visit Count</span>
		<span class="analytics_view_count">View Count</span>
	</header>
	<ul id="traffic_sources">
		<li>
			<section class="analytics_metric_name"><loader>Loading</loader></section>
			<section class="analytics_visit_count">&nbsp;</section>
			<section class="analytics_view_count">&nbsp;</section>
		</li>
	</ul>
</div>

<script type="text/javascript">
	$("#traffic_sources").load("<?=$aroot?>ajax/analytics/get-traffic-sources/", { start_date: "<?=date("Y-m-d",strtotime("-30 days"))?>", end_date: "<?=date("Y-m-d")?>" });
</script>