<h1><span class="modules"></span>Edit <?=$form["title"]?></h1>
<?
	include BigTree::path("admin/auto-modules/_nav.php");
	$item_id = end($path);

	// Check for a lock on this module, if it exists, tell us, if not, create one.
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_locks WHERE `table` = '".$form["table"]."' AND item_id = '$item_id'"));
	if ($f && $f["user"] != $_SESSION["bigtree"]["id"] && strtotime($f["last_accessed"]) > (time()-300)) {
		include BigTree::path("admin/auto-modules/forms/_locked.php");
	// We're not locked, load the form.
	} else {
		if ($f) {
			sqlquery("UPDATE bigtree_locks SET last_accessed = NOW(), user = '".$_SESSION["bigtree"]["id"]."' WHERE id = '".$f["id"]."'");
			$lockid = $f["id"];
		} else {
			sqlquery("INSERT INTO bigtree_locks (`table`,`item_id`,`user`,`title`) VALUES ('".$form["table"]."','$item_id','".$_SESSION["bigtree"]["id"]."','".mysql_real_escape_string($form["title"])."')");
			$lockid = sqlid();
		}

		$data = BigTreeAutoModule::getPendingItem($form["table"],$item_id);
			
		if (!$data) {
?>
<h1><span class="error"></span>Error</h1>
<p class="error">The item you are trying to edit no longer exists.</p>
<?
		} else {
			$view = BigTreeAutoModule::getRelatedViewForForm($form);				
			$item = $data["item"];
			
			$permission_level = $admin->getAccessLevel($module,$item,$form["table"]);
			
			if (!$permission_level || $permission_level == "n") {
				include BigTree::path("admin/auto-modules/forms/_denied.php");
			} else {
				$many_to_many = $data["mtm"];
				$status = $data["status"];
				$pending_resources = $data["resources"] ? $data["resources"] : array();
				
				$tags = $data["tags"];
					
				include BigTree::path("admin/auto-modules/forms/_form.php");
			}
		}
	}
?>