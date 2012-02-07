<?
	error_reporting(0);
	
	// First we need to package the file so they can download it manually if they wish.
	if (!is_writable($server_root."cache/")) {
		die("Please make the cache/ directory writable.");
	}
	
	if ($_POST["package"]) {
		$package_details = $admin->getModulePackage($_POST["package"]);
		if ($package_details["group_id"]) {
			$group_details = $admin->getModuleGroup($package_details["group_id"]);
			$modules = $admin->getModulesByGroup($package_details["group_id"]);
			$index = $group_details["name"]." Module(s)\n";
			$index .= "Packaged for BigTree ".$GLOBALS["bigtree"]["version"]." by ".$admin->Name."\n";
			$index .= "Group::||::".json_encode($group_details)."\n";
		} else {
			$module_details = $admin->getModule($package_details["module_id"]);
			$index = $module_details["name"]." Module\n";
			$index .= "Packaged for BigTree ".$GLOBALS["bigtree"]["version"]." by ".$admin->Name."\n";
			$modules = array($module_details);
		}
	} elseif ($_POST["module"]) {
		$module_details = $admin->getModule($_POST["module"]);
		$index = $module_details["name"]." Module\n";
		$index .= "Packaged for BigTree ".$GLOBALS["bigtree"]["version"]." by ".$admin->Name."\n";
		$modules = array($module_details);
		$file_name = $cms->urlify($module_details["name"]);
		$package_name = $module_details["name"];
	} elseif ($_POST["group"]) {
		$group_details = $admin->getModuleGroup($_POST["group"]);
		$modules = $admin->getModulesByGroup($group_details["id"]);
		$index = $group_details["name"]." Module(s)\n";
		$index .= "Packaged for BigTree ".$GLOBALS["bigtree"]["version"]." by ".$admin->Name."\n";
		$index .= "Group::||::".json_encode($group_details)."\n";
		$file_name = $cms->urlify($group_details["name"]);
		$package_name = $group_details["name"];
	}
	
	// Clear the cache area to build the package.
	$dir = $server_root."cache/packager/";
	exec("rm -rf ".$dir);
	unlink($server_root."cache/package.tar.gz");
	mkdir($server_root."cache/packager");
	$x = 0;
	
	// If someone accidentally added something twice, remove the duplicates.
	foreach ($_POST as $key => $val) {
		if (is_array($val)) {
			$_POST[$key] = array_unique($val);
		}
	}
	
	foreach ($modules as $item) {
		// Do stuff to dump databases here.
		$index .= "Module::||::".json_encode($item)."\n";
		// Find the actions for the module
		$actions = $admin->getModuleActions($item["id"]);
		foreach ($actions as $a) {
			// If there's an auto module, include it as well.
			if ($a["form"]) {
				$form = $autoModule->getForm($a["form"]);
				$index .= "ModuleForm::||::".json_encode($form)."\n";
			}
			if ($a["view"]) {
				$view = $autoModule->getView($a["view"]);
				$index .= "ModuleView::||::".json_encode($view)."\n";
			}
			// Draw Action after the form/view since we'll need to know the form/view ID to create the action.
			$index .= "Action::||::".json_encode($a)."\n";
		}
	}
	
	// Get the templates we're passing in.
	foreach ($_POST["templates"] as $template) {
		$item = $cms->getTemplateById($template);
		$index .= "Template::||::".json_encode($item)."\n";
		
		// If we're bringing over a module template, copy the whole darn folder.
		if (substr($template,0,7) == "module-") {
			$tname = substr($template,7);
			recurseFileDirectory($server_root."templates/modules/$tname/"); 
		} else {
			$x++;
			copy($server_root."templates/pages/$template.php",$dir."$x.part.bpz");
			$index .= "File::||::$x.part.bpz::||::templates/pages/$template.php::||::Template\n";
		}
	}
	
	// Get the callouts we're passing in.
	foreach ($_POST["callouts"] as $callout) {
		$item = $cms->getCalloutById($callout);
		$index .= "Callout::||::".json_encode($item)."\n";
		$x++;
		$index .= "File::||::$x.part.bpz::||::templates/callouts/$callout.php\n";
		copy($server_root."templates/callouts/$callout.php",$dir."$x.part.bpz");
	}
	
	// Get the feeds
	foreach ($_POST["feeds"] as $feed) {
		$item = $cms->getFeedById($feed);
		$index .= "Feed::||::".json_encode($item)."\n";
	}
	
	// Get the settings
	foreach ($_POST["settings"] as $setting) {
		$item = $admin->getSettingById($setting);
		$index .= "Setting::||::".json_encode($item)."\n";
	}
	
	// Get the included tables now... yep.
	foreach ($_POST["tables"] as $t) {
		$x++;
		list($table,$type) = explode("#",$t);
		$f = sqlfetch(sqlquery("SHOW CREATE TABLE `$table`"));
		$create = str_replace(array("\r","\n")," ",end($f)).";\n";
		if ($type == "structure") {
			if (strpos($create,"AUTO_INCREMENT=") !== false) {
				$pos = strpos($create,"AUTO_INCREMENT=");
				$part1 = substr($create,0,$pos);
				$part2 = substr($create,strpos($create," ",$pos)+1);
				$create = $part1.$part2;
			}
		} else {
			$q = sqlquery("SELECT * FROM `$table` ORDER BY id ASC");
			while ($f = sqlfetch($q)) {
				$fields = array();
				$values = array();
				foreach ($f as $key => $val) {
					$fields[] = "`$key`";
					$values[] = '"'.mysql_real_escape_string(str_replace(array("\r","\n")," ",$val)).'"';
				}
				$create .= "INSERT INTO `$table` (".implode(",",$fields).") VALUES (".implode(",",$values).");\n";
			}
		}
		file_put_contents($dir."$x.part.bpz",$create);
		$index .= "SQL::||::$table::||::$x.part.bpz\n";
	}
	
	
	// Copy all the class files over...
	foreach ($_POST["class_files"] as $file) {
		$x++;
		// This is a module class, replace var $Module = "[num]";
		$module_for_file = false;
		foreach ($_POST["class_files"] as $mid => $cfn) {
			if ($cfn == $file)
				$module_for_file = $mid;
		}
		$index .= "ClassFile::||::$x.part.bpz::||::$file::||::$module_for_file\n";
		copy($server_root.$file,$dir."$x.part.bpz");	
	}
	
	// Copy all the required files over...
	foreach ($_POST["required_files"] as $file) {
		$x++;
		$index .= "File::||::$x.part.bpz::||::$file::||::Required\n";
		copy($server_root.$file,$dir."$x.part.bpz");	
	}
	
	// Copy all the other files over...
	foreach ($_POST["other_files"] as $file) {
		$x++;
		$index .= "File::||::$x.part.bpz::||::$file::||::Other\n";
		copy($server_root.$file,$dir."$x.part.bpz");	
	}
	
	file_put_contents($dir."index.bpz",$index);
	exec("cd $dir; tar -zcf $server_root"."cache/package.tar.gz *");
	
	// Create the saved copy of this creation.
	bigtree_process_post_vars(array("mysql_real_escape_string"));
	
	$details = mysql_real_escape_string(json_encode(array(
		"tables" => $tables,
		"class_files" => $class_files,
		"required_files" => $required_files,
		"other_files" => $other_files,
		"templates" => $templates,
		"callouts" => $callouts,
		"feeds" => $feeds,
		"settings" => $settings
	)));
	
	// Let's see if this stuff is already a package.
	if ($package) {
		$data = $admin->getModulePackage($package);
		sqlquery("UPDATE bigtree_module_packages SET details = '$details', last_updated = NOW() WHERE id = '$package'");
		bigtree_clean_globalize_array($data,array("htmlspecialchars"));
	} else {
		$author = mysql_real_escape_string($admin->Name);
		sqlquery("INSERT INTO bigtree_module_packages (`author`,`name`,`primary_version`,`secondary_version`,`tertiary_version`,`description`,`release_notes`,`details`,`group_id`,`module_id`,`last_updated`) VALUES ('$author','$package_name','1','0','0','','','$details','".$group["id"]."','".$module["id"]."',NOW())");
		$package = sqlid();
		$name = $package_name;
		$primary_version = 1;
		$secondary_version = 0;
		$tertiary_version = 0;
	}
	
	// Move the file into place.
	bigtree_move($server_root."cache/package.tar.gz",$server_root."cache/packages/".$package.".tar.gz");
