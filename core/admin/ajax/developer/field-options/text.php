<?
	$sub_types = array(
		"" => "",
		"name" => "Name",
		"address" => "Address",
		"email" => "Email",
		"website" => "Website",
		"phone" => "Phone Number"
	);
?>
<fieldset>
	<label>Sub Type</label>
	<select name="sub_type">
		<? foreach ($sub_types as $type => $desc) { ?>
		<option value="<?=$type?>"<? if ($type == $d["sub_type"]) { ?> selected="selected"<? } ?>><?=$desc?></option>
		<? } ?>
	</select>
</fieldset>
