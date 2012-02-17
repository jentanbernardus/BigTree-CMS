<?
	$page_data = $admin->getPendingPage(is_array($page) ? $page["id"] : $page);
	$age = floor((time() - strtotime($page_data["updated_at"])) / (60 * 60 * 24));
	$seo = $admin->getPageSEORating($page_data,$page_data["resources"]);
	if (is_numeric($page_data["id"])) {
		$url = $www_root.$page_data["path"]."/";
		$link_type = "Live";
		if ($page_data["changes_applied"]) {
			$status = "Changes Pending";
		} else {
			$status = "Published";
		}
	} else {
		$url = $www_root."_preview-pending/".substr($page_data["id"],1)."/";
		$link_type = "Preview";
		$status = "Unpublished";
	}
	
	$open = $_COOKIE["bigtree_default_properties_open"] ? true : false;
?>
<h3 class="properties"><span>Properties</span><span class="icon_small icon_small_caret_<? if ($open) { ?>down<? } else { ?>right<? } ?>"></span></h3>
<section class="property_block"<? if (!$open) { ?> style="display: none;"<? } ?>>
	<article>
		<label>Status</label>
		<p class="<?=str_replace(" ","_",strtolower($status))?>"><?=$status?></p>
	</article>
	<article>
		<label>SEO Rating</label>
		<p style="color: <?=$seo["color"]?>"><span><?=$seo["score"]?>%</span><a href="#" class="icon_small icon_small_help"></a></p>
	</article>
	<article>
		<label>Content Age</label>
		<p><?=$age?> Days</p>
	</article>
	<article class="link">
		<label><?=$link_type?> URL</label>
		<p><a href="<?=$url?>" target="_blank"><?=$url?></a></p>
	</article>
</section>
<hr <? if ($open) { ?>style="display: none;" <? } ?>/>