?>
<h3 class="foundry">Package Module: Details</h3>
<form method="post" action="<?=$aroot?>developer/foundry/package/submit/module/" class="module">
	<input type="hidden" name="id" value="<?=$package?>" />
	<fieldset>
		<label class="required">Name</label>
		<input type="text" class="required" name="name" value="<?=$name?>" />
	</fieldset>
	<fieldset>
		<label>Private <small>(if you wish to only make this module accessible when logged into your Foundry account check this box)</small></label>
		<input type="checkbox" name="private" <? if ($private) { ?>checked="checked" <? } ?>/>
	</fieldset>
	<fieldset>
		<label>Version</label>
		<input type="text" name="primary_version" value="<?=$primary_version?>" class="field_type_version" />.<input type="text" name="secondary_version" value="<?=$secondary_version?>" class="field_type_version" />.<input type="text" name="tertiary_version" value="<?=$tertiary_version?>" class="field_type_version" />
	</fieldset>
	<fieldset>
		<label>Description</label>
		<textarea name="description"><?=$description?></textarea>
	</fieldset>
	<fieldset>
		<label>Release Notes</label>
		<textarea name="release_notes"><?=$release_notes?></textarea>
	</fieldset>
	
	<div id="form_error" style="display: none;">
		<p>Please check the marked fields for errors.</p>
	</div>
	<input type="submit" class="button white" value="Submit to Foundry" />
</form>

<?
	// Function used for template directory inclusion:
	function recurseFileDirectory($directory) {
		global $x,$index,$server_root,$tname,$dir;
		$o = opendir($directory);
		while ($r = readdir($o)) {
			if ($r != "." && $r != "..") {
				if (is_dir($directory.$r)) {
					recurseFileDirectory($directory.$r."/");
				} else {
					$x++;
					$index .= "File::||::$x.part.bpz::||::".str_replace($server_root,"",$directory)."$r::||::Template\n";
					copy($directory.$r,$dir."$x.part.bpz");
				}
			}
		}
	}
?>