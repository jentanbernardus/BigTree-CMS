<?
	include bigtree_path("admin/modules/dashboard/analytics/_check.php");
	$breadcrumb[] = array("link" => "dashboard/analytics/keywords/", "title" => "Keywords");
?>
<h1><span class="analytics"></span>Keywords</h1>
<div class="table">
	<summary>
		<p>This report shows the search keywords for your visitors in the past 30 days.</p>
	</summary>
	<header>
		<span class="analytics_metric_name">Keyword</span>
		<span class="analytics_visit_count">Visit Count</span>
		<span class="analytics_view_count">View Count</span>
	</header>
	<ul id="keywords_found">
		<li>
			<section class="analytics_metric_name"><loader>Loading</loader></section>
			<section class="analytics_visit_count">&nbsp;</section>
			<section class="analytics_view_count">&nbsp;</section>
		</li>
	</ul>
</div>

<script type="text/javascript">
	$("#keywords_found").load("<?=$aroot?>ajax/analytics/get-keywords/", { start_date: "<?=date("Y-m-d",strtotime("-30 days"))?>", end_date: "<?=date("Y-m-d")?>" });
</script>