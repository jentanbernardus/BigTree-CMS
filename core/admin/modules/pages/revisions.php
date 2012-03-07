<?
	$page = end($path);
	$access = $admin->getPageAccessLevelByUser($page,$admin->ID);
	if ($access != "p") {
		$admin->stop("You must be a publisher to manage revisions.");
	}
	$pdata = $cms->getPage($page);
	
	if (!$pdata) {
?>
<h1><span class="error"></span>Error</h1>
<p class="error">The page you are trying to edit no longer exists.</p>
<?
		$admin->stop();
	}
?>
<h1><span class="refresh"></span><?=$pdata["nav_title"]?></h1>	
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
	
	// See if there's a draft copy.
	$draft = sqlfetch(sqlquery("SELECT date,user,id FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND item_id = '".$pdata["id"]."'"));
	
	// Get the current published copy.  We're going to just pull a few columns or I'd use getPage here.
	$current = sqlfetch(sqlquery("SELECT updated_at,last_edited_by FROM bigtree_pages WHERE id = '".$pdata["id"]."'"));
	$current_author = $admin->getUser($current["last_edited_by"]);
	
	// Get all previous revisions, add them to the saved or unsaved list
	$revisions = array();
	$saved = array();
	$q = sqlquery("SELECT bigtree_users.name, bigtree_page_versions.saved, bigtree_page_versions.saved_description, bigtree_page_versions.updated_at, bigtree_page_versions.id FROM bigtree_page_versions JOIN bigtree_users ON bigtree_page_versions.author = bigtree_users.id WHERE page = '".$pdata["id"]."' ORDER BY updated_at DESC");
	while ($f = sqlfetch($q)) {
		if ($f["saved"]) {
			$saved[] = $f;
		} else {
			$revisions[] = $f;
		}
	}
?>
<div class="table">
	<summary><h2><span class="visible"></span>Unpublished Drafts</h2></summary>
	<header>
		<span class="pages_last_edited">Last Edited</span>
		<span class="pages_draft_author">Draft Author</span>
		<span class="pages_publish">Publish</span>
		<span class="pages_edit">Edit</span>
		<span class="pages_delete">Delete</span>
	</header>
	<ul>
		<?
			if ($draft) {
				$draft_author = $admin->getUser($draft["user"]);
		?>
		<li>
			<section class="pages_last_edited"><?=date("F j, Y @ g:ia",strtotime($draft["date"]))?></section>
			<section class="pages_draft_author"><?=$draft_author["name"]?></section>
			<section class="pages_publish"><a class="icon_publish" href="#"></a></section>
			<section class="pages_edit"><a class="icon_edit" href="<?=$admin_root?>pages/edit/<?=$pdata["id"]?>/"></a></section>
			<section class="pages_delete"><a class="icon_delete" href="<?=$admin_root?>ajax/pages/delete-draft/?id=<?=$pdata["id"]?>"></a></section>
		</li>
		<?
			}
		?>
	</ul>
</div>
<div class="table">
	<summary><h2><span class="published"></span>Published Revisions</h2></summary>
	<header>
		<span class="pages_last_edited">Published</span>
		<span class="pages_draft_author">Author</span>
		<span class="pages_delete">Save</span>
		<span class="pages_publish">New Draft</span>
		<span class="pages_edit">Delete</span>
	</header>
	<ul>
		<li class="active">
			<section class="pages_last_edited"><?=date("F j, Y @ g:ia",strtotime($current["updated_at"]))?></section>
			<section class="pages_draft_author"><?=$current_author["name"]?><span class="active_draft">Active</span></section>
			<section class="pages_delete"><a href="#" class="icon_save"></a></section>
			<section class="pages_publish"><a href="#" class="icon_draft"></a></section>
			<section class="pages_edit"></span>
		</li>
		<? foreach ($revisions as $r) { ?>
		<li>
			<section class="pages_last_edited"><?=date("F j, Y @ g:ia",strtotime($r["updated_at"]))?></section>
			<section class="pages_draft_author"><?=$r["name"]?></section>
			<section class="pages_delete"><a href="#<?=$r["id"]?>" class="icon_save"></a></section>
			<section class="pages_publish"><a href="#<?=$r["id"]?>" class="icon_draft"></a></section>
			<section class="pages_edit"><a href="#<?=$r["id"]?>" class="icon_delete"></a></span>
		</li>
		<? } ?>
	</ul>
</div>
<div class="table">
	<summary><h2><span class="saved"></span>Saved Revisions</h2></summary>
	<header>
		<span class="pages_last_edited">Saved</span>
		<span class="pages_draft_description">Description</span>
		<span class="pages_publish">New Draft</span>
		<span class="pages_edit">Delete</span>
	</header>
	<ul>
		<? foreach ($saved as $r) { ?>
		<li>
			<section class="pages_last_edited"><?=date("F j, Y @ g:ia",strtotime($r["updated_at"]))?></section>
			<section class="pages_draft_description"><?=$r["saved_description"]?></section>
			<section class="pages_publish"><a href="#<?=$r["id"]?>" class="icon_draft"></a></section>
			<section class="pages_edit"><a href="#<?=$r["id"]?>" class="icon_delete"></a></span>
		</li>
		<? } ?>
	</ul>
</div>
<script type="text/javascript">
	var active_draft = <? if ($draft) { ?>true<? } else { ?>false<? } ?>;
	var page = "<?=$pdata["id"]?>";
	var page_updated_at = "<?=$pdata["updated_at"]?>";
	lockTimer = setInterval("$.ajax('<?=$admin_root?>ajax/pages/refresh-lock/', { type: 'POST', data: { id: '<?=$lockid?>' } });",60000);
	
	$(".icon_save").click(function() {
		new BigTreeDialog("Save Revision",'<fieldset><label>Short Description <small>(quick reminder of what\'s special about this revision)</small></label><input type="text" name="description" /></fieldset>',$.proxy(function(d) {
			$.ajax("<?=$admin_root?>ajax/pages/save-revision/", { type: "POST", data: { id: BigTree.CleanHref($(this).attr("href")), description: d.description }, complete: function() {
				window.location.reload();
			}});
		},this));
		
		return false;
	});
	
	$(".icon_delete").click(function() {
		href = $(this).attr("href");
		if (href.substr(0,1) == "#") {
			new BigTreeDialog("Delete Revision",'<p class="confirm">Are you sure you want to delete this revision?</p>',$.proxy(function() {
				$.ajax("<?=$admin_root?>ajax/pages/delete-revision/?id=" + BigTree.CleanHref($(this).attr("href")));
				$(this).parents("li").remove();
				BigTree.growl("Pages","Deleted Revision");
			},this),"delete",false,"OK");
		} else {
			new BigTreeDialog("Delete Draft",'<p class="confirm">Are you sure you want to delete this draft?</p>',$.proxy(function() {
				$.ajax($(this).attr("href"));
				$(this).parents("li").remove();
				BigTree.growl("Pages","Deleted Draft");
			},this),"delete",false,"OK");
		}
		
		return false;
	});
	
	$(".icon_draft").click(function() {
		if (active_draft) {
			new BigTreeDialog("Use Revision",'<p class="confirm">Are you sure you want to overwrite your existing draft with this revision?</p>',$.proxy(function() {
				document.location.href = "<?=$admin_root?>ajax/pages/use-draft/?id=" + BigTree.CleanHref($(this).attr("href"));
			},this),"",false,"OK");
		} else {
			document.location.href = "<?=$admin_root?>ajax/pages/use-draft/?id=" + BigTree.CleanHref($(this).attr("href"));
		}
		return false;
	});
</script>