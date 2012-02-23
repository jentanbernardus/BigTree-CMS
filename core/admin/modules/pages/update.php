<?
	// Initiate the Upload Service class.
	$upload_service = new BigTreeUploadService;

	$page = $_POST["page"];
	
	if ($page[0] == "p") {
		$change_id = substr($page,1);
		$f = $admin->getPendingChange($change_id);
		$pdata = $f["changes"];
		$r = $admin->getPageAccessLevelByUser($pdata["parent"],$admin->ID);
	} else {
		$r = $admin->getPageAccessLevelByUser($page,$admin->ID);
		$pdata = $admin->getPendingPage($page);
	}
	
	// Work out the permissions	
	if ($r == "p") {
		$publisher = true;
	} elseif ($r == "e") {
		$publisher = false;
	} else {
		die("You do not have access to update this page.");
	}
	
	$resources = array();
	$crops = array();
	$fails = array();
	
	// Parse resources
	include bigtree_path("admin/modules/pages/_resource-parse.php");
	// Parse callouts
	include bigtree_path("admin/modules/pages/_callout-parse.php");	
	
	if ($publisher && $_POST["ptype"] == "Save & Publish") {
		// Let's make it happen.
		if ($page[0] == "p") {
			// It's a pending page, so let's create one.
			if (!$_POST["parent"]) {
				$_POST["parent"] = $pdata["parent"];
			}
			
			$page = $admin->createPage($_POST);
			$admin->growl("Pages","Created & Published Page");
			sqlquery("DELETE FROM bigtree_pending_changes WHERE id = '$change_id'");
		} else {
			// It's an existing page.
			$admin->updatePage($page,$_POST);
			$admin->growl("Pages","Updated Page");
		}
	} else {
		if (!$_POST["parent"]) {
			$_POST["parent"] = $pdata["parent"];
		}
		$admin->submitPageChange($page,$_POST);
		$admin->growl("Pages","Saved Page Draft");
	}
	
	sqlquery("DELETE FROM bigtree_locks WHERE `table` = 'bigtree_pages' AND item_id = '$page'");
	
	if (count($crops)) {
		$retpage = $aroot."pages/view-tree/".$pdata["parent"]."/";
		include bigtree_path("admin/modules/pages/_crop.php");
	} elseif (count($fails)) {
		include bigtree_path("admin/modules/pages/_failed.php");
	} else {
		if (end($path) == "preview") {
			$admin->ungrowl();
			header("Location: ".$cms->getPreviewLink($page)."?bigtree_preview_bar=true");
		} else {
			if ($pdata["parent"] == "-1") {
				$pdata["parent"] = 0;
			}
			header("Location: ".$aroot."pages/view-tree/".$pdata["parent"]."/");
		}
		die();
	}
?>