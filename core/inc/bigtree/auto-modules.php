<?
	// Auto Module Class for BigTree 3.0+
	// ----------------------------------
	// Handles various functions for Auto Modules.


	class BigTreeAutoModule {
	
		// This will grab all the data from a view and do parsing on it based on automatic assumptions and manual parsers.
		static function cacheViewData($view) {
			// See if we already have cached data.
			$r = sqlrows(sqlquery("SELECT id FROM bigtree_module_view_cache WHERE view = '".$view["id"]."'"));
			if ($r) {
				return;
			}
			
			// Find out what module we're using so we can get the gbp_field
			$action = sqlfetch(sqlquery("SELECT module FROM bigtree_module_actions WHERE view = '".$view["id"]."'"));
			$module = sqlfetch(sqlquery("SELECT gbp FROM bigtree_modules WHERE id = '".$action["module"]."'"));
			$view["gbp"] = json_decode($module["gbp"],true);
			
			// Setup information on our parsers and populated lists.
			$form = self::getRelatedFormForView($view);
			$view["fields"] = is_array($view["fields"]) ? $view["fields"] : array();
			$parsers = array();
			$poplists = array();
			
			foreach ($view["fields"] as $key => $field) {
				$value = $item[$key];
				if ($field["parser"]) {
					$parsers[$key] = $field["parser"];
				} elseif ($form["fields"][$key]["type"] == "poplist") {
					$poplists[$key] = array("description" => $form["fields"][$key]["pop-description"], "table" => $form["fields"][$key]["pop-table"]);
				}
			}
			
			// See if we need to modify the cache table to add more fields.
			$field_count = count($view["fields"]);
			$cache_columns = sqlcolumns("bigtree_module_view_cache");
			$cc = count($cache_columns) - 10;
			while ($field_count > $cc) {
				$cc++;
				sqlquery("ALTER TABLE bigtree_module_view_cache ADD COLUMN column$cc TEXT AFTER column".($cc-1));
			}
			
			// Cache all records that are published (and include their pending changes)
			$q = sqlquery("SELECT `".$view["table"]."`.*,bigtree_pending_changes.changes AS bigtree_changes FROM `".$view["table"]."` LEFT JOIN bigtree_pending_changes ON (bigtree_pending_changes.item_id = `".$view["table"]."`.id AND bigtree_pending_changes.table = '".$view["table"]."')");
			while ($item = sqlfetch($q)) {
				if ($item["bigtree_changes"]) {
					$changes = json_decode($item["bigtree_changes"],true);
					foreach ($changes as $key => $change) {
						$item[$key] = $change;
					}
				}	

				self::cacheRecord($item,$view,$parsers,$poplists);
			}

			$q = sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = '".$view["table"]."' AND item_id = '0'");
			while ($f = sqlfetch($q)) {
				$item = json_decode($f["changes"],true);
				$item["bigtree_pending"] = true;
				$item["bigtree_pending_owner"] = $f["user"];
				$item["id"] = "p".$f["id"];
				
				self::cacheRecord($item,$view,$parsers,$poplists);
			}
		}
		
		static function cacheRecord($item,$view,$parsers,$poplists) {
			// Setup the fields and VALUES to INSERT INTO the cache table.
			
			$status = "l";
			$pending_owner = 0;
			if ($item["bigtree_changes"]) {
				$status = "c";
			} elseif ($item["bigtree_pending"]) {
				$status = "p";
				$pending_owner = $item["bigtree_pending_owner"];
			}
			$fields = array("view","id","status","position","approved","archived","featured","pending_owner");
			$vals = array("'".$view["id"]."'","'".$item["id"]."'","'$status'","'".$item["position"]."'","'".$item["approved"]."'","'".$item["archived"]."'","'".$item["featured"]."'","'".$pending_owner."'");
			
			// Let's see if we have a grouping field.  If we do, let's get all that info and cache it as well.
			if ($view["options"]["group_field"]) {
				$value = $item[$view["options"]["group_field"]];

				// Check for a parser
				if ($view["options"]["group_parser"]) {
					eval($view["options"]["group_parser"]);
				}

				$fields[] = "group_field";
				$vals[] = "'".mysql_real_escape_string($value)."'";
				
				if (is_numeric($value) && $view["options"]["other_table"]) {
					$f = sqlfetch(sqlquery("SELECT * FROM `".$view["options"]["other_table"]."` WHERE id = '$value'"));
					if ($view["options"]["ot_sort_field"]) {
						$fields[] = "group_sort_field";
						$vals[] = "'".mysql_real_escape_string($f[$view["options"]["ot_sort_field"]])."'";
					}
				}
			}
			
			// Group based permissions data
			if ($view["gbp"]["enabled"] && $view["gbp"]["table"] == $view["table"]) {
				$fields[] = "gbp_field";
				$vals[] = "'".$item[$view["gbp"]["group_field"]]."'";
			}
			
			// Run parsers
			foreach ($parsers as $key => $parser) {
				$value = $item[$key];
				eval($parser);
				$item[$key] = $value;
			}
			
			// Run pop lists
			foreach ($poplists as $key => $pop) {
				$f = sqlfetch(sqlquery("SELECT `".$pop["description"]."` FROM `".$pop["table"]."` WHERE id = '".$item[$key]."'"));
				$item[$key] = end($f);
			}
			
			$cache = true;
			if ($view["options"]["filter"]) {
				eval('$cache = '.$view["options"]["filter"].'($cache);');
			}
			
			if ($cache) {
				$x = 1;
				
				if ($view["type"] == "images" || $view["type"] == "images-grouped") {
					$fields[] = "column1";
					$vals[] = "'".$item[$view["options"]["image"]]."'";
					$fields[] = "column2";
					$vals[] = "'".$item[$view["options"]["caption"]]."'";
				} else {
					foreach ($view["fields"] as $field => $options) {
						$fields[] = "column$x";
						if ($parsers[$field]) {
							$vals[] = "'".mysql_real_escape_string($item[$field])."'";					
						} else {
							$vals[] = "'".mysql_real_escape_string(strip_tags($item[$field]))."'";
						}
						$x++;
					}
				}
				
				sqlquery("INSERT INTO bigtree_module_view_cache (".implode(",",$fields).") VALUES (".implode(",",$vals).")");
			}
		}
		
		// For new items created
		static function cacheNewItem($id,$table,$pending = false,$recache = false) {
			if (!$pending) {
				$item = sqlfetch(sqlquery("SELECT `$table`.*,bigtree_pending_changes.changes AS bigtree_changes FROM `$table` LEFT JOIN bigtree_pending_changes ON (bigtree_pending_changes.item_id = `$table`.id AND bigtree_pending_changes.table = '$table') WHERE `$table`.id = '$id'"));
				if ($item["bigtree_changes"]) {
					$changes = json_decode($item["bigtree_changes"],true);
					foreach ($changes as $key => $change) {
						$item[$key] = $change;
					}
				}
			} else {
				$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '$id'"));
				$item = json_decode($f["changes"],true);
				$item["bigtree_pending"] = true;
				$item["bigtree_pending_owner"] = $f["user"];
				$item["id"] = "p".$f["id"];
			}
			
			$q = sqlquery("SELECT * FROM bigtree_module_views WHERE `table` = '$table'");
			while ($view = sqlfetch($q)) {
				if ($recache) {
					sqlquery("DELETE FROM bigtree_module_view_cache WHERE `view` = '".$view["id"]."' AND id = '$id'");
				}
				
				$view["fields"] = json_decode($view["fields"],true);
				$view["actions"] = json_decode($view["actions"],true);
				$view["options"] = json_decode($view["options"],true);
				
				// Find out what module we're using so we can get the gbp_field
				$action = sqlfetch(sqlquery("SELECT module FROM bigtree_module_actions WHERE view = '".$view["id"]."'"));
				$module = sqlfetch(sqlquery("SELECT gbp FROM bigtree_modules WHERE id = '".$action["module"]."'"));
				$view["gbp"] = json_decode($module["gbp"],true);
				
				$form = self::getRelatedFormForView($view);
				
				$parsers = array();
				$poplists = array();
				
				foreach ($view["fields"] as $key => $field) {
					$value = $item[$key];
					if ($field["parser"]) {
						$parsers[$key] = $field["parser"];
					} elseif ($form["fields"][$key]["type"] == "poplist") {
						$poplists[$key] = array("description" => $form["fields"][$key]["pop-description"], "table" => $form["fields"][$key]["pop-table"]);
					}
				}
				
				self::cacheRecord($item,$view,$parsers,$poplists);
			}
		}
		
		// For updates to existing items
		static function recacheItem($id,$table,$pending = false) {
			self::cacheNewItem($id,$table,$pending,true);
		}
		
		// For deleted items
		static function uncacheItem($id,$table) {
			$q = sqlquery("SELECT * FROM bigtree_module_views WHERE `table` = '$table'");
			while ($view = sqlfetch($q)) {
				sqlquery("DELETE FROM bigtree_module_view_cache WHERE `view` = '".$view["id"]."' AND id = '$id'");
			}
		}
		
		// Clear a view's cache
		static function clearCache($view) {
			$view = is_array($view) ? $view["id"] : mysql_real_escape_string($view);
			sqlquery("DELETE FROM bigtree_module_view_cache WHERE view = '$view'");
		}

		static function createItem($table,$data,$many_to_many = array(),$tags = array(),$resources = array()) {
			global $admin,$module;
			
			$columns = sqlcolumns($table);
			$query_fields = array();
			$query_vals = array();
			foreach ($data as $key => $val) {
				if (isset($columns[$key])) {
					$query_fields[] = "`".$key."`";
					if ($val === "NULL" || $val == "NOW()") {
						$query_vals[] = $val;
					} else {
						$query_vals[] = "'".mysql_real_escape_string($val)."'";
					}
				}
			}
			sqlquery("INSERT INTO $table (".implode(",",$query_fields).") VALUES (".implode(",",$query_vals).")");
			$id = sqlid();

			// Handle many to many
			foreach ($many_to_many as $mtm) {
				$cols = sqlcolumns($mtm["table"]);
				if (is_array($mtm["data"])) {
					foreach ($mtm["data"] as $position => $item) {
						if ($cols["position"])
							sqlquery("INSERT INTO `".$mtm["table"]."` (`".$mtm["my-id"]."`,`".$mtm["other-id"]."`,`position`) VALUES ('$id','$item','$position')");
						else
							sqlquery("INSERT INTO `".$mtm["table"]."` (`".$mtm["my-id"]."`,`".$mtm["other-id"]."`) VALUES ('$id','$item')");
					}
				}
			}

			// Handle the tags
			$mid = mysql_real_escape_string($module["id"]);
			sqlquery("DELETE FROM bigtree_tags_rel WHERE module = '$mid' AND entry = '$id'");
			if (is_array($tags)) {
				foreach ($tags as $tag) {
					sqlquery("DELETE FROM bigtree_tags_rel WHERE module = $mid AND entry = $id AND tag = $tag");
					sqlquery("INSERT INTO bigtree_tags_rel (`module`,`entry`,`tag`) VALUES ($mid,$id,$tag)");
				}
			}
			
			// Handle the resources
			if (is_array($resources)) {
				foreach ($resources as $rid) {
					sqlquery("UPDATE bigtree_resources SET entry = '$id' WHERE id = '$rid'");
				}
			}

			self::cacheNewItem($id,$table);
			
			$admin->track($table,$id,"created");

			return $id;
		}

		static function createPendingItem($module,$table,$data,$many_to_many = array(),$tags = array(),$resources = array()) {
			global $admin;

			foreach ($data as $key => $val) {
				if ($val === "NULL")
					$data[$key] = "";
			}

			$data = mysql_real_escape_string(json_encode($data));
			$many_data = mysql_real_escape_string(json_encode($many_to_many));
			$tags_data = mysql_real_escape_string(json_encode($tags));
			$resources_data = mysql_real_escape_string(json_encode($resources));
			sqlquery("INSERT INTO bigtree_pending_changes (`user`,`date`,`table`,`changes`,`mtm_changes`,`tags_changes`,`resources_changes`,`module`,`type`) VALUES (".$admin->ID.",NOW(),'$table','$data','$many_data','$tags_data','$resources_data','$module','NEW')");
			
			$id = sqlid();

			self::cacheNewItem($id,$table,true);
			
			$admin->track($table,"p$id","created-pending");
			
			return $id;
		}

		static function deleteItem($table,$id) {
			global $admin;
			
			$id = mysql_real_escape_string($id);
			sqlquery("DELETE FROM $table WHERE id = '$id'");
			sqlquery("DELETE FROM bigtree_pending_changes WHERE `table` = '$table' AND item_id = '$id'");
			self::uncacheItem($id,$table);
			$admin->track($table,$id,"deleted");
		}
		
		static function deletePendingItem($table,$id) {
			$id = mysql_real_escape_string($id);
			sqlquery("DELETE FROM bigtree_pending_changes WHERE `table` = '$table' AND id = '$id'");
			self::uncacheItem("p$id",$table);
			$admin->track($table,"p$id","deleted-pending");
		}

		static function getForm($id) {
			if (is_array($id)) {
				$id = $id["id"];
			}
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_forms WHERE id = '$id'"));
			$f["fields"] = json_decode($f["fields"],true);
			return $f;
		}
		
		static function getModuleForForm($form) {
			if (is_array($form)) {
				$form = $form["id"];
			}
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE form = '$form'"));
			return $f["module"];
		}

		static function getModuleForView($view) {
			if (is_array($view)) {
				$view = $view["id"];
			}
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE view = '$view'"));
			return $f["module"];
		}

		static function getPendingItem($table,$id) {
			global $cms,$module;
			$status = "published";
			$many_to_many = array();
			$resources = array();
			if (substr($id,0,1) == "p") {
				$change = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".substr($id,1)."'"));
				if (!$change) {
					return false;
				}
				
				$item = json_decode($change["changes"],true);
				$many_to_many = json_decode($change["mtm_changes"],true);
				$resources = json_decode($change["resources_changes"],true);
				$temp_tags = json_decode($change["tags_changes"],true);
				$tags = array();
				if (!empty($temp_tags)) {
					foreach ($temp_tags as $tid) {
						$tags[] = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE id = '$tid'"));
					}
				}
				$status = "pending";
			} else {
				$item = sqlfetch(sqlquery("SELECT * FROM $table WHERE id = '$id'"));
				if (!$item) {
					return false;
				}
				
				$change = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = '$table' AND `item_id` = '$id'"));
				if ($change) {
					$status = "updated";
					$changes = json_decode($change["changes"],true);
					foreach ($changes as $key => $val) {
						$item[$key] = $val;
					}
					$many_to_many = json_decode($change["mtm_changes"],true);
					$temp_tags = json_decode($change["tags_changes"],true);
					$tags = array();
					if (is_array($temp_tags)) {
						foreach ($temp_tags as $tid) {
							$tags[] = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE id = '$tid'"));
						}
					}
				} else {
					$tags = self::getTagsForEntry($module["id"],$id);
				}
			}
			foreach ($item as $key => $val) {
				if (is_array(json_decode($val,true))) {
					$item[$key] = bigtree_untranslate_array(json_decode($val,true));
				} else {
					$item[$key] = $cms->replaceInternalPageLinks($val);
				}
			}
			return array("item" => $item, "mtm" => $many_to_many, "tags" => $tags, "resources" => $resources, "status" => $status);
		}

		// This will grab all the data from a view and do parsing on it based on automatic assumptions and manual parsers.
		static function getViewData($view,$sort = "id DESC",$type = "both",$module = false) {
			// If we don't need parsed data, just use the normal table.
			if ($view["uncached"]) {
				$view["per_page"] = 10000;
				$r = self::getSearchResults($view,0,"",$sort,"",false,$module);
				return $r["results"];
			}
		
			// Check to see if we've cached this table before.
			self::cacheViewData($view);
			
			$items = array();
			if ($type == "both") {
				$q = sqlquery("SELECT * FROM bigtree_module_view_cache WHERE view = '".$view["id"]."'".self::getFilterQuery($view)." ORDER BY $sort");
			} elseif ($type == "active") {
				$q = sqlquery("SELECT * FROM bigtree_module_view_cache WHERE status != 'p' AND view = '".$view["id"]."'".self::getFilterQuery($view)." ORDER BY $sort");	
			} elseif ($type == "pending") {
				$q = sqlquery("SELECT * FROM bigtree_module_view_cache WHERE status = 'p' AND view = '".$view["id"]."'".self::getFilterQuery($view)." ORDER BY $sort");				
			}
			
			while ($f = sqlfetch($q)) {
				$items[] = $f;
			}
			
			return $items;
		}
		
		// Same as getViewData only you can choose a group.
		static function getViewDataForGroup($view,$group,$sort,$type = "both") {
			// Check to see if we've cached this table before.
			self::cacheViewData($view);
			
			$items = array();
			if ($type == "both") {
				$q = sqlquery("SELECT * FROM bigtree_module_view_cache WHERE group_field = '".mysql_real_escape_string($group)."' AND view = '".$view["id"]."'".self::getFilterQuery($view)." ORDER BY $sort");
			} elseif ($type == "active") {
				$q = sqlquery("SELECT * FROM bigtree_module_view_cache WHERE group_field = '".mysql_real_escape_string($group)."' AND status != 'p' AND view = '".$view["id"]."'".self::getFilterQuery($view)." ORDER BY $sort");
			} elseif ($type == "pending") {
				$q = sqlquery("SELECT * FROM bigtree_module_view_cache WHERE group_field = '".mysql_real_escape_string($group)."' AND status = 'p' AND view = '".$view["id"]."'".self::getFilterQuery($view)." ORDER BY $sort");
			}
			
			while ($f = sqlfetch($q)) {
				$items[] = $f;
			}
			
			return $items;
		}

		static function getRelatedFormForView($view) {
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_forms WHERE `table` = '".mysql_real_escape_string($view["table"])."'"));
			$f["fields"] = json_decode($f["fields"],true);
			return $f;
		}

		static function getRelatedViewForForm($form) {
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_views WHERE `table` = '".mysql_real_escape_string($form["table"])."'"));
			return $f;
		}
		
		static function getFilterQuery($view) {
			global $admin;
			$module = $admin->getModule(self::getModuleForView($view));
			if ($module["gbp"]["enabled"] && $module["gbp"]["table"] == $view["table"]) {
				$groups = $admin->getAccessGroups($module["id"]);
				if (is_array($groups)) {
					$gfl = array();
					foreach ($groups as $g) {
						$gfl[] = "`gbp_field` = '".mysql_real_escape_string($g)."'";
					}
					return " AND (".implode(" OR ",$gfl).")";
				}
			}
			return "";
		}
		
		static function getUncachedFilterQuery($view) {
			global $admin;
			$module = $admin->getModule(self::getModuleForView($view));
			if ($module["gbp"]["enabled"] && $module["gbp"]["table"] == $view["table"]) {
				$groups = $admin->getAccessGroups($module["id"]);
				if (is_array($groups)) {
					$gfl = array();
					foreach ($groups as $g) {
						$gfl[] = "`".$module["gbp"]["group_field"]."` = '".mysql_real_escape_string($g)."'";
					}
					return " AND (".implode(" OR ",$gfl).")";
				}
			}
			return "";
		}

		// Provides data to the Searchable view.
		static function getSearchResults($view,$page = 0,$query = "",$sort = "id",$sort_direction = "DESC",$group = false, $module = false) {
			global $last_query,$admin;
			
			// If we don't need parsed data, just use the normal table.
			if ($view["uncached"]) {
				$search_parts = explode(" ",strtolower($query));
				
				if ($group) {
					$query = "SELECT * FROM ".$view["table"]." WHERE `".$view["options"]["group_field"]."` = '".mysql_real_escape_string($group)."'".self::getUncachedFilterQuery($view);
				} else {
					$query = "SELECT * FROM ".$view["table"]." WHERE 1".self::getUncachedFilterQuery($view);
				}
				
				foreach ($search_parts as $part) {
					$x = 0;
					$qp = array();
					foreach ($view["fields"] as $key => $field) {
						$qp[] = "`$key` LIKE '%".mysql_real_escape_string($part)."%'";
					}
					$query .= " AND (".implode(" OR ",$qp).")";
				}
				
				$per_page = $view["options"]["per_page"] ? $view["options"]["per_page"] : 15;
				
				$qc = sqlfetch(sqlquery(str_replace("SELECT * FROM","SELECT COUNT(id) as `count` FROM",$query)));
				$count = $qc["count"];
				
				$pages = ceil($count / $per_page);
				$pages = ($pages > 0) ? $pages : 1;
				$results = array();
				
				$q = sqlquery($query." ORDER BY $sort $sort_direction LIMIT ".($page * $per_page).",$per_page");
				
				while ($f = sqlfetch($q)) {
					$item = array("id" => $f["id"], "featured" => $f["featured"], "position" => $f["position"], "approved" => $f["approved"], "archived" => $f["archived"]);
					$x = 0;
					foreach ($view["fields"] as $key => $field) {
						$x++;
						$item["column$x"] = strip_tags($f[$key]);
						if ($module["gbp"]["enabled"]) {
							$item["gbp_field"] = $f[$module["gbp"]["group_field"]];
						}
					}
					$results[] = $item;
				}
				
				return array("pages" => $pages, "results" => $results);
			}
			
			// Check to see if we've cached this table before.
			self::cacheViewData($view);
			
			$search_parts = explode(" ",strtolower($query));
			$view_columns = count($view["fields"]);
			
			if ($group !== false) {
				$query = "SELECT * FROM bigtree_module_view_cache WHERE view = '".$view["id"]."' AND group_field = '".mysql_real_escape_string($group)."'".self::getFilterQuery($view);				
			} else {
				$query = "SELECT * FROM bigtree_module_view_cache WHERE view = '".$view["id"]."'".self::getFilterQuery($view);
			}

			foreach ($search_parts as $part) {
				$x = 0;
				$qp = array();
				while ($x < $view_columns) {
					$x++;
					$qp[] = "column$x LIKE '%".mysql_real_escape_string($part)."%'";
				}
				$query .= " AND (".implode(" OR ",$qp).")";
			}
			
			$per_page = $view["options"]["per_page"] ? $view["options"]["per_page"] : 15;
			$pages = ceil(sqlrows(sqlquery($query)) / $per_page);
			$pages = ($pages > 0) ? $pages : 1;
			$results = array();
			
			// Get the correct column name for sorting
			if ($sort != "id") {
				$x = 0;
				foreach ($view["fields"] as $field => $options) {
					$x++;
					if ($field == $sort) {
						$sort = "column$x";
					}
				}
			}
			
			if ($page === "all") {
				$q = sqlquery($query." ORDER BY $sort $sort_direction");
			} else {
				$q = sqlquery($query." ORDER BY $sort $sort_direction LIMIT ".($page * $per_page).",$per_page");
			}
			
			while ($f = sqlfetch($q)) {
				unset($f["hash"]);
				$results[] = $f;
			}

			return array("pages" => $pages, "results" => $results);
		}
		
		static function getTagsForEntry($module,$id) {
			$tags = array();
			$q = sqlquery("SELECT bigtree_tags.* FROM bigtree_tags JOIN bigtree_tags_rel WHERE bigtree_tags_rel.module = '$module' AND bigtree_tags_rel.entry = '$id' AND bigtree_tags_rel.tag = bigtree_tags.id ORDER BY bigtree_tags.tag ASC");
			while ($f = sqlfetch($q)) {
				$tags[] = $f;
			}
			return $tags;
		}

		static function getView($id) {
			if (is_array($id)) {
				$id = $id["id"];
			}
			
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_views WHERE id = '$id'"));
			$f["actions"] = json_decode($f["actions"],true);
			$f["options"] = json_decode($f["options"],true);
			
			$actions = $f["preview_url"] ? ($f["actions"] + array("preview" => "on")) : $f["actions"];
			$fields = json_decode($f["fields"],true);
			$first = current($fields);
			if (!$first["width"]) {
				$awidth = count($actions) * 62;
				$available = 958 - $awidth;
				$percol = floor($available / count($fields));
			
				foreach ($fields as $key => $field) {
					$fields[$key]["width"] = $percol - 20;
				}
			}
			$f["fields"] = $fields;

			return $f;
		}

		// Parser for view data.
		static function parseViewData($view,$items) {
			$form = self::getRelatedFormForView($view);
			$parsed = array();
			foreach ($items as $item) {
				if (is_array($view["fields"])) {
					foreach ($view["fields"] as $key => $field) {
						$value = $item[$key];
						// If we have a parser, run it.
						if ($field["parser"]) {
							eval($field["parser"]);
							$item[$key] = $value;
						// If we know this field is a populated list, get the title they entered in the form.
						} else {
							if ($form["fields"][$key]["type"] == "poplist") {
								$fdata = $form["fields"][$key];
								$f = sqlfetch(sqlquery("SELECT `".$fdata["pop-description"]."` FROM `".$fdata["pop-table"]."` WHERE `id` = '".mysql_real_escape_string($value)."'"));
								$value = $f[$fdata["pop-description"]];
							}
							
							$item[$key] = strip_tags($value);
						}
					}
				}
				$parsed[] = $item;
			}
			return $parsed;
		}

		// Publish a Pending Item
		static function publishPendingItem($table,$id,$data,$many_to_many = array(),$tags = array(),$resources = array()) {
			global $module;
			
			self::deletePendingItem($table,substr($id,1));
			
			$query_fields = array();
			$query_vals = array();
			foreach ($data as $key => $val) {
				$query_fields[] = "`".$key."`";
				if ($val === "NULL" || $val == "NOW()") {
					$query_vals[] = $val;
				} else {
					$query_vals[] = "'".mysql_real_escape_string($val)."'";
				}
			}
			sqlquery("INSERT INTO $table (".implode(",",$query_fields).") VALUES (".implode(",",$query_vals).")");
			$id = sqlid();

			// Handle many to many
			foreach ($many_to_many as $mtm) {
				$cols = sqlcolumns($mtm["table"]);
				if (!empty($mtm["data"])) {
					foreach ($mtm["data"] as $position => $item) {
						if ($cols["position"]) {
							sqlquery("INSERT INTO `".$mtm["table"]."` (`".$mtm["my-id"]."`,`".$mtm["other-id"]."`,`position`) VALUES ('$id','$item','$position')");
						} else {
							sqlquery("INSERT INTO `".$mtm["table"]."` (`".$mtm["my-id"]."`,`".$mtm["other-id"]."`) VALUES ('$id','$item')");
						}
					}
				}
			}

			// Handle the tags
			$mid = mysql_real_escape_string($module["id"]);
			sqlquery("DELETE FROM bigtree_tags_rel WHERE module = '$mid' AND entry = '$id'");
			if (!empty($tags)) {
				foreach ($tags as $tag) {
					sqlquery("DELETE FROM bigtree_tags_rel WHERE module = $mid AND entry = $id AND tag = $tag");
					sqlquery("INSERT INTO bigtree_tags_rel (`module`,`entry`,`tag`) VALUES ($mid,$id,$tag)");
				}
			}
			
			// Handle the resources
			if (!empty($resources)) {
				foreach ($resources as $rid) {
					sqlquery("UPDATE bigtree_resources SET entry = '$id' WHERE id = '$rid'");
				}
			}

			self::cacheNewItem($id,$table);
			
			return $id;
		}

		// Create a Change Request for an Auto Module Item
		static function submitChange($module,$table,$id,$data,$many_to_many = array(),$tags = array()) {
			global $admin;

			$original = sqlfetch(sqlquery("SELECT * FROM $table WHERE id = '$id'"));
			foreach ($data as $key => $val) {
				if ($val === "NULL")
					$data[$key] = "";
				if ($original[$key] == $val)
					unset($data[$key]);
			}
			$changes = mysql_real_escape_string(json_encode($data));
			$many_data = mysql_real_escape_string(json_encode($many_to_many));
			$tags_data = mysql_real_escape_string(json_encode($tags));

			// Find out if there's already a change waiting
			if (substr($id,0,1) == "p") {
				$existing = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".substr($id,1)."'"));
			} else {
				$existing = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = '$table' AND item_id = '$id'"));
			}
			if ($existing) {
				$comments = json_decode($existing["comments"],true);
				if ($existing["user"] == $admin->ID) {
					$comments[] = array(
						"user" => "BigTree",
						"date" => date("F j, Y @ g:ia"),
						"comment" => "A new revision has been made."
					);
				} else {
					$user = $admin->getUser($admin->ID);
					$comments[] = array(
						"user" => "BigTree",
						"date" => date("F j, Y @ g:ia"),
						"comment" => "A new revision has been made.  Owner switched to ".$user["name"]."."
					);
				}
				$comments = mysql_real_escape_string(json_encode($comments));
				sqlquery("UPDATE bigtree_pending_changes SET comments = '$comments', changes = '$changes', mtm_changes = '$many_data', tags_changes = '$tags_data', date = NOW(), user = '".$admin->ID."', type = 'EDIT' WHERE id = '".$existing["id"]."'");
				self::recacheItem($id,$table);
				$admin->track($table,$id,"updated-draft");
				return $existing["id"];
			} else {
				sqlquery("INSERT INTO bigtree_pending_changes (`user`,`date`,`table`,`item_id`,`changes`,`mtm_changes`,`tags_changes`,`module`,`type`) VALUES ('".$admin->ID."',NOW(),'$table','$id','$changes','$many_data','$tags_data','$module','EDIT')");
				self::recacheItem($id,$table);
				$admin->track($table,$id,"saved-draft");
				return sqlid();
			}
		}

		// Update an entry
		static function updateItem($table,$id,$data,$many_to_many = array(),$tags = array()) {
			global $admin,$module;
			$columns = sqlcolumns($table);
			$query = "UPDATE $table SET";
			foreach ($data as $key => $val) {
				if (isset($columns[$key])) {
					if ($val === "NULL" || $val == "NOW()") {
						$query .= "`$key` = $val,";
					} else {
						$query .= "`$key` = '".mysql_real_escape_string($val)."',";
					}
				}
			}
			$query = rtrim($query,",")." WHERE id = '$id'";
			sqlquery($query);

			// Handle many to many
			if (!empty($many_to_many)) {
				foreach ($many_to_many as $mtm) {
					sqlquery("DELETE FROM `".$mtm["table"]."` WHERE `".$mtm["my-id"]."` = '$id'");
					$cols = sqlcolumns($mtm["table"]);
					if (is_array($mtm["data"])) {
						foreach ($mtm["data"] as $position => $item) {
							if ($cols["position"]) {
								sqlquery("INSERT INTO `".$mtm["table"]."` (`".$mtm["my-id"]."`,`".$mtm["other-id"]."`,`position`) VALUES ('$id','$item','$position')");
							} else {
								sqlquery("INSERT INTO `".$mtm["table"]."` (`".$mtm["my-id"]."`,`".$mtm["other-id"]."`) VALUES ('$id','$item')");
							}
						}
					}
				}
			}

			// Handle the tags
			$mid = mysql_real_escape_string($module["id"]);
			sqlquery("DELETE FROM bigtree_tags_rel WHERE module = '$mid' AND entry = '$id'");
			if (!empty($tags)) {
				foreach ($tags as $tag) {
					sqlquery("DELETE FROM bigtree_tags_rel WHERE module = $mid AND entry = $id AND tag = $tag");
					sqlquery("INSERT INTO bigtree_tags_rel (`module`,`entry`,`tag`) VALUES ($mid,$id,$tag)");
				}
			}
			
			// Clear out any pending changes.
			sqlquery("DELETE FROM bigtree_pending_changes WHERE item_id = '$id' AND `table` = '$table'");

			self::recacheItem($id,$table);
			$admin->track($table,$id,"updated");

			return sqlid();
		}

	}
?>
