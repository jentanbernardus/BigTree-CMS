<?
	$data = $admin->getGADataByDateRange("networkLocation","-pageviews",$_POST["start_date"],$_POST["end_date"]);
	foreach ($data["results"] as $provider => $result) {
?>
<li>
	<section class="analytics_metric_name"><?=ucwords($provider)?></section>
	<section class="analytics_visit_count"><?=$result["visits"]?></section>
	<section class="analytics_view_count"><?=$result["views"]?></section>
</li>
<?
	}
?>