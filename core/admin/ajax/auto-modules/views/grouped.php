<?
	// If it's an AJAX request, get our data.
	if ($_POST["view"]) {
		$view = BigTreeAutoModule::getView($_POST["view"]);
	}
	
	bigtree_clean_globalize_array($view);
	$m = BigTreeAutoModule::getModuleForView($view);
	$perm = $admin->getAccessLevel($m);
	$module = $admin->getModule($m);

	$suffix = $suffix ? "-".$suffix : "";
	$o = $options;
	$view["per_page"] = 10000;
	
	// Setup the preview action if we have a preview URL and field.
	if ($view["preview_url"]) {
		$actions["preview"] = "on";
	}
	
	// Cache the data in case it's not there.
	BigTreeAutoModule::cacheViewData($view);
	
	$query = "SELECT DISTINCT(group_field) FROM bigtree_module_view_cache WHERE view = '".$view["id"]."'";
	if ($o["ot_sort_field"]) {
		$query .= " ORDER BY group_sort_field ".$o["ot_sort_direction"];
	} else {
		$query .= " ORDER BY group_field";
	}
	
	$q = sqlquery($query);
	$gc = 0;
	while ($f = sqlfetch($q)) {
		if ($o["other_table"]) {
			$g = sqlfetch(sqlquery("SELECT `".$o["title_field"]."` AS `title` FROM `".$o["other_table"]."` WHERE id = '".$f["group_field"]."'"));
			$title = $g["title"];
		} else {
			$title = $f["group_field"];
		}
		
		if ($o["draggable"]) {
			$r = BigTreeAutoModule::getSearchResults($view,0,$_POST["search"],"position DESC, id ASC","",$f["group_field"],$module);
		} else {
			$r = BigTreeAutoModule::getSearchResults($view,0,$_POST["search"],$o["sort_field"],$o["sort_direction"],$f["group_field"],$module);
		}
		
		if (count($r["results"])) {
			$gc++;
?>
<header class="group"><?=$title?></header>
<header>
	<?
			$x = 0;
			foreach ($fields as $key => $field) {
				$x++;
	?>
	<span class="view_column" style="width: <?=$field["width"]?>px;"><?=$field["title"]?></span>
	<?
			}
			
			foreach ($actions as $action => $status) {
	?>
	<span class="view_action"><?=$action?></span>
	<?
			}
	?>
</header>
<ul id="sort_table_<?=$gc?>">
	<? foreach ($r["results"] as $item) { ?>
	<li id="row_<?=$item["id"]?>"<? if ($item["bigtree_pending"]) { ?> class="pending"<? } ?><? if ($item["bigtree_changes"]) { ?> class="changes"<? } ?>>
		<?
			$x = 0;
			foreach ($fields as $key => $field) {
				$x++;
				$value = $item["column$x"];
		?>
		<section class="view_column" style="width: <?=$field["width"]?>px;">
			<? if ($x == 1 && $perm == "p" && !$_POST["search"] && $o["draggable"]) { ?>
			<span class="icon_sort"></span>
			<? } ?>
			<?=$value?>
		</section>
		<?
			}

			$iperm = ($perm == "p") ? "p" : $admin->getCachedAccessLevel($module,$item,$view["table"]);
			foreach ($actions as $action => $data) {
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
	<? } ?>
</ul>
<?
		}
	}
?>	