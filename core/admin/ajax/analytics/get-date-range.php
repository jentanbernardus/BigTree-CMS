<?	
	$current = $admin->getGADataByDateRange("browser","-pageviews",$_POST["start_date"],$_POST["end_date"]);
	
	$previous_year_start = date("Y-m-d",strtotime($_POST["start_date"]." -1 year"));
	$previous_year_end = date("Y-m-d",strtotime($_POST["end_date"]." -1 year"));
	
	$past = $admin->getGADataByDateRange("browser","-pageviews",$previous_year_start,$previous_year_end);
	
	if ($past["views"]) {
		$view_growth = (($current["views"]-$past["views"]) / $past["views"]) * 100;
	} else {
		$view_growth = "N/A";
	}
	
	if ($past["visits"]) {
		$visits_growth = (($current["visits"]-$past["visits"]) / $past["visits"]) * 100;
	} else {
		$visits_growth = "N/A";
	}
	
	if ($past["bounce_rate"]) {
		$bounce_growth = $current["bounce_rate"]-$past["bounce_rate"];
	} else {
		$bounce_growth = "N/A";
	}
	
	if ($past["average_time"]) {
		$time_growth = (($current["average_time"]-$past["average_time"]) / $past["average_time"]) * 100;
	} else {
		$time_growth = "N/A";
	}
	
	$c_min = "";
	$c_seconds = $current["average_time"]." second(s)";
	$c_time = $current["average_time"];
	if ($c_time > 60) {
		$c_minutes = floor($c_time / 60);
		$c_seconds = $c_time - ($c_minutes * 60)." second(s)";
		$c_min = $c_minutes." minute(s)";
	}
	$c_time = trim($c_min." ".$c_seconds);
	
	$p_ = "";
	$p_seconds = $past["average_time"]." second(s)";
	$p_time = $past["average_time"];
	if ($p_time > 60) {
		$p_minutes = floor($p_time / 60);
		$p_seconds = $p_time - ($p_minutes * 60)." second(s)";
		$p_min = $p_minutes." minute(s)";
	}
	$p_time = trim($p_min." ".$p_seconds);
	
	//$view_color = "#333";
	if ($view_growth > 5) {
		//$view_color = "green";
		$view_class = 'growth';
	} elseif ($view_growth < -5) {
		//$view_color = "red";
		$view_class = 'warning';
	}
	
	//$visit_color = "#333";
	if ($visits_growth > 5) {
		//$visit_color = "green";
		$visit_class = 'growth';
	} elseif ($visits_growth < -5) {
		//$visit_color = "red";
		$visit_class = 'warning';
	}
	
	//$time_color = "#333";
	if ($time_growth > 5) {
		//$time_color = "green";
		$time_class = "growth";
	} elseif ($time_growth < -5) {
		//$time_color = "red";
		$time_class = "warning";
	}
	
	//$bounce_color = "#333";
	if ($bounce_growth < -2) {
		//$bounce_color = "green";
		$bounce_class = 'growth';
	} elseif ($bounce_growth > 2) {
		//$bounce_color = "red";
		$bounce_class = 'warning';
	}
?>
<summary><?=$_POST["title"]?> <small>(<?=date("n/j/Y",strtotime($_POST["start_date"]))?> &mdash; <?=date("n/j/Y",strtotime($_POST["end_date"]))?>)</small></summary>
<set>
	<data>
		<header>Views</header>
		<percentage class="<?=$view_class?>"><?=number_format($view_growth,2)?>%</percentage>
		<label>Present</label>
		<value><?=number_format($current["views"])?></value>
		<label>Year-ago</label>
		<value><?=number_format($past["views"])?></value>
	</data>
</set>
<set>
	<data>
		<header>Visits</header>
		<percentage class="<?=$visit_class?>"><?=number_format($visits_growth,2)?>%</percentage>
		<label>Present</label>
		<value><?=number_format($current["visits"])?></value>
		<label>Year-ago</label>
		<value><?=number_format($past["visits"])?></value>
	</data>
</set>
<set>
	<data>
		<header>Average Time on Site</header>
		<percentage class="<?=$time_class?>"><?=number_format($time_growth,2)?>%</percentage>
		<label>Present</label>
		<value><?=$c_time?></value>
		<label>Year-ago</label>
		<value><?=$p_time?></value>
	</data>
</set>
<set>
	<data>
		<header>Bounce Rate</header>
		<percentage class="<?=$bounce_class?>"><?=number_format($bounce_growth,2)?>%</percentage>
		<label>Present</label>
		<value><?=number_format($current["bounce_rate"],2)?>%</value>
		<label>Year-ago</label>
		<value><?=number_format($past["bounce_rate"],2)?>%</value>
	</data>
</set>