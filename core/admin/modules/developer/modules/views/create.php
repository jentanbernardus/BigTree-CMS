<?
	BigTree::globalizePOSTVars();

	$breadcrumb[] = array("title" => "Created View", "href" => "#");

	$options = json_decode($options,true);

	$table_description = @BigTree::describeTable($table);
	$columns = $table_description["columns"];
	$errors = array();
	// Check for errors
	if (($type == "draggable" || $type == "draggable-group" || $options["draggable"]) && !$columns["position"]) {
		$errors[] = "Sorry, but you can't create a draggable view without a 'position' column in your table.  Please create a position column (integer) in your table and try again.";
	}
	if (isset($actions["archive"]) && !(($columns["archived"]["type"] == "char" || $columns["archived"]["type"] == "varchar") && $columns["archived"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'archived' that is char(2) in order to use the archive function.";
	}
	if (isset($actions["approve"]) && !(($columns["approved"]["type"] == "char" || $columns["approved"]["type"] == "varchar") && $columns["approved"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'approved' that is char(2) in order to use the approve function.";
	}
	if (isset($actions["feature"]) && !(($columns["featured"]["type"] == "char" || $columns["featured"]["type"] == "varchar") && $columns["featured"]["size"] == "2")) {
		$errors[] = "Sorry, but you must have a column named 'featured' that is char(2) in order to use the feature function.";
	}

	if (count($errors)) {
		$_SESSION["bigtree"]["developer"]["saved_view"] = $_POST;
?>
<h1><span class="icon_developer_modules"></span>View Creation Error</h1>
<div class="form_container">
	<section>
		<? foreach ($errors as $error) { ?>
		<p><?=$error?></p>
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
		if (isset($actions)) {
			foreach ($actions as $key => $val) {
				if ($val) {
					$clean_actions[$key] = $val;
				}
			}
		}
		$actions = $clean_actions;

		$module = end($bigtree["path"]);

		// Silence notices
		$suffix = isset($suffix) ? $suffix : "";

		// Check to see if there's a default view for the module. If not our route is going to be blank.
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
		$view_id = $admin->createModuleView($title,$description,$table,$type,$options,$fields,$actions,$suffix,$preview_url);
		$admin->createModuleAction($module,"View $title",$route,"on","list",0,$view_id);

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
		<a href="<?=ADMIN_ROOT?><?=$mod["route"]?>/<? if ($route) { echo $route."/"; } ?>" class="button white">Test View</a> &nbsp;
		<a href="<?=$developer_root?>modules/forms/add/<?=end($bigtree["path"])?>/<?=urlencode($table)?>/<?=urlencode($title)?>/<?=urlencode($suffix)?>/<?=$view_id?>/" class="button blue">Add Form</a></p>
	</footer>
</div>
<?
	}
?>