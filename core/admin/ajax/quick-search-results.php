<?
	$terms = explode(" ",$_POST["query"]);
	
	foreach ($terms as $term) {
		$qpart[] = "(LOWER(nav_title) LIKE '%".mysql_real_escape_string(strtolower($term))."%' OR LOWER(title) LIKE '%".mysql_real_escape_string(strtolower($term))."%')";
	}

	$q = sqlquery("SELECT id,path,nav_title FROM bigtree_pages WHERE ".implode(" AND ",$qpart)." AND archived != 'on' ORDER BY nav_title LIMIT 10");
	if (sqlrows($q) == 0) {
		echo '<p>No Results</p>';
	} else {
		echo '<p>Quick Search Results</p>';
		echo '<ul>';
		while ($f = sqlfetch($q)) {
			$bc = $cms->getBreadcrumbByPage($f);
			$crumbs = array();
			foreach ($bc as $crumb) {
				$crumbs[] = $crumb["title"];
			}
			echo '<li><a href="'.$admin_root."pages/view-tree/".$f["id"].'/" title="'.implode(" &raquo; ",$crumbs).'">'.$f["nav_title"].'</a></li>';
		}
		echo '</ul>';
	}
?>