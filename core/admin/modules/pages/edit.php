<?
	$page = end($path);
	
	$show_revert = false;
	if ($page[0] == "p") {
		$cid = substr($page,1);
		$f = $admin->getPendingChange($cid);
		$pdata = $f["changes"];
		$pdata["updated_at"] = $f["date"];
		$r = $admin->getPageAccessLevelByUserId($pdata["parent"],$admin->ID);

		$tags = array();
		$temp_tags = json_decode($f["tags_changes"],true);
		if (is_array($temp_tags)) {
			foreach ($temp_tags as $tag) {
				$tags[] = sqlfetch(sqlquery("SELECT * FROM bigtree_tags WHERE id = '$tag'"));
			}
		}
		$presources = json_decode($f["resources_changes"],true);
		
		$pdata["id"] = $page;
	} else {
		$r = $admin->getPageAccessLevelByUserId($page,$admin->ID);
		$pdata = $admin->getPendingPageById($page);
		$tags = $pdata["tags"];
		if (sqlrows(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND item_id = '$page'"))) {
			$show_revert = true;
		}
	}

	$resources = $pdata["resources"];
	$callouts = $pdata["callouts"];
	
	if (!isset($pdata["id"])) {
		$breadcrumb = array(
			array("link" => "pages/", "title" => "Pages"),
			array("link" => "pages/view-tree/0/", "title" => "Home")
		);
?>
<h1><span class="error"></span>Error</h1>
<p class="error">The page you are trying to edit no longer exists.</p>
<?
		$admin->stop();
	}
		
	if ($r == "p") {
		$publisher = true;
	} elseif ($r == "e") {
		$publisher = false;
	} else {
		die("You do not have access to this page.");
	}
	
	if ($page[0] != "p") {
?>
<p class="page_url"><a href="<?=$cms->getLinkById($page)?>" target="_blank" title="View Page"><img src="<?=$icon_root?>world.png" alt="" /> <?=$cms->getLinkById($page)?></a></p>
<?
	}
?>
<h1><span class="edit_page"></span><?=$pdata["nav_title"]?></h1>
<?
	include bigtree_path("admin/modules/pages/_nav.php");
	// Force your way through the page lock
	if (isset($_GET["force"])) {
		$f = sqlfetch(sqlquery("SELECT * FROM bigtree_locks WHERE `table` = 'bigtree_pages' AND item_id = '$page'"));
		sqlquery("UPDATE bigtree_locks SET user = '".$_SESSION["bigtree"]["id"]."', last_accessed = NOW() WHERE id = '".$f["id"]."'");
	}
	
	// Check for a page lock
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_locks WHERE `table` = 'bigtree_pages' AND item_id = '$page'"));
	if ($f && $f["user"] != $_SESSION["bigtree"]["id"] && strtotime($f["last_accessed"]) > (time()-300)) {
		include bigtree_path("admin/modules/pages/_locked.php");
		$admin->stop();
	}
	
	if ($f) {
		sqlquery("UPDATE bigtree_locks SET last_accessed = NOW(), user = '".$_SESSION["bigtree"]["id"]."' WHERE id = '".$f["id"]."'");
		$lockid = $f["id"];
	} else {
		sqlquery("INSERT INTO bigtree_locks (`table`,`item_id`,`user`,`title`) VALUES ('bigtree_pages','$page','".$_SESSION["bigtree"]["id"]."','Page')");
		$lockid = sqlid();
	}
	
	// SEO Checks
	$seo = $admin->getPageSEORating($pdata,$resources);
	$seo_rating = $seo["score"];
	$seo_recommendations = $seo["recommendations"];
	$seo_color = $seo["color"];
	
	$action = "update";
	include bigtree_path("admin/modules/pages/_form.php");
?>