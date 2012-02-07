<?
	$breadcrumb[] = array("title" => "Created Feed", "link" => "#");
	
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));
		
	$options = json_decode($_POST["options"],true);
	if (is_array($options)) {
		foreach ($options as &$option) {
			$option = str_replace($www_root,"{wwwroot}",$option);
		}
	}
	
	// Get a unique route!
	$route = $cms->urlify($_POST["name"]);
	$x = 2;
	$oroute = $route;
	$f = $admin->getFeedByRoute($route);
	while ($f) {
		$route = $oroute."-".$x;
		$f = $admin->getFeedByRoute($route);
		$x++;
	}
	
	$fields = mysql_real_escape_string(json_encode($_POST["fields"]));
	$options = mysql_real_escape_string(json_encode($options));
	
	sqlquery("INSERT INTO bigtree_feeds (`route`,`name`,`description`,`type`,`table`,`fields`,`options`) VALUES ('$route','$name','$description','$type','$table','$fields','$options')");
	
?>
<h1><span class="icon_developer_feeds"></span>Created Feed</h1>
<p>Your feed is accessible at: <a href="<?=$www_root?>feeds/<?=$route?>/"><?=$www_root?>feeds/<?=$route?>/</a></p>