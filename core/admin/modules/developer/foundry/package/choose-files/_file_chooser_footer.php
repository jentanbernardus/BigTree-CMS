	<p><img src="<?=$icon_root?>drive_cd.png" alt="" /> = with data &nbsp; &nbsp; <img src="<?=$icon_root?>application_view_columns.png" alt="" /> = structure only &nbsp; &nbsp; <small>(click to switch)</small></p>
	<ul>
		<li class="package_column">
			<strong>Tables</strong>
			<ul class="package_tables">
				<?
					foreach ($tables as $tinfo) {
						list($table,$type) = explode("#",$tinfo);
				?>
				<li>
					<input type="hidden" name="tables[]" value="<?=$tinfo?>" />
					<a href="#" class="delete"></a>
					<a href="#<?=$table?>" class="<? if ($type == "with-data") { ?>data<? } else { ?>structure<? } ?>"></a>
					<?=$table?>
				</li>
				<? } ?>
			</ul>
			<div class="add_table adder">
				<a class="add" href="#"></a>
				<select id="add_table">
					<? bigtree_table_select(); ?>
				</select>
			</div>
		</li>
		<li class="package_column" id="class_files">
			<strong>Class Files</strong>
			<ul class="package_files">
				<? foreach ($class_files as $mid => $file) { ?>
				<li>
					<input type="hidden" name="class_files[<?=$mid?>]" value="<?=htmlspecialchars($file)?>" />
					<a href="#<?=$table?>" class="delete"></a>
					<span><?=$file?></span>
				</li>
				<? } ?>
			</ul>
		</li>
		<li class="package_column" id="required_files">
			<strong>Required Includes</strong>
			<ul class="package_files">
				<? foreach ($required_files as $file) { $parts = safe_pathinfo($file); ?>
				<li>
					<input type="hidden" name="required_files[]" value="<?=htmlspecialchars($file)?>" />
					<a href="#<?=$table?>" class="delete"></a>
					<span><?=$file?></span>
				</li>
				<? } ?>
			</ul>
			<div class="add_file">
				<a class="browse required_browse" href="#">Browse For File</a>
			</div>
		</li>
		<li class="package_column last" id="other_files">
			<strong>Other Files</strong>
			<ul class="package_files">
				<? foreach ($other_files as $file) { $parts = safe_pathinfo($file); ?>
				<li>
					<input type="hidden" name="other_files[]" value="<?=htmlspecialchars($file)?>" />
					<a href="#<?=$table?>" class="delete"></a>
					<span><?=$file?></span>
				</li>
				<? } ?>
			</ul>
			<div class="add_file adder">
				<a class="browse other_browse" href="#">Browse For File</a>
			</div>
		</li>
		<li class="package_column clear">
			<strong>Templates</strong>
			<ul class="package_tables">
				<? foreach ($templates as $template) { ?>
				<li>
					<input type="hidden" name="templates[]" value="<?=$template?>" />
					<a href="#" class="delete"></a>
					<?=$template?>
				</li>
				<? } ?>
			</ul>
			<div class="add_template adder">
				<a class="add" href="#"></a>
				<select id="add_template">
					<?
						$q = sqlquery("SELECT * FROM bigtree_templates ORDER BY id");
						while ($f = sqlfetch($q)) {
					?>
					<option value="<?=$f["id"]?>"><?=$f["id"]?></option>
					<?
						}
					?>
				</select>
			</div>
		</li>
		<li class="package_column">
			<strong>Callouts</strong>
			<ul class="package_tables">
			</ul>
			<div class="add_callout adder">
				<a class="add" href="#"></a>
				<select id="add_callout">
					<?
						$q = sqlquery("SELECT * FROM bigtree_callouts ORDER BY id");
						while ($f = sqlfetch($q)) {
					?>
					<option value="<?=$f["id"]?>"><?=$f["id"]?></option>
					<?
						}
					?>
				</select>
			</div>
		</li>
		<li class="package_column">
			<strong>Settings</strong>
			<ul class="package_tables">
				<? foreach ($settings as $setting) { ?>
				<li>
					<input type="hidden" name="settings[]" value="<?=$setting?>" />
					<a href="#" class="delete"></a>
					<?=$setting?>
				</li>
				<? } ?>
			</ul>
			<div class="add_setting adder">
				<a class="add" href="#"></a>
				<select id="add_setting">
					<?
						$q = sqlquery("SELECT * FROM bigtree_settings ORDER BY id");
						while ($f = sqlfetch($q)) {
					?>
					<option value="<?=$f["id"]?>"><?=$f["id"]?></option>
					<?
						}
					?>
				</select>
			</div>
		</li>
		<li class="package_column last">
			<strong>Feeds</strong>
			<ul class="package_tables">
				<? foreach ($feeds as $feed => $name) { ?>
				<li>
					<input type="hidden" name="feeds[]" value="<?=$feed?>" />
					<a href="#" class="delete"></a>
					<?=$name?>
				</li>
				<? } ?>
			</ul>
			<div class="add_feed adder">
				<a class="add" href="#"></a>
				<select id="add_feed">
					<?
						$q = sqlquery("SELECT * FROM bigtree_feeds ORDER BY name");
						while ($f = sqlfetch($q)) {
					?>
					<option value="<?=$f["id"]?>"><?=$f["name"]?></option>
					<?
						}
					?>
				</select>
			</div>
		</li>
	</ul>
	<br class="clear" />
	<input type="submit" class="button white" value="Build Package" />
