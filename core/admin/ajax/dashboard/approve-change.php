<?
	header("Content-type: text/javascript");
	$cid = mysql_real_escape_string($_GET["change"]);
	
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '$cid'"));
	$table = $f["table"];
	$changes = json_decode($f["changes"],true);
	$mtm_changes = json_decode($f["mtm_changes"],true);
	$type = $f["type"];
	$id = $f["item_id"];
	$module = $f["module"];
	
	// See if we have permission.
	$ok = false;	
	if ($module) {
		$perm = $admin->getAccessLevel($module,$changes,$table);
	} else {
		if ($type == "EDIT" || $type == "DELETE") {
			$perm = $admin->getPageAccessLevelByUser($id,$admin->ID);
		} else {
			$perm = $admin->getPageAccessLevelByUser($changes["parent"],$admin->ID);
		}
	}
	
	if ($perm != "p") {
?>
BigTree.growl("Pending Changes","You do not have permission to approve this change.");
<?
	} else {
		// Actually do something.
		if ($type == "DELETE") {
			sqlquery("DELETE FROM $table WHERE id = '$id'");
		}
		if ($type == "EDIT") {
			$ustring = array();
			$columns = sqlcolumns($table);
			foreach ($changes as $key => $val) {
				if (is_array($val))
					$val = json_encode($val);
				if (isset($columns[$key]))
					$ustring[] = "`$key` = '".mysql_real_escape_string($val)."'";
			}
			sqlquery("UPDATE $table SET ".implode(", ",$ustring)." WHERE id = '$id'");
			BigTreeAutoModule::recacheItem($id,$table);
		}
		if ($type == "NEW") {			
			if ($table == "bigtree_pages") {
				$admin->createPage($changes);
			} else {
				BigTreeAutoModule::createItem($table,$changes,$mtm_changes);
			}
		}
		
		BigTreeAutoModule::deletePendingItem($table,$cid);
?>
BigTree.growl("Pending Changes","Change request approved.");
<?
	}
?>