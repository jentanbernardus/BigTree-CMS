<?
	if (isset($_POST["view"])) {
		$view = BigTreeAutoModule::getView($_POST["view"]);
	}
	
	$module_id = BigTreeAutoModule::getModuleForView($view);
	$permission = $admin->getAccessLevel($module_id);
	
	// Edit Suffix
	$suffix = $view["suffix"] ? "-".$view["suffix"] : "";
		
	// Setup the preview action if we have a preview URL and field.
	if ($view["preview_url"]) {
		$view["actions"]["preview"] = "on";
	}
	
	$module = $admin->getModule($module_id);
	
	// Retrieve our results.
	if (isset($_POST["search"]) && $_POST["search"]) {
		$view["options"]["per_page"] = 10000000;
		$r = BigTreeAutoModule::getSearchResults($view,0,$_POST["search"],"position DESC, id ASC",false,$module);
		$items = $r["results"];
	} else {
		$items = BigTreeAutoModule::getViewData($view,"position DESC, id ASC","both",$module);
		$_POST["search"] = "";
	}
	
	foreach ($items as $item) {
		// Stop the item status notice
		if (!isset($item["status"])) {
			$item["status"] = false;
		}
		if ($item["status"] == "p") {
			$status = "Pending";
			$status_class = "pending";
		} elseif ($item["status"] == "c") {
			$status = "Changed";
			$status_class = "pending";
		} else {
			$status = "Published";
			$status_class = "published";
		}
?>
<li id="row_<?=$item["id"]?>" class="<?=$status_class?>">
	<?
		$x = 0;
		foreach ($view["fields"] as $key => $field) {
			$x++;
			$value = $item["column$x"];
	?>
	<section class="view_column" style="width: <?=$field["width"]?>px;">
		<? if ($x == 1 && $permission == "p" && !$_POST["search"]) { ?>
		<span class="icon_sort"></span>
		<? } ?>
		<?=$value?>
	</section>
	<?
		}
	?>
	<section class="view_status status_<?=$status_class?>"><?=$status?></section>
	<?
		$iperm = ($permission == "p") ? "p" : $admin->getCachedAccessLevel($module,$item,$view["table"]);
		foreach ($view["actions"] as $action => $data) {
			if ($data == "on") {
				if (($action == "delete" || $action == "approve" || $action == "feature" || $action == "archive") && $iperm != "p") {
					if ($action == "delete" && $item["pending_owner"] == $admin->ID) {
						$class = "icon_delete";
					} else {
						$class = "icon_disabled";
					}
				} else {
					$class = $admin->getActionClass($action,$item);
				}
				
				if ($action == "preview") {
					$link = rtrim($view["preview_url"],"/")."/".$item["id"].'/" target="_preview';
				} else {
					$link = "#".$item["id"];
				}
	?>
	<section class="view_action action_<?=$action?>"><a href="<?=$link?>" class="<?=$class?>"></a></section>
	<?
			} else {
				$data = json_decode($data,true);
				$link = $mpage.$data["route"]."/".$item["id"]."/";
				if ($data["function"]) {
					eval('$link = '.$data["function"].'($item);');
				}
	?>
	<section class="view_action"><a href="<?=$link?>" class="<?=$data["class"]?>"></a></section>
	<?
			}
		}
	?>
</li>
<?
	}
?>