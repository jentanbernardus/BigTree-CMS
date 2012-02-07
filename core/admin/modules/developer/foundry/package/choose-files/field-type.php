<?
	$js = array("foundry.js");
	$type = $admin->getFieldType(end($commands));
	$files = json_decode($type["files"],true);
	if (empty($files)) {
		if (file_exists($server_root."custom/admin/form-field-types/draw/".$type["id"].".php"))
			$files[] = $server_root."custom/admin/form-field-types/draw/".$type["id"].".php";
		if (file_exists($server_root."custom/admin/form-field-types/process/".$type["id"].".php"))
			$files[] = $server_root."custom/admin/form-field-types/process/".$type["id"].".php";
	}
?>
<h3 class="foundry">Package Field Type: Choose Files</h3>
<p>Please select all the files required for the Field Type &ldquo;<?=$type["name"]?>&rdquo;</p>
<form class="module" method="post" action="<?=$aroot?>developer/foundry/package/release-notes/field-type/">
	<input type="hidden" name="id" value="<?=htmlspecialchars($type["id"])?>" />
	<div class="package_column">
		<strong>Package Files</strong>
		<ul class="package_files">
			<? foreach ($files as $file) { $parts = safe_pathinfo($file); ?>
			<li>
				<input type="hidden" name="files[]" value="<?=htmlspecialchars($file)?>" />
				<a href="#<?=$table?>" class="delete"></a>
				<span><?=$file?></span>
			</li>
			<? } ?>
		</ul>
		<div class="add_file">
			<a class="browse" href="#">Browse For File</a>
		</div>
	</div>
	<br class="clear" />
	<input type="submit" class="button white" value="Build Package" />
</form>

<script type="text/javascript">
	
	$(".browse").click(function() {
		new BigTreeFileBrowser("","",function(data) {
			li = $('<li>');
			li.html('<input type="hidden" name="files[]" value="' + data.directory + data.file + '" /><a href="#" class="delete"></a>' + data.directory + data.file);
			$(".package_files").append(li);
		});
		
		return false;
	});
	
	$(".package_files a.delete").live("click",function() {
		$(this).parents("li").remove();
		return false;
	});
</script>