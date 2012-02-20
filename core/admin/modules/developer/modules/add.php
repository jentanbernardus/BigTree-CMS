<?
	$breadcrumb[] = array("title" => "Add Module", "link" => "developer/modules/add/");
	$groups = $admin->getModuleGroups();
?>

<h1><span class="icon_developer_modules"></span>Add Module</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>
<div class="form_container">
	<form method="post" action="<?=$sroot?>create/" class="module">
		<section>
			<p class="error_message" style="display: none;">Errors found! Please fix the highlighted fields before submitting.</p>
			<div class="left">
				<fieldset>
					<label class="required">Name</label>
					<input name="name" class="required" type="text" value="<?=$name?>" />
				</fieldset>
			</div>
			<br class="clear" /><br />
			<fieldset class="developer_module_group">
				<label>Group <small>(if a new group name is chosen, the select box is ignored)</small></label> 
				<input name="group_new" type="text" placeholder="New Group" value="<?=$group_new?>" /><span>OR</span> 
				<select name="group_existing">
					<option value="0"></option>
					<? foreach ($groups as $group) { ?>
					<option value="<?=$group["id"]?>"<? if ($group["id"] == $group_existing) { ?> selected="selected"<? } ?>><?=htmlspecialchars($group["name"])?></option>
					<? } ?>
				</select>
			</fieldset>
			<div class="left">
				<fieldset>
					<label class="required">Related Table</label>
					<select name="table" id="rel_table" class="required">
						<option></option>
						<? bigtree_table_select($table) ?>
					</select>
				</fieldset>
				<fieldset>
					<label class="required">Class Name <small>(will create a class file in custom/inc/modules/)</small></label>
					<input name="class" type="text" value="<?=$class?>" class="required" />
				</fieldset>
				<fieldset>
					<input type="checkbox" name="gbp[enabled]" id="gbp_on" <? if ($gbp["enabled"]) { ?>checked="checked" <? } ?>/>
					<label class="for_checkbox">Enable Advanced Permissions <small>(allows setting permissions on grouped views)</small></label>
				</fieldset>
			</div>
		</section>
		<section class="sub" id="gbp"<? if (!$gbp["enabled"]) { ?> style="display: none;"<? } ?>>
			<div class="left">
				<fieldset>
					<label>Grouping Name <small>(i.e. "Category")</small></label>
					<input type="text" name="gbp[name]" value="<?=htmlspecialchars($gbp["name"])?>" />
				</fieldset>
			</div>
			<br class="clear" /><br />
			<article>
				<fieldset>
					<label>Main Table</label>
					<select name="gbp[table]" class="table_select">
						<option></option>
						<? bigtree_table_select($gbp["table"]) ?>
					</select>
				</fieldset>
				<fieldset name="gbp[group_field]">
					<label>Main Field</label>
					<div>
						<? if ($gbp["table"]) { ?>
						<select name="gbp[group_field]">
							<? bigtree_field_select($gbp["table"],$gbp["group_field"]) ?>
						</select>
						<? } else { ?>
						&mdash;
						<? } ?>
					</div>
				</fieldset>
			</article>
			<article>
				<fieldset>
					<label>Other Table</label>
					<select name="gbp[other_table]" class="table_select">
						<option></option>
						<? bigtree_table_select($gbp["other_table"]) ?>
					</select>
				</fieldset>
				<fieldset name="gbp[title_field]">
					<label>Title Field</label>
					<div>
						<? if ($gbp["title_field"]) { ?>
						<select name="gbp[title_field]">
							<? bigtree_field_select($gbp["other_table"],$gbp["title_field"]) ?>
						</select>
						<? } else { ?>
						&mdash;
						<? } ?>
					</div>
				</fieldset>
			</article>
		</section>
		<footer>
			<input type="submit" class="button blue" value="Create" />
		</footer>
	</form>
</div>
<? include bigtree_path("admin/modules/developer/modules/_module-add-edit-js.php") ?>