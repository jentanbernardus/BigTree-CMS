<?
	$cr = $server_root."cache/unpack/";
	$index = file_get_contents($cr."index.bpz");
	$lines = explode("\n",$index);
	$module_name = $lines[0];
	$package_info = $lines[1];
	$group_id = 0;
	$data = json_decode($_POST["details"],true);
	$package_id = mysql_real_escape_string($data["id"]);
	$package_files = array();
	$package_tables = array();
	$module_match = array();
	$route_match = array();
		
	// Saved information for managing these packages later.
	$savedData["tables"] = array();
	$savedData["class_files"] = array();
	$savedData["required_files"] = array();
	$savedData["files"] = array();
	$savedData["templates"] = array();
	$savedData["callouts"] = array();
	$savedData["settings"] = array();
	$savedData["feeds"] = array();
	
	next($lines);
	next($lines);
	foreach ($lines as $line) {
		$parts = explode("::||::",$line);
		$type = $parts[0];
		$data = json_decode($parts[1],true);
		
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				if (substr($key,0,1) != "_") {
					if ($key != "type") {
						if (is_array($val)) {
							$$key = mysql_real_escape_string(json_encode($val));
						} else {
							$$key = mysql_real_escape_string($val);
						}
					}
				}
			}
		}
		
		if ($type == "Group") {
			$existing = $admin->getModuleGroupByName($name);
			if ($existing) {
				$group_id = $existing["id"];
			} else {
				sqlquery("INSERT INTO bigtree_module_groups (`name`,`package`) VALUES ('$name','$package_id')");
				$group_id = sqlid();
			}
		}
		
		// Import the Module
		if ($type == "Module") {
			// Get a unique route
			$oroute = $route;
			$x = 2;
			while (sqlrows(sqlquery("SELECT * FROM bigtree_modules WHERE route = '$route'"))) {
				$route = $oroute."-".$x;
				$x++;
			}
			if ($route != $oroute) {
				$route_match["custom/admin/$oroute/"] = "custom/admin/$route/";
			}
			sqlquery("INSERT INTO bigtree_modules (`name`,`description`,`image`,`route`,`class`,`group`,`package`) VALUES ('$name','$description','$image','$route','$class','$group_id','$package_id')");
			$module_match[$id] = sqlid();
			$module_id = sqlid();
		}
		
		// Import a Module Action
		if ($type == "Action") {
			if ($form)
				$form = $last_form_id;
			if ($view)
				$view = $last_view_id;
			sqlquery("INSERT INTO bigtree_module_actions (`module`,`name`,`route`,`in_nav`,`view`,`form`,`class`,`position`) VALUES ('$module_id','$name','$route','$in_nav','$view','$form','$class','$position')");
		}
		
		// Import a Module Form
		if ($type == "ModuleForm") {
			sqlquery("INSERT INTO bigtree_module_forms (`title`,`javascript`,`css`,`callback`,`table`,`fields`,`positioning`) VALUES ('$title','$javascript','$css','$callback','$table','$fields','$positioning')");
			$last_form_id = sqlid();
		}
		
		// Import a Module View
		if ($type == "ModuleView") {
			sqlquery("INSERT INTO bigtree_module_views (`title`,`type`,`table`,`fields`,`options`,`actions`,`suffix`) VALUES ('$title','".$data["type"]."','$table','$fields','$options','$actions','$suffix')");
			$last_view_id = sqlid();
		}
		
		// Import a Template
		if ($type == "Template") {
			sqlquery("DELETE FROM bigtree_templates WHERE id = '$id'");
			sqlquery("INSERT INTO bigtree_templates (`id`,`name`,`image`,`module`,`resources`,`description`,`level`,`package`) VALUES ('$id','$name','$image','$module','$resources','$description','$level','$package_id')");
			$savedData["templates"][] = $id;
		}
		
		// Import a Callout
		if ($type == "Callout") {
			sqlquery("DELETE FROM bigtree_callouts WHERE id = '$id'");
			sqlquery("INSERT INTO bigtree_callouts (`id`,`title`,`description`,`resources`,`package`) VALUES ('$id','$title','$description','$resources','$package_id')");
			$savedData["callouts"][] = $id;
		}
		
		// Import a Setting
		if ($type == "Setting") {
			if ($data["module"])
				$module = $module_match[$module];
			sqlquery("DELETE FROM bigtree_settings WHERE id = '$id'");
			sqlquery("INSERT INTO bigtree_settings (`id`,`value`,`type`,`title`,`description`,`locked`,`module`,`package`) VALUES ('$id','$value','".$data["type"]."','$title','$description','$locked','$module','$package_id')");
			$savedData["settings"][] = $id;
		}
		
		// Import a Feed
		if ($type == "Feed") {
			sqlquery("DELETE FROM bigtree_feeds WHERE route = '$route'");
			sqlquery("INSERT INTO bigtree_feeds (`route`,`name`,`description`,`type`,`table`,`fields`,`options`,`package`) VALUES ('$route','$name','$description','".$data["type"]."','$table','$fields','$options','$package_id')");
			$savedData["feeds"][] = $route;
		}
		
		// Import a File
		if ($type == "File") {
			$source = $parts[1];
			$destination = $parts[2];
			$section = $parts[3];
			foreach ($route_match as $key => $val) {
				$destination = str_replace($key,$val,$destination);
			}
			
			bigtree_copy($cr.$source,$server_root.$destination);
			if ($section == "Other") {			
				$savedData["other_files"][] = $destination;
			} elseif ($section == "Required") {
				$savedData["required_files"][] = $destination;				
			}
			$package_files[] = $destination;
		}
		
		if ($type == "ClassFile") {
			$source = $parts[1];
			$destination = $parts[2];
			$module_id = $parts[3];
			bigtree_copy($cr.$source,$server_root.$destination);
			file_put_contents($server_root.$destination,str_replace('var $Module = "'.$module_id.'";','var $Module = "'.$module_match[$module_id].'";',file_get_contents($server_root.$destination)));
			$savedData["class_files"][] = $destination;
			$package_files[] = $destination;
		}
		
		// Import a SQL file
		if ($type == "SQL") {
			$table = $parts[1];
			$file = $cr.$parts[2];
			$queries = explode("\n",file_get_contents($file));
			foreach ($queries as $query) {
				sqlquery($query);
			}
			$savedData["tables"][] = $table;
			$package_tables[] = $table;
		}
	}
	
	$data = json_decode($_POST["details"],true);
	
	bigtree_clean_globalize_array($data,array("mysql_real_escape_string"));
	
	$package_files = mysql_real_escape_string(json_encode($package_files));
	$package_tables = mysql_real_escape_string(json_encode($package_tables));
	
	sqlquery("INSERT INTO bigtree_module_packages (`foundry_id`,`author`,`name`,`primary_version`,`secondary_version`,`tertiary_version`,`description`,`release_notes`,`details`,`group_id`,`module_id`,`tables`,`files`,`downloaded`,`last_updated`) VALUES ('$id','".mysql_real_escape_string($author["name"])."','$name','$primary_version','$secondary_version','$tertiary_version','$description','$release_notes','".mysql_real_escape_string(json_encode($savedData))."','$group_id','$module_id','$package_tables','$package_files','on','$last_updated')");
	
	$admin->growl("Developer","Installed Module");
	header("Location: ".$aroot."developer/foundry/modules/");
	die();
?>