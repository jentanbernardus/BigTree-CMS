<?
	$mroot = $aroot."dashboard/analytics/";

	$breadcrumb = array(
		array("link" => "dashboard/", "title" => "Pages"),
		array("link" => "dashboard/analytics/", "title" => "Analytics")
	);
	
	$user = $cms->getSetting("google-analytics-email");
	$pass = $cms->getSetting("google-analytics-password");
	$profile = $cms->getSetting("google-analytics-profile");
?>
<nav class="tertiary">
	<ul>
		<li><a href="<?=$mroot?>"><span class="icon_small icon_small_home"></span>Dashboard</a></li>
		<li><a href="<?=$mroot?>service-providers/"><span class="icon_small icon_small_transmit"></span>Service Providers</a></li>
		<li><a href="<?=$mroot?>traffic-sources/"><span class="icon_small icon_small_car"></span>Traffic Sources</a></li>
		<li><a href="<?=$mroot?>keywords/"><span class="icon_small icon_small_key"></span>Keywords</a></li>
	</ul>
</div>