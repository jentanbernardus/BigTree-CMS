<?
	$ga_on = $cms->getSetting("google-analytics-profile");
	
	$parent = is_array($commands) ? end($commands) : 0;
	$page = $cms->getPageById($parent,false);
	$ppage = $cms->getPageById($page["parent"],false);
	$parent_access = $admin->getPageAccessLevelByUserId($parent,$admin->ID);
	
	// Setup the page breadcrumb
	if ($parent && $page) {

	} else {
		$breadcrumb = array(
			array("link" => "pages/", "title" => "Pages"),
			array("link" => "pages/view-tree/0/", "title" => "Home")
		);
	}
	
	function local_drawPageTree($nav,$title,$subtitle,$class,$draggable = false) {
		global $aroot,$proot,$admin,$cms,$www_root,$ga_on,$parent_access,$parent;
?>
<div class="table">
	<summary>
		<h2><span class="<?=$class?>"></span><?=$title?><small><?=$subtitle?></small></h2>
	</summary>
	<header>
		<?
			if ($class == "archived") {
		?>
		<span class="pages_title_widest">Title</span>
		<span class="pages_restore">Restore</span>
		<span class="pages_delete">Delete</span>
		<?
			} else {
				if ($ga_on) {
		?>
		<span class="pages_title">Title</span>
		<span class="pages_views">Views</span>
		<?
				} else {
		?>
		<span class="pages_title_wider">Title</span>		
		<?
				}
		?>
		<span class="pages_status">Status</span>
		<span class="pages_archive">Archive</span>
		<span class="pages_edit">Edit</span>
		<?
			}
		?>
	</header>
	<ul id="pages_<?=$class?>">
		<?
			foreach ($nav as $item) {
				$perm = $admin->getPageAccessLevelByUserId($item["id"],$admin->ID);
				
				if ($item["bigtree_pending"]) {
					$status = '<a href="'.$www_root.'_preview-pending/'.$item["id"].'/" target="_blank">Pending</a>';
					$status_class = "pending";
				} elseif (sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND item_id = '".$item["id"]."'"))) {
					$status = '<a href="'.$www_root.'_preview/'.$cms->getFullNavigationPath($item["id"]).'/" target="_blank">Changed</a>';
					$status_class = "pending";
				} elseif (strtotime($item["publish_at"]) > time()) {
					$status = "Scheduled";
					$status_class = "scheduled";
				} else {
					$status = "Published";
					$status_class = "published";
				}
		?>
		<li id="row_<?=$item["id"]?>">
			<section class="pages_title<? if ($class == "archived") { ?>_widest<? } elseif (!$ga_on) { ?>_wider<? } ?>">
				<? if ($parent_access == "p" && !$item["bigtree_pending"] && $draggable) { ?>
				<span class="icon_sort"></span>
				<? } ?>
				<? if ($class != "archived") { ?>
				<a href="<?=$proot?>view-tree/<?=$item["id"]?>/"><?=$item["title"]?></a>
				<? } else { ?>
				<?=$item["title"]?>				
				<? } ?>
			</section>
			<?
				if ($class == "archived") {
			?>
			<section class="pages_restore">
				<a href="<?=$proot?>restore/<?=$item["id"]?>/" title="Restore Page" class="icon_restore"></a>
			</section>
			<section class="pages_delete">
				<a href="<?=$proot?>delete/<?=$item["id"]?>/" title="Delete Page" class="icon_delete"></a>
			</section>
			<?	
				} else {
					if ($ga_on) {
			?>
			<section class="pages_views">
				<a class="tooltip" tooltip="Click to view detailed analytics." href="<?=$proot?>analytics/details/<?=$item["id"]?>/"><?=$admin->getGA30DayViewsForPage($item["id"])?></a>
			</section>
			<?
					}
			?>
			<section class="pages_status status_<?=$status_class?>">
				<?=$status?>
			</section>
			<section class="pages_archive">
				<? if (!$item["bigtree_pending"] && $perm == "p" && ($parent != 0 || $admin->Level > 1)) { ?>
				<a href="<?=$proot?>archive/<?=$item["id"]?>/" title="Archive Page" class="icon_archive"></a>
				<? } elseif ($item["bigtree_pending"] && $perm == "p") { ?>
				<a href="<?=$proot?>delete/<?=$item["id"]?>/" title="Delete Pending Page" class="icon_delete"></a>
				<? } else { ?>
				<span class="icon_no_access"></span>
				<? } ?>
			</section>
			<section class="pages_edit">
				<? if ($perm) { ?>
				<a href="<?=$proot?>edit/<?=$item["id"]?>/" title="Edit Page" class="icon_edit"></a>
				<? } else { ?>
				<span class="icon_no_access"></span>
				<? } ?>
			</section>
			<?
				}
			?>
		</li>
		<?
			}
		?>
	</ul>
</div>
<?
		if ($draggable && $parent_access) {
?>
<script type="text/javascript">
	$("#pages_<?=$class?>").sortable({ items: "li", axis: "y", handle: ".icon_sort", update: function() {
		$.ajax("<?=$aroot?>ajax/pages/order/?id=<?=$parent?>&sort=" + escape($("#pages_<?=$class?>").sortable("serialize")));
	}});
</script>
<?
		}
	}

	if (!$page) {
?>
<h1><span class="error"></span>Error</h1>
<p class="error">The page you are trying to view no longer exists.</p>
<?
	} else {
?>
<h1>
	<? if (!$parent) { ?>
	<span class="home"></span>Home
	<? } else { ?>
	<span class="page"></span><?=$page["nav_title"]?>
	<? } ?>
</h1>
<?
		include bigtree_path("admin/modules/pages/_nav.php");
		
		// Drag Visible Pages
		$nav = array_merge($admin->getNaturalNavigationByParent($parent,1),$admin->getPendingNavigationByParent($parent));
		if (count($nav)) {
			local_drawPageTree($nav,"Visible","","visible",true);
		}
		
		// Draw Hidden Pages
		$nav = array_merge($admin->getHiddenNavigationByParent($parent),$admin->getPendingNavigationByParent($parent,""));	
		if (count($nav)) {
			local_drawPageTree($nav,"Hidden","Not Appearing In Navigation","hidden",false);
		}
		
		// Draw Archived Pages
		$nav = $admin->getArchivedNavigationByParent($parent);
		if (count($nav)) {
			local_drawPageTree($nav,"Archived","Not Accessible By Users","archived",false);
		}
	}
?>