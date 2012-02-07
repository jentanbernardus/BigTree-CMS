<?
	bigtree_process_get_vars();
	
	if ($type == "list") {
		$options = explode("-----",$list_options);
		foreach ($options as $option) {
			$option = explode("|||||",$option);
			$oplist[$option[0]] = $option[1];
		}
		$data = array(
			"id" => $id,
			"name" => $name,
			"type" => $type,
			"list" => $oplist,
			"optional" => $optional,
			"default_hidden" => $default_hidden
		);
	} elseif ($type == "image" || $t == "crop" || $t == "photo_gallery" || $t == "feature") {
		$data = array(
			"id" => $id,
			"name" => $name,
			"type" => $type,
			"width" => $width,
			"height" => $height,
			"optional" => $optional,
			"default_hidden" => $default_hidden
		);
	} elseif ($type == "poplist") {
		$data = array(
			"id" => $id,
			"name" => $name,
			"type" => $type,
			"table" => $table,
			"table_id" => $table_id,
			"table_descriptor" => $table_descriptor,
			"table_sort" => $table_sort_by,
			"optional" => $optional,
			"default_hidden" => $default_hidden
		);		
	} else {
		$data = array(
			"id" => $id,
			"name" => $name,
			"type" => $type,
			"optional" => $optional,
			"default_hidden" => $default_hidden
		);
	}
?>
<div id="resource-<?=$id?>">
	<input type="hidden" name="resources[]" value="<?=htmlspecialchars(json_encode($data))?>" />
	<?=$name?> (<?=$id?>) <a href="javascript:remove('<?=$id?>');"><font color="red">[Remove]</font></a>
</div>