<?
	bigtree_process_post_vars(array("htmlspecialchars","mysql_real_escape_string"));

	$breadcrumb[] = array("title" => "Created View", "href" => "#");
	
	// Clean up actions
	$clean_actions = array();
	foreach ($actions as $key => $val) {
		if ($val) {
			$clean_actions[$key] = $val;
		}
	}
	$actions = $clean_actions;
	
	// Check to see if there's a default view for the module.  If not our route is going to be blank.
	$r = sqlrows(sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '".end($path)."' AND route = ''"));
	if ($r > 0) {
		if ($suffix) {
			$route = "view-$suffix";
		} else {
			$route = $cms->urlify("view $title");
		}
	} else {
		$route = "";
	}
	
	$oroute = $route;
	$x = 2;
	while ($f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '".end($path)."' AND route = '$route'"))) {
		$route = $oroute."-".$x;
		$x++;
	}

	$columns = sqlcolumns($table);
	$errors = array();
	// Check for errors
	if (($type == "draggable" || $type == "draggable-group") && !$columns["position"]) {
		$errors[] = "Sorry, but you can't create a draggable view without a 'position' column in your table.  Please create a position column (integer) in your table and try again.";
	}
	if ($actions["archive"] && !($columns["archived"]["type"] == "char" && $columns["archived"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'archived' that is char(2) in order to use the archive function.";
	}
	if ($actions["approve"] && !($columns["approved"]["type"] == "char" && $columns["approved"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'approved' that is char(2) in order to use the approve function.";
	}
	if ($actions["feature"] && !($columns["featured"]["type"] == "char" && $columns["featured"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'featured' that is char(2) in order to use the feature function.";
	}
	
	// Let's create the view

	$actions = mysql_real_escape_string(json_encode($actions));
	$fields = mysql_real_escape_string(json_encode($fields));
	$options = mysql_real_escape_string($_POST["options"]);
	
	if (count($errors)) {
		echo "<h3>Add Module View</h3>";
		foreach ($errors as $error) {
			echo "<p>".$error."</p>";
		}
	} else {	
		sqlquery("INSERT INTO bigtree_module_views (`title`,`description`,`type`,`fields`,`actions`,`table`,`options`,`suffix`,`uncached`,`preview_url`) VALUES ('$title','$description','$type','$fields','$actions','$table','$options','$suffix','$uncached','$preview_url')");
		
		$vid = sqlid();
		
		sqlquery("INSERT INTO bigtree_module_actions (`module`,`in_nav`,`name`,`route`,`view`,`class`) VALUES ('".end($path)."','on','". mysql_real_escape_string("View $title")."','$route','$vid','icon_small_home')");
		
		$mod = $admin->getModule(end($path));
?>
<h1><span class="icon_developer_modules"></span>Created View</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>
<div class="form_container">
	<section>
		<p>Your view for <?=$mod["name"]?> is now created!<br />You may continue to create a form for this view or choose to test the view instead.</p>
	</section>
	<footer>
		<a href="<?=$aroot?><?=$mod["route"]?>/<?=$route?>/" class="button white">Test View</a> &nbsp; 
		<a href="<?=$saroot?>modules/forms/add/<?=end($path)?>/<?=urlencode($table)?>/<?=urlencode($title)?>/<?=urlencode($suffix)?>/" class="button blue">Add Form</a></p>
	</footer>
</div>
<?
	}
?>