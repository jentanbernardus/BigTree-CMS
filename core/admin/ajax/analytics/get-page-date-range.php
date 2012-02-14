<?
	// Get the page's path
	$f = sqlfetch(sqlquery("SELECT path FROM bigtree_pages WHERE id = '".mysql_real_escape_string($_POST["page"])."'"));
	$route = "/".$f["path"]."/";
	
	$current = $admin->getGADataByDateRange("browser","-pageviews",$_POST["start_date"],$_POST["end_date"],10000,"pagePath==$route");
	
	$previous_year_start = date("Y-m-d",strtotime($_POST["start_date"]." -1 year"));
	$previous_year_end = date("Y-m-d",strtotime($_POST["end_date"]." -1 year"));
	
	$past = $admin->getGADataByDateRange("browser","-pageviews",$previous_year_start,$previous_year_end,10000,"pagePath==$route");
		
	$view_growth = (($current["views"]-$past["views"]) / $past["views"]) * 100;
	
	$view_color = "#333";
	if ($view_growth > 5) {
		$view_color = "green";
	} elseif ($view_growth < -5) {
		$view_color = "red";
	}
?>
<h4><?=$_POST["title"]?> <small>(<?=date("n/j/Y",strtotime($_POST["start_date"]))?> &mdash; <?=date("n/j/Y",strtotime($_POST["end_date"]))?>)</small></h4>
<set>
	<data>
		<header>Views</header>
		<percentage style="color: <?=$view_color?>"><?=number_format($view_growth,2)?>%</percentage>
		<label>Present</label>
		<value><?=number_format($current["views"])?></value>
		<label>Year-ago</label>
		<value><?=number_format($past["views"])?></value>
	</data>
</set>