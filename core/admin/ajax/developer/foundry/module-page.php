<?
	$search = $_GET["search"] ? $_GET["search"] : "";
	$sort = $_GET["sort"] ? $_GET["sort"] : "last_updated";
	$sort_direction = $_GET["sort_direction"] ? $_GET["sort_direction"] : "SORT_DESC";
	
	if ($sort_direction == "SORT_ASC")
		$sort_direction = SORT_ASC;
	else
		$sort_direction = SORT_DESC;
					
	$perpage = $options["per_page"] ? $options["per_page"] : 15;
	$page = $_GET["page"] ? $_GET["page"] : 0;
	
	$types = $admin->getFoundryModules();
	$filtered_types = array();
	$names = array();
	$authors = array();
	$companies = array();
	$last_updated = array();
	foreach ($types as $type) {
		if (!$search || stripos($type["author"],$search) !== false || stripos($type["name"],$search) !== false || stripos($type["company"],$search) !== false) {
			$filtered_types[] = $type;
			$names[] = $type["name"];
			$authors[] = $type["author"];
			$companies[] = $type["company"];
			$last_updated[] = strtotime($type["last_updated"]);
		}
	}
	if ($sort == "author") {
		array_multisort($authors,SORT_STRING,$sort_direction,$filtered_types);
	} elseif ($sort == "name") {
		array_multisort($names,SORT_STRING,$sort_direction,$filtered_types);
	} elseif ($sort == "company") {
		array_multisort($companies,SORT_STRING,$sort_direction,$filtered_types);
	} elseif ($sort == "last_updated") {
		array_multisort($last_updated,SORT_NUMERIC,$sort_direction,$filtered_types);
	}
	
	$pages = ceil(count($filtered_types) / 15);
	if ($pages == 0)
		$pages = 1;

	if ($page == 0) {
		if ($pages > 1)
			$np = true;
		$pp = false;
	} elseif ($page == ($pages - 1)) {
		$np = false;
		$pp = true;
	} else {
		$np = true;
		$pp = true;
	}
	$npn = $page + 1;
	$ppn = $page - 1;
?>

<div class="search_results">
	<ul class="page_numbers">
		<li class="previous_page"><? if ($pp) { ?><a href="#<?=$ppn?>"><? } else { ?><a href="#" class="disabled"><? } ?>&lsaquo;</a></li>
		<?
			$parray = get_page_array($page,$pages);
			$x = 0;
			while ($x < count($parray)) {
				$p = $parray[$x];
				if (($page + 1) == $p && is_numeric($p))
					echo '<li><a href="#" class="active">'.$p.'</a></li>';
				elseif (is_numeric($p))
					echo '<li><a href="#'.($p - 1).'">'.$p.'</a></li>';
				else
					echo '<li><span>...</span></li>';
				$x++;
			}
		?>
		<li class="next_page"><? if ($np) { ?><a href="#<?=$npn?>"><? } else { ?><a href="#" class="disabled"><? } ?>&rsaquo;</a></li>
	</ul>
</div>
<br class="clear" /><br />
<dl class="table" id="sort_table">
	<dt>
		<span class="foundry_name"><a href="<? if ($sort == "name" && $sort_direction == SORT_ASC) { ?>SORT_DESC<? } else { ?>SORT_ASC<? } ?>" class="sort_column" name="name">Module <? if ($sort == "name" && $sort_direction == SORT_ASC) { ?>&#9650;<? } elseif ($sort == "name") { ?>&#9660;<? } ?></a></span>
		<span class="foundry_version">Version</span>
		<span class="foundry_author"><a href="<? if ($sort == "author" && $sort_direction == SORT_ASC) { ?>SORT_DESC<? } else { ?>SORT_ASC<? } ?>" class="sort_column" name="author">Author <? if ($sort == "author" && $sort_direction == SORT_ASC) { ?>&#9650;<? } elseif ($sort == "author") { ?>&#9660;<? } ?></a></span>
		<span class="foundry_company"><a href="<? if ($sort == "company" && $sort_direction == SORT_ASC) { ?>SORT_DESC<? } else { ?>SORT_ASC<? } ?>" class="sort_column" name="company">Company <? if ($sort == "company" && $sort_direction == SORT_ASC) { ?>&#9650;<? } elseif ($sort == "company") { ?>&#9660;<? } ?></a></span>
		<span class="foundry_updated"><a href="<? if ($sort == "last_updated" && $sort_direction == SORT_ASC) { ?>SORT_DESC<? } else { ?>SORT_ASC<? } ?>" class="sort_column" name="last_updated">Last Updated <? if ($sort == "last_updated" && $sort_direction == SORT_ASC) { ?>&#9650;<? } elseif ($sort == "last_updated") { ?>&#9660;<? } ?></a></span>
		<span class="action">View</span>
		<span class="action">Install</span>
	</dt>
	<? foreach ($filtered_types as $item) { ?>
	<dd>
		<ul>
			<li class="foundry_name"><?=$item["name"]?></li>
			<li class="foundry_version"><?=$item["primary_version"]?>.<?=$item["secondary_version"]?>.<?=$item["tertiary_version"]?></li>
			<li class="foundry_author"><?=$item["author"]?></li>
			<li class="foundry_company"><?=$item["company"]?></li>
			<li class="foundry_updated"><?=date("F j, Y",strtotime($item["last_updated"]))?></li>
			<li class="action">
				<a href="#<?=$item["id"]?>" class="button_view" title="View Details"></a>
			</li>
			<li class="action">
				<? if ($f = sqlfetch(sqlquery("SELECT * FROM bigtree_module_packages WHERE foundry_id = '".mysql_real_escape_string($item["id"])."'"))) { ?>
				<? if ($f["primary_version"] < $item["primary_version"] || ($f["primary_version"] == $item["primary_version"] && $f["secondary_version"] < $item["secondary_version"]) || ($f["primary_version"] == $item["primary_version"] && $f["secondary_version"] == $item["secondary_version"] && $f["tertiary_version"] < $item["tertiary_version"])) { ?>
				<a href="#<?=$item["id"]?>" class="button_update" title="Update"></a>
				<? } else { ?>
				<img src="<?=$aroot?>images/icon_approve.gif" alt="Installed" title="Installed" />
				<? } ?>
				<? } else { ?>
				<a href="#<?=$item["id"]?>" class="button_download" title="Download"></a>				
				<? } ?>
			</li>
		</ul>
	</dd>
	<? } ?>
</dl>