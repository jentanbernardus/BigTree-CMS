<p class="error_message" style="display: none;">Errors found! Please fix the highlighted fields before submitting.</p>

<div class="left">
	<fieldset>
		<label class="required">Navigation Title</label>
		<input type="text" name="nav_title" id="nav_title" value="<?=$pdata["nav_title"]?>" tabindex="1" class="required" />
	</fieldset>
	
	<fieldset>
		<label class="required">Page Title</label>
		<input type="text" name="title" id="page_title" tabindex="3" value="<?=$pdata["title"]?>" class="required" />
	</fieldset>
	
	<fieldset>
		<? if ($parent > 0 || $admin->Level > 1) { ?>
		<input type="checkbox" name="in_nav" <? if (!$pdata || $pdata["in_nav"]) { ?>checked="checked" <? } ?>class="checkbox" tabindex="5" /> <label class="for_checkbox">Visible In Navigation</label>
		<? } else { ?>
		<input type="checkbox" name="in_nav" <? if ($pdata["in_nav"]) { ?>checked="checked" <? } ?>disabled="disabled" class="checkbox" tabindex="5" /> Visible In Navigation <em>(you may not add visible top level navigation)</em>
		<? } ?>
	</fieldset>
</div>

<div class="right">
	<fieldset>
		<label>URL Route <small>Leave Blank to Auto Generate</small></label>
		<input type="text" name="route" value="<?=$pdata["route"]?>" tabindex="2" />
	</fieldset>
	
	<fieldset>
		<label>Publish Date <small>Leave Blank to Publish Immediately</small></label>
		<input type="text" class="date" id="publish_at" name="publish_at" tabindex="4" value="<? if ($pdata["publish_at"]) { echo date("Y-m-d",strtotime($pdata["publish_at"])); } ?>" />
	</fieldset>
	<script type="text/javascript">
		$("#publish_at").datepicker({ durration: 200, showAnim: "slideDown" });
	</script>
</div>