<?
	BigTree::globalizePOSTVars(array("htmlspecialchars","mysql_real_escape_string"));

	$breadcrumb[] = array("title" => "Created View", "href" => "#");

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
	
	if (count($errors)) {	
?>
<h1><span class="icon_developer_modules"></span>View Error</h1>
<div class="form_container">
	<section>
		<? foreach ($errors as $error) { ?>
		<p class="error_message"><?=$error?></p>
		<? } ?>
	</section>
	<footer>
		<a href="javascript: history.back();" class="button white">Back</a>
	</footer>
</div>
<?
	} else {
		// Clean up actions
		$clean_actions = array();
		foreach ($actions as $key => $val) {
			if ($val) {
				$clean_actions[$key] = $val;
			}
		}
		$actions = $clean_actions;
		
		$module = end($path);
		
		// Check to see if there's a default view for the module.  If not our route is going to be blank.
		$landing_exists = $admin->doesModuleLandingActionExist($module);
		if ($landing_exists) {
			if ($suffix) {
				$route = "view-$suffix";
			} else {
				$route = $cms->urlify("view $title");
			}
		} else {
			$route = "";
		}
		
		// Let's create the view

		$actions = mysql_real_escape_string(json_encode($actions));
		$fields = mysql_real_escape_string(json_encode($fields));
		$options = mysql_real_escape_string($_POST["options"]);
		
		sqlquery("INSERT INTO bigtree_module_views (`title`,`description`,`type`,`fields`,`actions`,`table`,`options`,`suffix`,`uncached`,`preview_url`) VALUES ('$title','$description','$type','$fields','$actions','$table','$options','$suffix','$uncached','$preview_url')");
		
		$view_id = sqlid();
		
		$admin->createModuleAction($module,"View $title",$route,"on","icon_small_home",0,$view_id);
		
		$mod = $admin->getModule($module);
?>
<h1><span class="icon_developer_modules"></span>Created View</h1>
<? include BigTree::path("admin/modules/developer/modules/_nav.php"); ?>
<div class="form_container">
	<section>
		<h3 class="action_title">View <?=$title?></h3>
		<p>Your view for <?=$mod["name"]?> has been created. You may continue to create a form for this view or choose to test the view instead.</p>
	</section>
	<footer>
		<a href="<?=$admin_root?><?=$mod["route"]?>/<?=$route?>/" class="button white">Test View</a> &nbsp; 
		<a href="<?=$developer_root?>modules/forms/add/<?=end($path)?>/<?=urlencode($table)?>/<?=urlencode($title)?>/<?=urlencode($suffix)?>/" class="button blue">Add Form</a></p>
	</footer>
</div>
<?
	}
?>