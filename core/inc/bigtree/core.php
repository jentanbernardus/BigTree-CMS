<?
	// BigTree Core Navigation Class

	include bigtree_path("inc/bigtree/modules.php");
	include bigtree_path("inc/bigtree/forms.php");

	class BigTreeCMS {

		// !Constructor
		function __construct() {
		
			if (file_exists($GLOBALS["server_root"]."cache/module-class-list.btc")) {
				$items = json_decode(file_get_contents($GLOBALS["server_root"]."cache/module-class-list.btc"),true);
			} else {
				// Get the Module Class List
				$q = sqlquery("SELECT * FROM bigtree_modules");
				$items = array();
				while ($f = sqlfetch($q)) {
					$items[$f["class"]] = $f["route"];
				}
				
				file_put_contents($GLOBALS["server_root"]."cache/module-class-list.btc",json_encode($items));
			}
			
			$this->ModuleClassList = $items;
		}

		// !Utility Methods

		function getSetting($id) { return $this->getSettingById($id); }
		function getSettingById($id) {
			global $config;
			$id = mysql_real_escape_string($id);
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_settings WHERE id = '$id'"));
			if ($f["encrypted"]) {
				$f = sqlfetch(sqlquery("SELECT AES_DECRYPT(`value`,'".mysql_real_escape_string($config["settings_key"])."') AS `value` FROM bigtree_settings WHERE id = '$id'"));
			}
			return json_decode($f["value"],true);
		}
		
		function makeSecure() {
			if ($_SERVER["SERVER_PORT"] == 80) {
				header("Location: ".str_replace("http://","https://",$GLOBALS["www_root"]).$_GET["bigtree_htaccess_url"]);
				die();
			}
			$this->Secure = true;
		}

		function stripe($reset = false) {
			static $x;
			if ($reset) {
				$x = 0;
				return;
			}
			$x++;
			if ($x % 2 == 1) {
				return "odd";
			} else {
				return "even";
			}
		}
		
		function tabindex($reset = false) {
			static $x;
			if ($reset) {
				$x = 1;
				return 1;
			}
			$x++;
			return $x;
		}

		// !Navigation/Page Methods
		
		function decodeCallouts($data) {
			if (!is_array($data)) {
				$data = json_decode($data,true);
			}
			$parsed = array();
			if (!is_array($data)) {
				$data = json_decode($data,true);
			}
			if (is_array($data)) {
				foreach ($data as $key => $d) {
					$p = array();
					foreach ($d as $kk => $dd) {
						if (is_array(json_decode($dd,true))) {
							$p[$kk] = bigtree_untranslate_array(json_decode($dd,true));
						} else {
							$p[$kk] = $this->replaceInternalPageLinks($dd);
						}
					}
					$parsed[$key] = $p;
				}
			}
			return $parsed;
		}

		function decodeResources($data) {
			if (!is_array($data)) {
				$data = json_decode($data,true);
			}
			if (is_array($data)) {
				foreach ($data as $key => $val) {
					if (is_array($val)) {
						$val = bigtree_untranslate_array($val);
					} elseif (is_array(json_decode($val,true))) {
						$val = bigtree_untranslate_array(json_decode($val,true));
					} else {
						$val = $this->replaceInternalPageLinks($val);				
					}
					$data[$key] = $val;
				}
			}
			return $data;
		}
		
		function generateMySQLReplace($content,$array,$val) {
			$string = str_repeat("REPLACE(",count($array));
			$string .= $content;
			foreach ($array as $piece) {
				$string .= ",'".mysql_real_escape_string($piece)."','$val')";
			}
			return $string;
		}
		
		function getAlphabeticalNavByParent($parent = 0,$levels = 1,$hidden = false) {
			$nav = array();
			if ($hidden) {
				$q = sqlquery("SELECT id,nav_title,parent,external,new_window,template,route FROM bigtree_pages WHERE parent = '$parent' AND archived != 'on' ORDER BY nav_title");
			} else {
				$q = sqlquery("SELECT id,nav_title,parent,external,new_window,template,route FROM bigtree_pages WHERE parent = '$parent' AND in_nav = 'on' AND archived != 'on' ORDER BY nav_title");
			}
			while ($f = sqlfetch($q)) {
				if ($f["external"] && $f["template"] == "") {
					if (substr($f["external"],0,6) == "ipl://") {
						$f["external"] = $this->getInternalPageLink($f["external"]);
					} else {
						$f["external"] = str_replace("{wwwroot}",$GLOBALS["www_root"],$f["external"]);
					}
				}
				if ($f["template"] == "") {
					$nav_item = array("id" => $f["id"], "title" => htmlspecialchars($f["nav_title"]), "external" => $f["external"], "new_window" => $f["new_window"]);
				} else {
					$nav_item = array("id" => $f["id"], "title" => htmlspecialchars($f["nav_title"]), "route" => $f["route"]);
				}
				if ($levels > 1) {
					$nav_item["children"] = $this->getNavByParent($f["id"],$levels - 1);
				}
				$nav[] = $nav_item;
			}
			$f = sqlfetch(sqlquery("SELECT id,nav_title,parent,external,new_window,template FROM bigtree_pages WHERE id = '$parent'"));
			if (substr($f["template"],0,7) == "module-") {
				$module_children = $this->getModuleNav($f["id"],$f["template"]);
				$nav = array_merge($module_children,$nav);
			}
			return $nav;
		}
		
		function getBreadcrumb($data = false) {
			global $page;
			if (!$data) {
				return $this->getBreadcrumbByPage($page);
			} else {
				return $this->getBreadcrumbByPage($data);
			}
		}
		
		function getBreadcrumbByPage($page) {
			$bc = array();
			
			$pieces = explode("/",$page["path"]);
			$paths = array();
			$path = "";
			foreach ($pieces as $piece) {
				$path = $path.$piece."/";
				$paths[] = "path = '".mysql_real_escape_string(trim($path,"/"))."'";
			}
			
			$q = sqlquery("SELECT id,nav_title,path FROM bigtree_pages WHERE (".implode(" OR ",$paths).") ORDER BY LENGTH(path) ASC");
			while ($f = sqlfetch($q)) {
				if ($f["external"] && $f["template"] == "") {
					if (substr($f["external"],0,6) == "ipl://") {
						$f["link"] = $this->getInternalPageLink($f["external"]);
					} else {
						$f["link"] = str_replace("{wwwroot}",$GLOBALS["www_root"],$f["external"]);
					}
				} else {
					$f["link"] = $GLOBALS["www_root"].$f["path"]."/";
				}
				$bc[] = array("title" => stripslashes($f["nav_title"]),"link" => $f["link"],"id" => $f["id"]);
			}
			
			// Check for module breadcrumbs
			$mod = sqlfetch(sqlquery("SELECT bigtree_modules.class FROM bigtree_modules JOIN bigtree_templates ON bigtree_modules.id = bigtree_templates.module WHERE bigtree_templates.id = '".$page["template"]."'"));
			if ($mod["class"]) {
				if (class_exists($m["class"])) {
					eval('$module = new '.$m["class"].';');
					if (method_exists($module,"getBreadcrumb")) {
						$bc += $module->getBreadcrumb($id);
					}
				}
			}
			
			return $bc;
		}
		
		function getFeedById($id) {
			$id = mysql_real_escape_string($id);
			$item = sqlfetch(sqlquery("SELECT * FROM bigtree_feeds WHERE id = '$id'"));
			$item["options"] = json_decode($item["options"],true);
			if (is_array($item["options"])) {
				foreach ($item["options"] as &$option) {
					$option = str_replace("{wwwroot}",$GLOBALS["www_root"],$option);
				}
			}
			$item["fields"] = json_decode($item["fields"],true);
			return $item;
		}
		
		function getFeedByRoute($route) {
			$route = mysql_real_escape_string($route);
			$item = sqlfetch(sqlquery("SELECT * FROM bigtree_feeds WHERE route = '$route'"));
			$item["options"] = json_decode($item["options"],true);
			if (is_array($item["options"])) {
				foreach ($item["options"] as &$option) {
					$option = str_replace("{wwwroot}",$GLOBALS["www_root"],$option);
				}
			}
			$item["fields"] = json_decode($item["fields"],true);
			return $item;		
		}
		
		function getFullNavigationPath($child, $path = array()) {
			$f = sqlfetch(sqlquery("SELECT route,id,parent FROM bigtree_pages WHERE id = '$child'"));
			$path[] = $this->urlify($f["route"]);
			if ($f["parent"] != $GLOBALS["root_page"] && $f["parent"] != 0) {
				return $this->getFullNavigationPath($f["parent"],$path);
			}
			$path = implode("/",array_reverse($path));
			return $path;
		}
		
		function getHiddenNavByParent($parent = 0) {
			$nav = array();
			$q = sqlquery("SELECT id,nav_title,parent,external,new_window,template,route FROM bigtree_pages WHERE parent = '$parent' AND in_nav != 'on' AND archived != 'on' ORDER BY nav_title");
			while ($f = sqlfetch($q)) {
				if ($f["external"] && $f["template"] == "") {
					if (substr($f["external"],0,6) == "ipl://") {
						$f["external"] = $this->getInternalPageLink($f["external"]);
					} else {
						$f["external"] = str_replace("{wwwroot}",$GLOBALS["www_root"],$f["external"]);
					}
				}
				if ($f["template"] == "") {
					$nav_item = array("id" => $f["id"], "title" => htmlspecialchars($f["nav_title"]), "external" => true, "link" => $f["external"], "new_window" => $f["new_window"]);
				} else {
					$nav_item = array("id" => $f["id"], "title" => htmlspecialchars($f["nav_title"]), "external" => false, "link" => $GLOBALS["www_root"].$f["path"], "new_window" => false);
				}
				
				$nav[] = $nav_item;
			}
			return $nav;
		}
		
		function getInternalPageLink($ipl) {
			if (substr($ipl,0,6) != "ipl://") {
				return str_replace("{wwwroot}",$GLOBALS["www_root"],$ipl);
			}
			$ipl = explode("//",$ipl);
			$navid = $ipl[1];
			$commands = implode("/",json_decode(base64_decode($ipl[2])),true);
			if ($commands && strpos($commands,"?") === false) {
				$commands .= "/";
			}
			return $GLOBALS["www_root"].$this->getFullNavigationPath($navid)."/".$commands;
		}
		
		function getLink($id) {
			if ($id == 0) {
				return $GLOBALS["www_root"];
			}
			$f = sqlfetch(sqlquery("SELECT path FROM bigtree_pages WHERE id = '".mysql_real_escape_string($id)."'"));
			return $GLOBALS["www_root"].$f["path"]."/";
		}
		
		function getLinkById($id) { return $this->getLink($id); }
		
		function getModuleBreadcrumb($id,$template) {
			$t = $this->getTemplateById($template);
			if (!$t["module"]) {
				return array();
			}
			$m = sqlfetch(sqlquery("SELECT * FROM bigtree_modules WHERE route = '".$t["module"]."'"));
			if (class_exists($m["class"])) {
				$s = '$module = new '.$m["class"].';';
				eval($s);
				if (method_exists($module,"getBreadcrumb")) {
					return $module->getBreadcrumb($id);
				}
			}
			return array();
		}
		
		function getModuleNav($class) {
			eval('$module = new '.$class.';');
			if (method_exists($module,"getNav")) {
				return $module->getNav($id);
			} else {
				return array();
			}
		}

		function getNavByParent($parent = 0,$levels = 1,$follow_module = true) {
			$nav = array();
			$find_children = array();
			
			if (is_array($parent)) {
				$where_parent = array();
				foreach ($parent as $p) {
					$where_parent[] = "parent = '".mysql_real_escape_string($p)."'";
				}
				$q = sqlquery("SELECT id,nav_title,parent,external,new_window,template,route,path FROM bigtree_pages WHERE (".implode(" OR ",$where_parent).") AND in_nav = 'on' AND archived != 'on' AND (publish_at <= NOW() OR publish_at IS NULL) ORDER BY position DESC, id ASC");
			} else {
				$parent = mysql_real_escape_string($parent);
				$q = sqlquery("SELECT id,nav_title,parent,external,new_window,template,route,path FROM bigtree_pages WHERE parent = '$parent' AND in_nav = 'on' AND archived != 'on' AND (publish_at <= NOW() OR publish_at IS NULL) ORDER BY position DESC, id ASC");
			}
			
			while ($f = sqlfetch($q)) {
				$link = $GLOBALS["www_root"].$f["path"]."/";
				$new_window = false;
				if ($f["external"] && $f["template"] == "") {
					if (substr($f["external"],0,6) == "ipl://") {
						$link = $this->getInternalPageLink($f["external"]);
					} else {
						$link = str_replace("{wwwroot}",$GLOBALS["www_root"],$f["external"]);
					}
					if ($f["new_window"] == "Yes") {
						$new_window = true;
					}
				}
				
				$nav[$f["id"]] = array("id" => $f["id"], "parent" => $f["parent"], "title" => $f["nav_title"], "route" => $f["route"], "link" => $link, "new_window" => $new_window, "children" => array());
				
				if ($levels > 1) {
					$find_children[] = $f["id"];
				}
			}
			
			if (count($find_children)) {
				$subnav = $this->getNavByParent($find_children,$levels - 1,$follow_module);
				foreach ($subnav as $item) {
					$nav[$item["parent"]]["children"][$item["id"]] = $item;
				}
			}
			
			if ($follow_module) {
				if (is_array($parent)) {
					$where_parent = array();
					foreach ($parent as $p) {
						$where_parent[] = "bigtree_pages.id = '".mysql_real_escape_string($p)."'";
					}
					$q = sqlquery("SELECT bigtree_modules.class,bigtree_templates.routed,bigtree_templates.module,bigtree_pages.id,bigtree_pages.template FROM bigtree_modules JOIN bigtree_templates JOIN bigtree_pages ON bigtree_templates.id = bigtree_pages.template WHERE bigtree_modules.id = bigtree_templates.module AND (".implode(" OR ",$where_parent).")");
					while ($f = sqlfetch($q)) {
						if ($f["class"]) {
							$modNav = $this->getModuleNav($f["class"]);
							foreach ($modNav as $item) {
								$item["parent"] = $f["id"];
								unset($item["id"]);
								$nav[] = $item;
							}
						}
					}
				} else {
					$f = sqlfetch(sqlquery("SELECT bigtree_templates.routed,bigtree_templates.module,bigtree_pages.id,bigtree_pages.template FROM bigtree_templates JOIN bigtree_pages ON bigtree_templates.id = bigtree_pages.template WHERE bigtree_pages.id = '$parent'"));
					if ($f["routed"] && $f["module"]) {
						$nav += $this->getModuleNav($parent,$f["template"]);
					}
				}
			}
			
			return $nav;
		}
		
		function getNavId($path) {
			$commands = array();
			
			$spath = implode("/",$path);
			$f = sqlfetch(sqlquery("SELECT id FROM bigtree_pages WHERE path = '$spath' AND archived = ''"));
			if ($f) {
				return array($f["id"],$commands);
			}
			
			$x = 0;
			while ($x < count($path)) {
				$x++;
				$commands[] = $path[count($path)-$x];
				$spath = implode("/",array_slice($path,0,-1 * $x));
				$f = sqlfetch(sqlquery("SELECT id,template FROM bigtree_pages WHERE path = '$spath' AND archived = ''"));
				if ($f && substr($f["template"],0,6) == "module") {
					return array($f["id"],array_reverse($commands));
				}
			}
			
			return false;
		}
		
		function getPage($child) {
			return $this->getPageById($child);
		}
		
		function getPageById($child,$decode = true) {
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pages WHERE id = '$child'"));
			if (!$f) {
				return false;
			}
			if ($f["external"] && $f["template"] == "") {
				$f["external"] = $this->getInternalPageLink($f["external"]);
			}
			if ($decode) {
				$f["resources"] = $this->decodeResources($f["resources"]);
				$f["callouts"] = $this->decodeCallouts($f["callouts"]);
			}
			return $f;
		}
		
		function getPageDepth($page) {
			if (!is_array($page)) {
				$page = sqlfetch(sqlquery("SELECT path FROM bigtree_pages WHERE id = '".mysql_real_escape_string($page)."'"));
			}
			
			return count(explode("/",$page["path"]));
		}
		
		function getPendingPageById($id,$decode = true) {
			if ($id[0] == "p") {
				$page = array();
				$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".substr($id,1)."'"));
				$changes = json_decode($f["changes"],true);
				foreach ($changes as $key => $val) {
					if ($key == "external") {
						$val = $this->getInternalPageLink($val);
					}
					$page[$key] = $val;
				}
			} else {
				$page = $this->getPageById($id);
				$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND item_id = '$id'"));
				if ($f) {
					$changes = json_decode($f["changes"],true);
					foreach ($changes as $key => $val) {
						if ($key == "external") {
							$val = $this->getInternalPageLink($val);
						}
						$page[$key] = $val;
					}
				}
			}
			if ($decode) {
				$page["resources"] = $this->decodeResources($page["resources"]);
				$page["callouts"] = $this->decodeCallouts($page["callouts"]);
			}
			return $page;
		}
		
		function getPreviewLink($id) {
			if ($id == 0) {
				return $GLOBALS["www_root"];
			}
			if (substr($id,0,1) == "p") {
				return $GLOBALS["www_root"]."_preview-pending/".substr($id,1)."/";
			} else {
				$f = sqlfetch(sqlquery("SELECT path FROM bigtree_pages WHERE id = '".mysql_real_escape_string($id)."'"));
				return $GLOBALS["www_root"]."_preview/".$f["path"]."/";
			}
		}
		
		function getRelatedPagesByTags($tags = array(),$exclude = false) {
			$results = array();
			$relevance = array();
			foreach ($tags as $tag) {
				$tdat = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE tag = '".mysql_real_escape_string($tag)."'"));
				if ($tdat) {
					$q = sqlquery("SELECT * FROM bigtree_tags_rel WHERE tag = '".$tdat["id"]."' AND module = '0'");
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
				$items[] = $this->getPage($result);
			}
			return $items;
		}
		
		function getCalloutById($id) {
			return sqlfetch(sqlquery("SELECT * FROM bigtree_callouts WHERE id = '$id'"));
		}
		
		function getTagsForPage($page) {
			if (!is_numeric($page)) {
				$page = $page["id"];
			}
			$q = sqlquery("SELECT bigtree_tags.* FROM bigtree_tags JOIN bigtree_tags_rel WHERE bigtree_tags_rel.module = '0' AND bigtree_tags_rel.entry = '$page' AND bigtree_tags.id = bigtree_tags_rel.tag ORDER BY bigtree_tags.tag");
			$tags = array();
			while ($f = sqlfetch($q)) {
				$tags[] = $f;
			}
			return $tags;
		}
		
		function getTemplate($id) { return $this->getTemplateById($id); }

		function getTemplateById($id) {
			$t = sqlfetch(sqlquery("SELECT * FROM bigtree_templates WHERE id = '$id'"));
			if (!$t) {
				return false;
			}
			$t["resources"] = json_decode($t["resources"],true);
			return $t;
		}
		
		function getTopLevelNavigationId($child,$top = 0) {
			return $this->getTopLevelNavigationIdForPage(sqlfetch(sqlquery("SELECT path FROM bigtree_pages WHERE id = '".mysql_real_escape_string($child)."'")));
		}
		
		function getTopLevelNavigationIdForPage($page) {
			$parts = explode("/",$page["path"]);
			$f = sqlfetch(sqlquery("SELECT id FROM bigtree_pages WHERE path = '".mysql_real_escape_string($parts[0])."'"));
			return $f["id"];
		}
		
		function replaceInternalPageLinks($html) {
			$drop_count = 0;
			if (!trim($html)) {
				return "";
			}

			$html = str_replace(array("{wwwroot}","%7Bwwwroot%7D"),$GLOBALS["www_root"],$html);
			$html = preg_replace_callback('^="(ipl:\/\/[a-zA-Z0-9\:\/\.\?\=\-]*)"^','bigtree_regex_get_ipl',$html);

			return $html;
		}

		function urlify($title) {
			$accent_match = array('Â', 'Ã', 'Ä', 'À', 'Á', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
			$accent_replace = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'B', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');

			$title = str_replace($accent_match, $accent_replace, $title);
			
			return strtolower(preg_replace('/\s/', '-',preg_replace('/[^a-zA-Z0-9\s\-\_]+/', '',trim($title))));
		}
	}
?>