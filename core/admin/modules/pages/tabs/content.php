<div id="callouts_enabled"></div>
<div id="callouts_disabled"></div>

<div id="template_type">
	<? include BigTree::path("admin/ajax/pages/get-template-form.php") ?>
</div>

<div class="tags" id="bigtree_tag_browser">
	<fieldset>
		<label>Tags <img src="<?=ADMIN_ROOT?>images/tag.png" alt="" /></label>
		<ul id="tag_list">
			<?
				if (is_array($pdata["tags"])) {
					foreach ($pdata["tags"] as $tag) {
			?>
			<li><input type="hidden" name="_tags[]" value="<?=$tag["id"]?>" /><a href="#"><?=$tag["tag"]?><span>x</span></a></li>
			<?
					}
				}
			?>
		</ul>
		<input type="text" name="tag_entry" id="tag_entry" />
		<ul id="tag_results" style="display: none;"></ul>
	</fieldset>
</div>