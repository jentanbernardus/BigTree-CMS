<?
	// !BigTree Random Useful Functions
	// --------------------------------
	// Many are required by the admin.
	
	function bigtree_api_encode($data) {
		global $api_type,$last_api_data;
		$last_api_data = $data;
		if ($api_type == "json") {
			header("Content-type: application/json");
			return json_encode($data);
		} elseif ($api_type == "xml") {
			header("Content-type: text/xml");
			return array_to_xml($data);
		}	
	}
	
	// Compares two tables in a MySQL database and tells you the SQL needed to get Table A to Table B.
	// -- You can pass in the columns ahead of time if these tables exist in separate databases.
	function bigtree_compare_tables($table_a,$table_b,$table_a_columns = false,$table_b_columns = false) {
		$table_a_columns = !empty($table_a_columns) ? $table_a_columns : sqlcolumns($table_a);
		$table_b_columns = !empty($table_b_columns) ? $table_b_columns : sqlcolumns($table_b);
		
		$queries = array();
		$last_key = "";
		foreach ($table_b_columns as $key => $column) {
			$mod = "";
			$action = "";
			// If this column doesn't exist in the Table A table, add it.
			if (!isset($table_a_columns[$key])) {
				$action = "ADD";
			} elseif ($table_a_columns[$key] !== $column) {
				$action = "MODIFY";
			}
			
			if ($action) {
				$mod = "ALTER TABLE `$table_a` $action COLUMN `$key` ".$column["type"];
				if ($column["size"]) {
					$mod .= "(".$column["size"].")";
				}
				if ($column["type_extras"]) {
					$mod .= " ".$column["type_extras"];
				}
				if ($column["null"] == "NO") {
					$mod .= " NOT NULL";
				} else {
					$mod .= " NULL";
				}
				if ($column["default"]) {
					$d = $column["default"];
					if ($d == "CURRENT_TIMESTAMP" || $d == "NULL") {
						$mod .= " DEFAULT $d";
					} else {
						$mod .= " DEFAULT '".mysql_real_escape_string($d)."'";
					}
				}
				if ($column["extra"]) {
					$mod .= " ".$column["extra"];
				}
				
				if ($last_key) {
					$mod .= " AFTER `$last_key`";
				} else {
					$mod .= " FIRST";
				}
				
				$queries[] = $mod;
			}
			
			$last_key = $key;
		}
		
		foreach ($table_a_columns as $key => $column) {
			// If this key no longer exists in the new table, we should delete it.
			if (!isset($table_b_columns[$key])) {
				$queries[] = "ALTER TABLE `$table_a` DROP COLUMN `$key`";
			}	
		}
		
		return $queries;
	}
	
	function bigtree_max_file_size() {
		// Let's determine the max file size, thanks to v3 (&) sonic-world.ru (via php.net)
		$pms = ini_get('post_max_size');
		$mul = substr($pms,-1);
		$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
		$max_file_size = $mul * (int)$pms;
		return $max_file_size;
	}

	// Parses CSS3 into vendor prefixes.
	function bigtree_parse_css3($css) {
		$GLOBALS["browser"] = get_user_browser();
		// Border Radius - border-radius: 0px 0px 0px 0px
/*
		$css = preg_replace_callback('/border-radius:([^\"]*);/iU',create_function('$data','
			$radii = explode(" ",trim($data[1]));
			if (count($radii) == 1) {
				$r = $radii[0];
				return "border-radius: $r; -moz-border-radius: $r; -webkit-border-radius: $r;";
			}
			if (count($radii) == 4) {
				$tl = $radii[0];
				$tr = $radii[1];
				$br = $radii[2];
				$bl = $radii[3];
				return "border-top-left-radius: $tl; border-top-right-radius: $tr; border-bottom-right-radius: $br; border-bottom-left-radius: $bl; -webkit-border-top-left-radius: $tl; -webkit-border-top-right-radius: $tr; -webkit-border-bottom-right-radius: $br; -webkit-border-bottom-left-radius: $bl; -moz-border-radius-topleft: $tl; -moz-border-radius-topright: $tr; -moz-border-radius-bottomright: $br; -moz-border-radius-bottomleft: $bl;";
			}
		'),$css);
*/
		
		// Border Radius Top Left - border-radius-top-left: 0px
		$css = preg_replace_callback('/border-radius-top-left:([^\"]*);/iU',create_function('$data','
			$r = trim($data[1]);
			return "border-top-left-radius: $r; -webkit-border-top-left-radius: $r; -moz-border-radius-topleft: $r;";
		'),$css);
		
		// Border Radius Top Right - border-radius-top-right: 0px
		$css = preg_replace_callback('/border-radius-top-right:([^\"]*);/iU',create_function('$data','
			$r = trim($data[1]);
			return "border-top-right-radius: $r; -webkit-border-top-right-radius: $r; -moz-border-radius-topright: $r;";
		'),$css);
		
		// Border Radius Bottom Left - border-radius-bottom-left: 0px
		$css = preg_replace_callback('/border-radius-bottom-left:([^\"]*);/iU',create_function('$data','
			$r = trim($data[1]);
			return "border-bottom-left-radius: $r; -webkit-border-bottom-left-radius: $r; -moz-border-radius-bottomleft: $r;";
		'),$css);
		
		// Border Radius Bottom Right - border-radius-bottom-right: 0px
		$css = preg_replace_callback('/border-radius-bottom-right:([^\"]*);/iU',create_function('$data','
			$r = trim($data[1]);
			return "border-bottom-right-radius: $r; -webkit-border-bottom-right-radius: $r; -moz-border-radius-bottomright: $r;";
		'),$css);
		
		// Background Gradients - background-gradient: #bottom #top
		$css = preg_replace_callback('/background-gradient:([^\"]*);/iU',create_function('$data','
			$d = trim($data[1]);
			list($start,$stop) = explode(" ",$d);
			$start_rgb = "rgb(".hexdec(substr($start,1,2)).",".hexdec(substr($start,3,2)).",".hexdec(substr($start,5,2)).")";
			$stop_rgb = "rgb(".hexdec(substr($stop,1,2)).",".hexdec(substr($stop,3,2)).",".hexdec(substr($stop,5,2)).")";
			return "background-image: -webkit-gradient(linear,left bottom,left top, color-stop(0, $start_rgb), color-stop(1, $stop_rgb)); background-image: -moz-linear-gradient(center bottom, $start_rgb 0%, $stop_rgb 100%); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=$stop, endColorstr=$start);-ms-filter: \"progid:DXImageTransform.Microsoft.gradient(startColorstr=$stop, endColorstr=$start)\"; zoom:1;";
		'),$css);
		
		// Box Shadow - box-shadow: 0px 0px 5px #color
		$css = preg_replace_callback('/box-shadow:([^\"]*);/iU', 'bigtree_format_css3', $css);
		
		// Column Count - column-count: number
		$css = preg_replace_callback('/column-count:([^\"]*);/iU', 'bigtree_format_css3', $css);
		
		// Column Rule - column-rule: 1px solid color
		$css = preg_replace_callback('/column-rule:([^\"]*);/iU', 'bigtree_format_css3', $css);
		
		// Column Gap - column-gap: number
		$css = preg_replace_callback('/column-gap:([^\"]*);/iU', 'bigtree_format_css3', $css);
		
		// Transition - transition: definition
		$css = preg_replace_callback('/transition:([^\"]*);/iU', 'bigtree_format_css3', $css);
		
		// User Select - user-select: none | text | toggle | element | elements | all | inherit
		$css = preg_replace_callback('/user-select:([^\"]*);/iU', 'bigtree_format_css3', $css);
		
		return $css;
	}
	
	function bigtree_format_css3($data)
	{
		$p = explode(":", $data[0]);
		$d = trim($data[1]);
		
		$return = $p[0] . ": $d; ";
		$return .= "-webkit-".$p[0].": $d; ";
		$return .= "-moz-".$p[0].": $d; ";
		$return .= "-ms-".$p[0].": $d; ";
		$return .= "-o-".$p[0].": $d; ";
		
		return $return;
	}
	
	function bigtree_process_get_vars($non_array_functions = array()) {
		foreach ($_GET as $key => $val) {
			if (strpos($key,0,1) != "_") {
				global $$key;
				if (is_array($val)) {
					$$key = $val;
				} else {
					foreach ($non_array_functions as $func) {
						$val = $func($val);
					}
					$$key = $val;
				}
			}
		}
	}
	
	function bigtree_process_post_vars($non_array_functions = array()) {
		foreach ($_POST as $key => $val) {
			if (strpos($key,0,1) != "_") {
				global $$key;
				if (is_array($val)) {
					$$key = $val;
				} else {
					foreach ($non_array_functions as $func) {
						$val = $func($val);
					}
					$$key = $val;
				}
			}
		}
	}
	
	function bigtree_clean_globalize_array($array,$non_array_functions = array()) {
		foreach ($array as $key => $val) {
			if (strpos($key,0,1) != "_") {
				global $$key;
				if (is_array($val)) {
					$$key = $val;
				} else {
					foreach ($non_array_functions as $func) {
						$val = $func($val);
					}
					$$key = $val;
				}
			}
		}
	}
	
	function bigtree_translate_array($array) {
		global $admin;
		foreach ($array as &$piece) {
			if (is_array($piece)) {
				$piece = bigtree_translate_array($piece);
			} else {
				$piece = $admin->autoIPL($piece);
			}
		}
		return $array;
	}
	
	function bigtree_untranslate_array($array) {
		global $cms;
		foreach ($array as &$piece) {
			if (is_array($piece)) {
				$piece = bigtree_untranslate_array($piece);
			} else {
				$piece = $cms->replaceInternalPageLinks($piece);
			}
		}
		return $array;
	}

	// A function for regular expressions to call that gets internal page links
	function bigtree_regex_get_ipl($matches) {
		global $cms;
		return '="'.$cms->getInternalPageLink($matches[1]).'"';
	}

	// A function for regular expressions to call that creates internal page links
	function bigtree_regex_set_ipl($matches) {
		global $cms;
		$href = str_replace("{wwwroot}",$GLOBALS["www_root"],$matches[1]);
		if (strpos($href,$GLOBALS["www_root"]) !== false) {
			// It's an internal page link.  Let's find it, hopefully.
			$command = explode("/",rtrim(str_replace($GLOBALS["www_root"],"",$href),"/"));
			list($navid,$commands) = $cms->getNavId($command);
			$page = $cms->getPage($navid,false);
			if ($navid && (!$commands[0] || substr($page["template"],0,6) == "module" || substr($commands[0],0,1) == "#")) {
				$href = "ipl://".$navid."//".base64_encode(json_encode($commands));
			}
		}
		$href = str_replace($GLOBALS["www_root"],"{wwwroot}",$href);
		return 'href="'.$href.'"';
	}
	
	// A function to draw a module dropdown
	function bigtree_draw_module_dropdown_contents($currently = false,$route = false) {
?>
<option></option>
<?
		$q = sqlquery("SELECT * FROM bigtree_module_groups ORDER BY name");
		while ($group = sqlfetch($q)) {
			$qq = sqlquery("SELECT * FROM bigtree_modules WHERE `group` = '".$group["id"]."' ORDER BY name");
			if (sqlrows($qq)) {
?>
<optgroup label="<?=$group["name"]?>">
	<? while ($module = sqlfetch($qq)) { ?>
	<option value="<? if ($route) { ?><?=$module["route"]?><? } else { ?><?=$module["id"]?><? } ?>"<? if (($currently == $module["id"] && !$route) || ($currently == $module["route"] && $route)) { ?> selected="selected"<? } ?>><?=$module["name"]?></option>
	<? } ?>
</optgroup>
<?
			}
		}
		$qq = sqlquery("SELECT * FROM bigtree_modules WHERE `group` = '0' ORDER BY name");
		if (sqlrows($qq)) {
?>
<optgroup label="Miscellaneous">
	<? while ($module = sqlfetch($qq)) { ?>
	<option value="<? if ($route) { ?><?=$module["route"]?><? } else { ?><?=$module["id"]?><? } ?>"<? if (($currently == $module["id"] && !$route) || ($currently == $module["route"] && $route)) { ?> selected="selected"<? } ?>><?=$module["name"]?></option>
	<? } ?>
</optgroup>
<?
		}
	}

	// Get a drop down of all the fields in a table.
	function bigtree_field_select($table,$default = "",$sorting = false) {
		$cols = sqlcolumns($table);
		echo '<option></option>';
		foreach ($cols as $col) {
			if ($sorting) {
				if ($default == $col["name"]." asc") {
					echo '<option selected="selected">'.$col["name"].' asc</option>';
				} else {
					echo '<option>'.$col["name"].' asc</option>';
				}
				
				if ($default == $col["name"]." desc") {
					echo '<option selected="selected">'.$col["name"].' desc</option>';
				} else {
					echo '<option>'.$col["name"].' desc</option>';
				}
			} else {
				if ($default == $col["name"]) {
					echo '<option selected="selected">'.$col["name"].'</option>';
				} else {
					echo '<option>'.$col["name"].'</option>';
				}
			}
		}
	}

	// Get the proper path for a file based on whether a custom override exists.
	function bigtree_path($file) {
		global $server_root;
		if (file_exists($server_root."custom/".$file)) {
			return $server_root."custom/".$file;
		} else {
			return $server_root."core/".$file;
		}
	}

	// Get a drop down of tables excluding BigTree tables.
	function bigtree_table_select($default = false) {
		$q = sqlquery("show tables");
		while ($f = sqlfetch($q)) {
			$tname = $f["Tables_in_".$GLOBALS["config"]["db"]["name"]];
			if ($GLOBALS["config"]["show_all_tables_in_dropdowns"] || ((substr($tname,0,8) !== "bigtree_" && substr($tname,0,3) !== "ap_" && substr($tname,0,7) !== "willow_" && substr($tname,0,4) !== "btm_") || $tname == "ap_forms")) {
				if ($default == $f["Tables_in_".$GLOBALS["config"]["db"]["name"]]) {
					echo '<option selected="selected">'.$f["Tables_in_".$GLOBALS["config"]["db"]["name"]].'</option>';
				} else {
					echo '<option>'.$f["Tables_in_".$GLOBALS["config"]["db"]["name"]].'</option>';
				}
			}
		}
	}
	
	// Crop from the center of an image
	function center_crop($file, $newfile, $cw, $ch) {
		list($w, $h) = getimagesize($file);
		
		// Find out what orientation we're cropping at.
		$v = $cw / $w;
		$nh = $h * $v;
		if ($nh < $ch) {
			// We're shrinking the height to the crop height and then chopping the left and right off.
			$v = $ch / $h;
			$nw = $w * $v;
			$x = ceil(($nw - $cw) / 2 * $w / $nw);
			$y = 0;
			create_crop($file,$newfile,$x,$y,$cw,$ch,($w - $x * 2),$h);
		} else {
			$y = ceil(($nh - $ch) / 2 * $h / $nh);
			$x = 0;
			create_crop($file,$newfile,$x,$y,$cw,$ch,$w,($h - $y * 2));
		}
	}
	
	// Find a color between the two given hex values
	function color_mesh($first_color,$second_color,$perc) {
		$perc = intval(str_replace("%","",$perc));
		$first_color = ltrim($first_color,"#");
		$second_color = ltrim($second_color,"#");

		// Get the RGB values for the colors
		$fc_r = hexdec(substr($first_color,0,2));
		$fc_g = hexdec(substr($first_color,2,2));
		$fc_b = hexdec(substr($first_color,4,2));

		$sc_r = hexdec(substr($second_color,0,2));
		$sc_g = hexdec(substr($second_color,2,2));
		$sc_b = hexdec(substr($second_color,4,2));

		$r_diff = ceil(($sc_r - $fc_r) * $perc / 100);
		$g_diff = ceil(($sc_g - $fc_g) * $perc / 100);
		$b_diff = ceil(($sc_b - $fc_b) * $perc / 100);

		$new_color = "#".str_pad(dechex($fc_r + $r_diff),2,"0",STR_PAD_LEFT).str_pad(dechex($fc_g + $g_diff),2,"0",STR_PAD_LEFT).str_pad(dechex($fc_b + $b_diff),2,"0",STR_PAD_LEFT);

		return $new_color;
	}

	function copy_directory($source,$destination,$directory,$parse_www) {
		$directory = rtrim($directory,"/")."/";
		if (!file_exists($destination.$directory)) {
			mkdir($destination.$directory,0777);
		}
		$d = opendir($source.$directory);
		while ($f = readdir($d)) {
			if ($f != "." && $f != "..") {
				if (is_dir($source.$directory.$f)) {
					copy_directory($source.$directory,$destination.$directory,$f,$parse_www);
				} else {
					if ($parse_www) {
						file_put_contents($destination.$directory.$f,str_replace("www_root/",$GLOBALS["www_root"],file_get_contents($source.$directory.$f)));
					} else {
						copy($source.$directory.$f,$destination.$directory.$f);
					}
					chmod($destination.$directory.$f,0777);
				}
			}
		}
	}

	function create_crop($file,$newfile,$x,$y,$crop_width,$crop_height,$width,$height,$jpeg_quality = 90) {
		if (!class_exists("Imagick",false)) {
			list($w, $h, $type) = getimagesize($file);
			$image_p = imagecreatetruecolor($crop_width,$crop_height);
			if ($type == IMAGETYPE_JPEG) {
				$image = imagecreatefromjpeg($file);
			} elseif ($type == IMAGETYPE_GIF) {
				$image = imagecreatefromgif($file);
			} elseif ($type == IMAGETYPE_PNG) {
				$image = imagecreatefrompng($file);
			}

			imagealphablending($image, true);
			imagealphablending($image_p, false);
			imagesavealpha($image_p, true);
			imagecopyresampled($image_p, $image, 0, 0, $x, $y, $crop_width, $crop_height, $width, $height);

			if ($type == IMAGETYPE_JPEG) {
				imagejpeg($image_p,$newfile,$jpeg_quality);
			} elseif ($type == IMAGETYPE_GIF) {
				imagegif($image_p,$newfile);
			} elseif ($type == IMAGETYPE_PNG) {
				imagepng($image_p,$newfile);
			}
			chmod($newfile,0777);

			imagedestroy($image);
			imagedestroy($image_p);
		} else {
			$image = new Imagick($file);
			$image->cropImage($width,$height,$x,$y);
			$image->thumbNailImage($crop_width,$crop_height);
			$image->writeImage($newfile);
		}
		return $newfile;
	}

	function create_thumbnail($file,$newfile,$maxwidth,$maxheight,$jpeg_quality = 90) {
		list($w, $h, $type) = getimagesize($file);
		if ($w > $maxwidth && $maxwidth) {
			$perc = $maxwidth / $w;
			$nw = $maxwidth;
			$nh = round($h * $perc,0);
			if ($nh > $maxheight && $maxheight) {
				$perc = $maxheight / $nh;
				$nh = $maxheight;
				$nw = round($nw * $perc,0);
			}
		} elseif ($h > $maxheight && $maxheight) {
			$perc = $maxheight / $h;
			$nh = $maxheight;
			$nw = round($w * $perc,0);
			if ($nw > $maxwidth && $maxwidth) {
				$perc = $maxwidth / $nw;
				$nw = $maxwidth;
				$nh = round($nh * $perc,0);
			}
		} else {
			$nw = $w;
			$nh = $h;
		}

		if (!class_exists("Imagick",false)) {
			$image_p = imagecreatetruecolor($nw, $nh);
			if ($type == IMAGETYPE_JPEG) {
				$image = imagecreatefromjpeg($file);
			} elseif ($type == IMAGETYPE_GIF) {
				$image = imagecreatefromgif($file);
			} elseif ($type == IMAGETYPE_PNG) {
				$image = imagecreatefrompng($file);
			}

			imagealphablending($image, true);
			imagealphablending($image_p, false);
			imagesavealpha($image_p, true);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);

			if ($type == IMAGETYPE_JPEG) {
				imagejpeg($image_p,$newfile,$jpeg_quality);
			} elseif ($type == IMAGETYPE_GIF) {
				imagegif($image_p,$newfile);
			} elseif ($type == IMAGETYPE_PNG) {
				imagepng($image_p,$newfile);
			}
			imagedestroy($image);
			imagedestroy($image_p);
			chmod($newfile,0777);
			return $newfile;
		} else {
			$image = new Imagick($file);
			$image->thumbnailImage($nw,$nh);
			$image->writeImage($newfile);
			return $newfile;
		}
	}
	
	// Get a file prefix for a full path.
	function file_prefix($file,$prefix) {
		$pinfo = safe_pathinfo($file);
		return $pinfo["dirname"]."/".$prefix.$pinfo["basename"];
	}
	
	function get_user_browser() {
		$s = $_SERVER["HTTP_USER_AGENT"];
		$browser = "unknown";
		if (strpos($s,"MSIE 6")) {
			$browser = "ie6";
		} elseif (strpos($s,"MSIE 7")) {
			$browser = "ie7";
		} elseif (strpos($s,"MSIE 8")) {
			$browser = "ie8";
		} elseif (strpos($s,"MSIE 9")) {
			$browser = "ie9";
		} elseif (strpos($s,"Gecko")) {
			$browser = "gecko";
		} elseif (strpos($s,"WebKit")) {
			$browser = "webkit";
		}
		return $browser;
	}
	
	function get_safe_filename($dir,$file) {
		// If we're working inside the context of the CMS, make the file names less crappy.
		global $cms;

		$parts = safe_pathinfo($dir.$file);

		if (isset($cms)) {
			$clean_name = $cms->urlify($parts["filename"]);
			if (strlen($clean_name) > 50) {
				$clean_name = substr($clean_name,0,50);
			}
			$file = $clean_name.".".$parts["extension"];
		} elseif (strlen($parts["filename"]) > 50) {
			$file = substr($parts["filename"],0,50).".".$parts["extension"];
		}

		// Just find a good filename that isn't used now.
		$x = 2;
		while (file_exists($dir.$file)) {
			$file = $clean_name."-$x.".$parts["extension"];
			$x++;
		}
		return $file;
	}
	
	function permissions_text_to_oct($text) {
		$types = array("-" => 0,"x" => 1, "w" => 2,"r" => 4);
		$user = 0;
		$group = 0;
		$world = 0;
		$user += $types[$text[0]];
		$user += $types[$text[1]];
		$user += $types[$text[2]];
		$group += $types[$text[3]];
		$group += $types[$text[4]];
		$group += $types[$text[5]];
		$world += $types[$text[6]];
		$world += $types[$text[7]];
		$world += $types[$text[8]];
		return "0".$user.$group.$world;
	}

	function safe_pathinfo($file) {
		$parts = pathinfo($file);
		if (!defined('PATHINFO_FILENAME')) {
			$parts["filename"] = substr($parts["basename"],0,strrpos($parts["basename"],'.'));
		}
		return $parts;
	}

	function websafe_file($name) {
		return strtolower(str_replace(array(",","?","&","'","?","*","(",")","^","%","$","#","@","!","~","`","+","{","}","[","]","|",":",";","<",">","=","\\"),"",str_replace(array(" ","/"),"-",stripslashes($name))));
	}

	// !Utility Functions

	// Turns an Array into XML
	function array_to_xml($array,$tab) {
		$xml = "";
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$xml .= "$tab<$key>\n".arraytoxml($val,"$tab\t")."$tab</$key>\n";
			} else {
				if (strpos($val,">") === false && strpos($val,"<") === false && strpos($val,"&") === false) {
					$xml .= "$tab<$key>$val</$key>\n";
				} else {
					$xml .= "$tab<$key><![CDATA[$val]]></$key>\n";
				}
			}
		}
		return $xml;
	}

	// copy but with support for directories that don't exist yet.
	function bigtree_copy($from,$to) {
		if (!bigtree_is_writable($to)) {
			return false;
		}
		if (!is_readable($from)) {
			return false;
		}
		$pathinfo = safe_pathinfo($to);
		$file_name = $pathinfo["basename"];
		$directory = $pathinfo["dirname"];
		$dir_parts = explode("/",ltrim($directory,"/"));

		$dpath = "/";
		foreach ($dir_parts as $d) {
			$dpath .= $d;
			if (!file_exists($dpath)) {
				mkdir($dpath);
				chmod($dpath,0777);
			}
			$dpath .= "/";
		}

		copy($from,$to);
		chmod($to,0777);
		return true;
	}

	// Easy wrapper for CURL
	function bigtree_curl($url,$post = array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (count($post)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	// is_writable with support for directories that don't exist yet.
	function bigtree_is_writable($path) {
		if (is_writable($path)) {
			return true;
		}
		$parts = explode("/",ltrim($path,"/"));
		unset($parts[count($parts)-1]);
		$path = "/".implode("/",$parts);
		if (!is_dir($path)) {
			return bigtree_is_writable($path);
		}
		return is_writable($path);
	}

	// Wrapper for bigtree_copy that removes the original file after copying
	function bigtree_move($from,$to) {
		$success = bigtree_copy($from,$to);
		if (!$success) {
			return false;
		}
		unlink($from);
		return true;
	}

	// touch with support for directories that don't exist yet
	function bigtree_touch($file) {
		if (!bigtree_is_writable($file)) {
			return false;
		}
		$pathinfo = safe_pathinfo($file);
		$file_name = $pathinfo["basename"];
		$directory = $pathinfo["dirname"];
		$dir_parts = explode("/",ltrim($directory,"/"));

		$dpath = "/";
		foreach ($dir_parts as $d) {
			$dpath .= $d;
			if (!file_exists($dpath)) {
				mkdir($dpath);
				chmod($dpath,0777);
			}
			$dpath .= "/";
		}

		touch($file);
		chmod($file,0777);
		return true;
	}

	// Displays 7 pages of the total
	function get_page_array($page,$pages) {
		if ($pages < 8) {
			// Just return it all
			$x = 0;
			while ($x < $pages) {
				$parray[] = $x + 1;
				$x++;
			}
			return $parray;
		}
		if ($page < 6) {
			return array(1,2,3,4,5,6,"...",$pages);
		}
		if ($page > $pages - 5) {
			$parray[] = 1;
			$parray[] = "...";
			$parray[] = $pages - 5;
			$parray[] = $pages - 4;
			$parray[] = $pages - 3;
			$parray[] = $pages - 2;
			$parray[] = $pages - 1;
			$parray[] = $pages;
			return $parray;
		}
		$parray[] = "1";
		$parray[] = "...";
		$parray[] = $page - 2;
		$parray[] = $page - 1;
		$parray[] = $page;
		$parray[] = $page + 1;
		$parray[] = $page + 2;
		$parray[] = "...";
		$parray[] = $pages;
		return $parray;
	}
	
	function bigtree_module_exists($class_name = false) {
		if (in_array($class_name, array_keys($GLOBALS["module_list"]))) {
			return true;
		}
		return false;
	}
	
	function bigtree_redirect($url = false, $type = "301") {
		if (!$url) {
			return false;
		} else if ($type == "301") {
			header ('HTTP/1.1 301 Moved Permanently');
		} else if ($type == "404") {
			header('HTTP/1.0 404 Not Found');
		}
		header("Location: " . $url);
		die();
	}

	// Cleans up HTML to take out tags we don't want in a body, based on a modified version of Christian Stocker's lx_externalinput_clean Class
	// Modified to allow embeds and iframes to still exist but to take out crazy scripting things.
	//
	// +----------------------------------------------------------------------+
	// | Copyright (c) 2001-2008 Liip AG                                      |
	// +----------------------------------------------------------------------+
	// | Licensed under the Apache License, Version 2.0 (the "License");      |
	// | you may not use this file except in compliance with the License.     |
	// | You may obtain a copy of the License at                              |
	// | http://www.apache.org/licenses/LICENSE-2.0                           |
	// | Unless required by applicable law or agreed to in writing, software  |
	// | distributed under the License is distributed on an "AS IS" BASIS,    |
	// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
	// | implied. See the License for the specific language governing         |
	// | permissions and limitations under the License.                       |
	// +----------------------------------------------------------------------+
	// | Author: Christian Stocker <christian.stocker@liip.ch>                |
	// +----------------------------------------------------------------------+
	
	function htmlclean($string) {
		$string = str_replace(array("&amp;", "&lt;", "&gt;"), array("&amp;amp;", "&amp;lt;", "&amp;gt;"), $string);
        
        // fix &entitiy\n;
        $string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $string);
        $string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $string);

        $string = html_entity_decode($string, ENT_COMPAT, "UTF-8");
        
        // remove any attribute starting with "on" or xmlns
        $string = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>#iUu', "$1>", $string);
        
        // remove javascript: and vbscript: protocol
        $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
        $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);
        $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $string);
        $string = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $string);
        
        // remove any style attributes, IE allows too much stupid things in them, eg.
        // <span style="width: expression(alert('Ping!'));"></span> 
        // and in general you really don't want style declarations in your UGC

        $string = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])style[^>]*>#iUu', "$1>", $string);

        // remove namespaced elements (we do not need them...)
        $string = preg_replace('#</*\w+:\w[^>]*>#i', "", $string);
        
        // remove really unwanted tags
        do {
            $oldstring = $string;
            $string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $string);
        } while ($oldstring != $string);
        
		return str_replace("<br></br>","<br />",strip_tags($string,"<h1><h2><h3><h4><h5><h6><p><br><br/><br /><hr /><hr/><hr><b><big><em><i><small><strong><sub><sup><ins><del><s><strike><u><pre><blockquote><address><cite><q><a><table><tr><td><thead><th><tbody><ul><ol><li><dl><dt><dd><form><input><select><textarea><label><fieldset><legend><option><optgroup><img><map><area><font><span><div><button><caption><center><param><tfoot><iframe><embed><object>"));
	}

	// Trims while not breaking HTML tags or counting them as length.	Closes all open tags once it's done trimming.
	function smarter_trim($string,$length) {
		$ns = "";
		$opentags = array();
		$string = trim($string);
		if (strlen(html_entity_decode(strip_tags($string))) < $length) {
			return $string;
		}
		if (strpos($string," ") === false && strlen(html_entity_decode(strip_tags($string))) > $length) {
			return substr($string,0,$length)."...";
		}
		$x = 0;
		$z = 0;
		while ($z < $length && $x <= strlen($string)) {
			$char = substr($string,$x,1);
			$ns .= $char;		// Add the character to the new string.
			if ($char == "<") {
				// Get the full tag -- but compensate for bad html to prevent endless loops.
				$tag = "";
				while ($char != ">"	 && $char !== false) {
					$x++;
					$char = substr($string,$x,1);
					$tag .= $char;
				}
				$ns .= $tag;

				$tagexp = explode(" ",trim($tag));
				$tagname = str_replace(">","",$tagexp[0]);

				// If it's a self contained <br /> tag or similar, don't add it to open tags.
				if ($tagexp[1] != "/") {

					// See if we're opening or closing a tag.
					if (substr($tagname,0,1) == "/") {
						$tagname = str_replace("/","",$tagname);
						// We're closing the tag. Kill the most recently opened aspect of the tag.
						$y = sizeof($opentags);
						$done = false;
						reset($opentags);
						while (current($opentags) && !$done) {
							if (current($opentags) == $tagname) {
								unset($opentags[key($opentags)]);
								$done = true;
							}
							next($opentags);
						}
					} else {
						// Open a new tag.
						$opentags[] = $tagname;
					}
				}
			} elseif ($char == "&") {
				$entity = "";
				while ($char != ";" && $char != " " && $char != "<") {
					$x++;
					$char = substr($string,$x,1);
					$entity .= $char;
				}
				if ($char == ";") {
					$z++;
					$ns .= $entity;
				} elseif ($char == " ") {
					$z += strlen($entity);
					$ns .= $entity;
				} else {
					$z += strlen($entity);
					$ns .= substr($entity,0,-1);
					$x -= 2;
				}
			} else {
				$z++;
			}
			$x++;
		}
		while ($x < strlen($string) && !in_array(substr($string,$x,1),array(" ","!",".",",","<","&"))) {
			$ns .= substr($string,$x,1);
			$x++;
		}
		$ns.= "...";
		$opentags = array_reverse($opentags);
		foreach ($opentags as $key => $val) {
			$ns .= "</".$val.">";
		}
		return $ns;
	}

	// Creates a random string
	function str_rand($length = 8, $seeds = 'alphanum') {
		// Possible seeds
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'ABCDEFGHJKLMNPQRTUVWXY0123456789';
		$seedings['hexidec'] = '0123456789abcdef';

		// Choose seed
		if (isset($seedings[$seeds]))
			$seeds = $seedings[$seeds];

		// Seed generator
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);

		// Generate
		$str = '';
		$seeds_count = strlen($seeds);
		for ($i = 0; $length > $i; $i++) {
			$str .= $seeds { mt_rand(0, $seeds_count - 1) };
		}
		return $str;
	}

	// Creates HTML Word Wraps
	function word_wrap($string,$width) {
		$s = explode(" ", $string);
		foreach ($s as $k => $v) {
			$cnt = strlen($v);
			if ($cnt > $width) {
				$v = wordwrap($v, $width, "<br />", true);
			}
			$new_string .= "$v ";
		}
		return $new_string;
	}

	// Checks to see if a URL exists
	function url_exists($url) {
		$handle = curl_init($url);
		if (false === $handle) {
			return false;
		}
		curl_setopt($handle,CURLOPT_HEADER,false);
		curl_setopt($handle,CURLOPT_FAILONERROR,true);
		curl_setopt($handle,CURLOPT_NOBODY,true);
		curl_setopt($handle,CURLOPT_RETURNTRANSFER,false);
		$connectable = curl_exec($handle);
		curl_close($handle);
		return $connectable;
	}
	
	// Convert bytes into something readable
	function format_bytes($size) {
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) {
			$size /= 1024;
		}
		return round($size, 2).$units[$i];
	}
	
	// Convert readable thing into bytes.
	function unformat_bytes($size) {
		$type = substr($size,-1,1);
		$num = substr($size,0,-1);
		if ($type == "M") {
			return $num * 1048576;
		} elseif ($type == "K") {
			return $num * 1024;
		} elseif ($type == "G") {
			return ($num * 1024 * 1024 * 1024);
		}
		return 0;
	}
	
	// Get max file size for uploads in bytes
	function upload_max_filesize() {
		$upload_max_filesize = ini_get("upload_max_filesize");
		if (!is_integer($upload_max_filesize)) {
			$upload_max_filesize = unformat_bytes($upload_max_filesize);
		}
		
		$post_max_size = ini_get("post_max_size");
		if (!is_integer($post_max_size)) {
			$post_max_size = unformat_bytes($post_max_size);
		}
		
		if ($post_max_size < $upload_max_filesize) {
			$upload_max_filesize = $post_max_size;
		}
		
		return $upload_max_filesize;
	}

	// For servers that don't have multibyte string extensions…
	if (!function_exists("mb_strlen")) {
		function mb_strlen($string) { return strlen($string); }
	}
	if (!function_exists("mb_strtolower")) {
		function mb_strtolower($string) { return strtolower($string); }
	}

	$state_list = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia", 'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois",'IN'=>"Indiana",'IA'=>"Iowa",'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland",'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma",'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");

	$country_list = array("United States","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");

	$month_list = array("1" => "January","2" => "February","3" => "March","4" => "April","5" => "May","6" => "June","7" => "July","8" => "August","9" => "September",		"10" => "October","11" => "November","12" => "December");

?>