</form>

<script type="text/javascript">
	$(".add_table a").click(function() {
		li = $('<li>');
		table = $("#add_table").val();
		li.html('<input type="hidden" name="tables[]" value="' + table + '#structure" /><a href="#" class="delete"></a><a href="#' + table + '" class="structure"></a>' + table);
		$(this).parents("div").prev("ul").append(li);
		
		return false;
	});
	
	$(".add_callout a").click(function() {
		li = $('<li>');
		callout = $("#add_callout").val();
		li.html('<input type="hidden" name="callouts[]" value="' + callout + '" /><a href="#" class="delete"></a>' + callout);
		$(this).parents("div").prev("ul").append(li);
		
		return false;
	});
	
	$(".add_template a").click(function() {
		li = $('<li>');
		template = $("#add_template").val();
		li.html('<input type="hidden" name="templates[]" value="' + template + '" /><a href="#" class="delete"></a>' + template);
		$(this).parents("div").prev("ul").append(li);
		
		return false;
	});
	
	$(".add_setting a").click(function() {
		li = $('<li>');
		setting = $("#add_setting").val();
		li.html('<input type="hidden" name="settings[]" value="' + setting + '" /><a href="#" class="delete"></a>' + setting);
		$(this).parents("div").prev("ul").append(li);
		
		return false;
	});
	
	$(".add_feed a").click(function() {
		ev.stop();
		li = $('<li>');
		add_feed = $("#add_feed").get(0);
		feed = $("#add_feed").val();
		feed_text = add_feed.options[add_feed.selectedIndex].text;
		li.html('<input type="hidden" name="feeds[]" value="' + feed + '" /><a href="#" class="delete"></a>' + feed_text);
		$(this).parents("div").prev("ul").append(li);
	});
	
	$(".class_browse").click(function() {
		new BigTreeFileBrowser("","",function(data) {
			doneSelectFile("class_files",data);
		});
		
		return false;
	});
	
	$(".required_browse").click(function() {
		new BigTreeFileBrowser("","",function(data) {
			doneSelectFile("required_files",data);
		});
		
		return false;
	});
	
	$(".other_browse").click(function() {
		new BigTreeFileBrowser("","",function(data) {
			doneSelectFile("other_files",data);
		});
		
		return false;
	});
	
	function doneSelectFile(column,data) {
		li = $('<li>');
		li.html('<input type="hidden" name="' + column + '[]" value="' + data.directory + data.file + '" /><a href="#" class="delete"></a>' + data.directory + data.file);
		$("#" + column).find("ul").append(li);
		packageHooks();
	}
	

	$(".package_column a.delete").live("click",function() {
			$(this).parents("li").remove();
			return false;
	});
	
	$(".package_column a.data, .package_column a.structure").live("click",function() {
		table = $(this).attr("href").substr(1);
		if ($(this).hasClass("structure")) {
			$(this).prev("input").val(table + "#with-data");
		} else {
			$(this).prev("input").val(table + "#structure");
		}
		$(this).toggleClass("data").toggleClass("structure");
	});
</script>