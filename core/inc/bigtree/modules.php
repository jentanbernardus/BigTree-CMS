<?
	// Base Class for all other BigTree Module Classes
	// Last modified 12/20/10 by Tim
	
	class BigTreeModule {
	
		static $Table = "";
		static $Module = "";

		// !Data Retrieval
		
		static function fetch($sortby = false,$limit = false,$where = false,$table = false) {
			if (!$table) {
				$table = static::$Table;
			}
			
			$query = "SELECT * FROM $table";

			if ($where) {
				$query .= " WHERE $where";
			}
			
			if ($sortby) {
				$query .= " ORDER BY $sortby";
			}
			
			if ($limit) {
				$query .= " LIMIT $limit";
			}
			
			$q = sqlquery($query);
			while ($f = sqlfetch($q)) {
				$items[] = static::get($f,$table);
			}
			
			return $items;
		}
		
		static function get($id,$table = false) {
			global $cms;
			
			if (!$table) {
				$table = static::$Table;
			}
			
			if (is_array($id)) {
				$f = $id;
			} else {
				$f = sqlfetch(sqlquery("SELECT * FROM $table WHERE id = '$id'"));
			}
			
			foreach ($f as $key => $val) {
				$f[$key] = $cms->replaceInternalPageLinks($val);
			}
			
			return $f;
		}
		
		static function getAllPositioned() {
			return static::fetch("position DESC, id ASC");
		}
		
		static function getApproved($order = false,$limit = false,$table = false) {
			return static::getMatching("approved","on",$order,$limit,$table);
		}
		
		static function getByRoute($route) {
			$item = sqlfetch(sqlquery("SELECT * FROM ".static::$Table." WHERE route = '".mysql_real_escape_string($route)."'"));

			if (!$item) {
				return false;
			} else {
				return static::get($item);
			}
		}
		
		static function getFeatured($order = false,$limit = false,$table = false) {
			return static::getMatching("featured","on",$order,$limit,$table);
		}
		
		static function getMatching($field,$value,$sortby = false,$limit = false,$table = false) {
			if (!is_array($field)) {
				$where = "`$field` = '".mysql_real_escape_string($value)."'";
			} else {
				$x = 0;
				while ($x < count($field)) {
					$where[] = "`".$field[$x]."` = '".mysql_real_escape_string($value[$x])."'";
					$x++;
				}
				$where = implode(" AND ",$where);
			}
			
			return static::fetch($sortby,$limit,$where,$table);
		}
		
		static function getPage($page = 0,$orderby = "position DESC, id ASC", $where = false, $perpage = false, $table = false) {
			if (!$perpage) {
				$perpage = 15;
			}
			
			return static::fetch($orderby,($page * $perpage).", $perpage",$where,$table);
		}
		
		static function getPageCount($where = false, $perpage = false, $table = false) {
			if (!$table) {
				$table = static::$Table;
			}
			if (!$perpage) {
				$perpage = 15;
			}
			
			if ($where) {
				$query = "SELECT id FROM $table WHERE $where";
			} else {
				$query = "SELECT id FROM $table";
			}
			
			$pages = ceil(sqlrows(sqlquery($query)) / $perpage);
			if ($pages == 0) {
				$pages = 1;
			}
				
			return $pages;
		}
		
		static function getPending($id) {
			global $cms;
			
			if (!$table) {
				$table = static::$Table;
			}
			
			$id = mysql_real_escape_string($id);
			
			if (substr($id,0,1) == "p") {
				$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".substr($id,1)."'"));
				$item = json_decode($f["changes"],true);
				$item["id"] = $id;
			} else {
				$item = sqlfetch(sqlquery("SELECT * FROM $table WHERE id = '$id'"));
				$c = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE item_id = '$id' AND `table` = '$table'"));
				if ($c) {
					$changes = json_decode($c["changes"],true);
					foreach ($changes as $key => $val) {
						$item[$key] = $val;
					}
				}
			
			}
			
			foreach ($item as $key => $val) {
				$item[$key] = $cms->replaceInternalPageLinks($val);
			}
			
			return $item;
		}
		
		static function getRandom($count = false) {
			if ($count === false) {
				$f = sqlfetch(sqlquery("SELECT * FROM ".static::$Table." ORDER BY RAND() LIMIT 1"));
				return static::get($f);
			}
			return static::fetch("rand()",$count);
		}
		
		static function getRelatedByTags($tags = array(),$exclude = false) {
			$results = array();
			$relevance = array();
			foreach ($tags as $tag) {
				$tdat = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE tag = '".mysql_real_escape_string($tag)."'"));
				if ($tdat) {
					$q = sqlquery("SELECT * FROM bigtree_tags_rel WHERE tag = '".$tdat["id"]."' AND module = '".static::$Module."'");
					while ($f = sqlfetch($q)) {
						$id = $f["entry"];
						if (in_array($id,$results)) {
							$relevance[$id]++;
						} else {
							$results[] = $id;
							$relevance[$id] = 1;
						}
					}
				}
			}
			array_multisort($relevance,SORT_DESC,$results);
			$items = array();
			foreach ($results as $result) {
				$items[] = static::get($result);
			}
			return $items;
		}
		
		static function getTagsForItem($item) {
			if (!is_numeric($item)) {
				$item = $item["id"];
			}
			
			$q = sqlquery("SELECT bigtree_tags.* FROM bigtree_tags JOIN bigtree_tags_rel WHERE bigtree_tags_rel.module = '".static::$Module."' AND bigtree_tags_rel.entry = '$item' AND bigtree_tags.id = bigtree_tags_rel.tag ORDER BY bigtree_tags.tag");

			$tags = array();
			while ($f = sqlfetch($q)) {
				$tags[] = $f;
			}
			
			return $tags;
		}
		
		static function getUpcoming($count = 5, $field = "date",$table = false) {
			return static::fetch("$field ASC",$count,"`$field` >= '".date("Y-m-d")."'",$table);
		}
		
		static function getUpcomingFeatured($count = 5, $field = "date",$table = false) {
			return static::fetch("$field ASC",$count,"featured = 'on' AND `$field` >= '".date("Y-m-d")."'",$table);
		}
		
		static function search($query,$sortby = false,$limit = false,$table = false) {
			if (!$table) {
				$table = static::$Table;
			}
			$fields = sqlcolumns($table);
			
			foreach ($fields as $field) {
				$where[] = "`$field` LIKE '%".mysql_real_escape_string($query)."%'";
			}
			
			return static::fetch($sortby,$limit,implode(" OR ",$where),$table);
		}
		
		// !Data Manipulation
		
		static function add($keys,$vals,$table = false) {
			if (!$table) {
				$table = static::$Table;
			}
			
			/* Prevent Duplicates! */
			$query = "SELECT id FROM $table WHERE ";
			$kparts = array();
			$x = 0;
			while ($x < count($keys)) {
				$kparts[] = "`".$keys[$x]."` = '".mysql_real_escape_string($vals[$x])."'";
				$x++;
			}
			$query .= implode(" AND ",$kparts);
			if (sqlrows(sqlquery($query))) {
				return false;
			}
			/* Done preventing dupes! */
			
			$query = "INSERT INTO $table (";
			$kparts = array();
			$vparts = array();
			foreach ($keys as $key) {
				$kparts[] = "`".$key."`";
			}
			
			$query .= implode(",",$kparts).") VALUES (";

			foreach ($vals as $val) {
				$vparts[] = "'".mysql_real_escape_string($val)."'";
			}
			
			$query .= implode(",",$vparts).")";
			sqlquery($query);
			
			return sqlid();
		}
		
		static function delete($id,$table = false) {
			if (!$table) {
				$table = static::$Table;
			}
			
			sqlquery("DELETE FROM $table WHERE id = '$id'");
		}
		
		static function save($item) {
			$id = $item["id"];
			unset($item["id"]);
			
			$keys = array_keys($item);
			static::update($id,$keys,$item);
		}
		
		static function submitChange($id,$changes,$module = 0,$type = "EDIT") {
			global $cms,$admin;
			
			$original = sqlfetch(sqlquery("SELECT * FROM ".static::$Table." WHERE id = '$id'"));
			foreach ($changes as $key => $val) {
				if ($original[$key] == $val) {
					unset($changes[$key]);
				}
			}
			
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = '".static::$Table."' AND item_id = '$id'"));
			if ($f) {
				$comments = json_decode($f["comments"],true);
				if ($f["user"] == $admin->ID) {
					$comments[] = array(
						"user" => "BigTree",
						"date" => date("F j, Y @ g:ia"),
						"comment" => "A new revision has been made."
					);
				} else {
					$user = $admin->getUserById($admin->ID);
					$comments[] = array(
						"user" => "BigTree",
						"date" => date("F j, Y @ g:ia"),
						"comment" => "A new revision has been made.  Owner switched to ".$user["name"]."."
					);
				}
				$comments = mysql_real_escape_string(json_encode($comments));
				$ochanges = json_decode($f["changes"],true);
				foreach ($changes as $key => $val) {
					$ochanges[$key] = $val;
				}
				$changes = mysql_real_escape_string(json_encode($ochanges));
				sqlquery("UPDATE bigtree_pending_changes SET comments = '$comments', changes = '$changes', date = NOW(), user = '".$admin->ID."', type = '$type' WHERE id = '".$f["id"]."'");
				return $f["id"];
			} else {
				sqlquery("INSERT INTO bigtree_pending_changes (`changes`,`date`,`user`,`table`,`item_id`,`type`,`module`) VALUES ('".mysql_real_escape_string(json_encode($changes))."',NOW(),'".$admin->ID."','".static::$Table."','$id','$type','$module')");
				return sqlid();
			}
		}
		
		static function update($id,$keys,$vals,$table = false) {
			if (!$table) {
				$table = static::$Table;
			}
			
			$query = "UPDATE $table SET ";
			
			if (is_array($keys)) {
				$kparts = array();
				foreach ($keys as $key) {
					$kparts[] = "`".$key."` = '".mysql_real_escape_string(current($vals))."'";
					next($vals);
				}
			
				$query .= implode(", ",$kparts)." WHERE id = '$id'";
			} else {
				$query = "UPDATE $table SET `$keys` = '".mysql_real_escape_string($vals)."' WHERE id = '$id'";
			}
			
			sqlquery($query);
		}
			
		
		// !Archiving
		
		static function archive($id,$table = false) {
			static::update($id,"archived","on",$table);
		}
		
		static function unarchive($id, $table = false) {
			static::update($id,"archived","",$table);
		}
		
		// !Approving
		
		static function approve($id,$table = false) {
			static::update($id,"approved","on",$table);
		}
		
		static function disapprove($id,$table = false) {
			static::update($id,"approved","",$table);
		}
		
		// !Featuring
		
		static function feature($id,$table = false) {
			static::update($id,"featured","on",$table);
		}
		
		static function unfeature($id,$table = false) {
			static::update($id,"featured","",$table);
		}
		
		// !Sorting
		static function setPosition($id,$position,$table = false) {
			static::update($id,"position",$position,$table);
		}
	
	}
?>