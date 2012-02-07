<?
	include bigtree_path("admin/modules/dashboard/analytics/_check.php");
	$breadcrumb[] = array("link" => "dashboard/analytics/service-providers/", "title" => "Service Providers");
?>
<h1><span class="analytics"></span>Service Providers</h1>

<div class="table">
	<summary>
		<p>This report shows the service providers for your visitors in the past 30 days.</p>
	</summary>
	<header>
		<span class="analytics_metric_name">Service Provider</span>
		<span class="analytics_visit_count">Visit Count</span>
		<span class="analytics_view_count">View Count</span>
	</header>
	<ul id="service_providers">
		<li>
			<section class="analytics_metric_name"><loader>Loading...</loader></section>
			<section class="analytics_visit_count">&nbsp;</section>
			<section class="analytics_view_count">&nbsp;</section>
		</li>
	</ul>
</div>

<script type="text/javascript">
	$("#service_providers").load("<?=$aroot?>ajax/analytics/get-service-providers/", { start_date: "<?=date("Y-m-d",strtotime("-30 days"))?>", end_date: "<?=date("Y-m-d")?>" });
</script>