<?
	$breadcrumb[] = array("title" => "Created Modules", "link" => "#");

	bigtree_process_post_vars();
	
	if ($group_new) {
		$f = $admin->getModuleGroupByName($group_new);
		if ($f) {
			$group = $f["id"];
		} else {
			sqlquery("INSERT INTO bigtree_module_groups (`name`) VALUES ('".mysql_real_escape_string($group_new)."')");
			$group = sqlid();
		}
	} else {
		$group = $group_existing;
	}
	
	$route = $cms->urlify($name);
	$route = $admin->getAvailableModuleRoute($route);
	
	$name = mysql_real_escape_string(htmlspecialchars($name));
	
	$gbp = mysql_real_escape_string(json_encode($_POST["gbp"]));
	
	sqlquery("INSERT INTO bigtree_modules (`name`,`route`,`class`,`group`,`gbp`) VALUES ('$name','$route','".mysql_real_escape_string($class)."','$group','$gbp')");
	$id = sqlid();
	
	if ($class) {
		// Create class module.
		$f = fopen($GLOBALS["server_root"]."custom/inc/modules/$route.php","w");
		fwrite($f,"<?\n");
		fwrite($f,"	class $class extends BigTreeModule {\n");
		fwrite($f,"\n");
		fwrite($f,'		var $Table = "'.$table.'";'."\n");
		fwrite($f,'		var $Module = "'.$id.'";'."\n");
		fwrite($f,"	}\n");
		fwrite($f,"?>\n");
		fclose($f);
		chmod($GLOBALS["server_root"]."custom/inc/modules/$route.php",0777);
		
		// Remove cached class list.
		unlink($GLOBALS["server_root"]."cache/module-class-list.btc");
	}	
?>
<h1><span class="icon_developer_modules"></span>Module Created</h1>
<div class="form_container">
	<section>
		<h3 class="action_title"><?=$name?></h3>
		<p>If you plan on programming this module manually, you can leave now. Otherwise, click the continue button below to setup the module's landing page.</p>
	</section>
	<footer>
		<a href="<?=$developer_root?>modules/views/add/<?=$id?>/<?=$table?>/<?=urlencode($name)?>/" class="button blue">Continue</a>	
	</footer>
</div>
