<?
	if (!$value && isset($options["default_now"]) && $options["default_now"]) {
		$value = date("m/d/Y g:i a");
	}
	
	$validation = isset($options["validation"]) ? " ".$options["validation"] : "";
?>
<fieldset>
	<?
		if ($title) {
	?>
	<label<?=$label_validation_class?>><?=$title?><? if ($subtitle) { ?> <small><?=$subtitle?></small><? } ?></label>
	<?
		}
		
		if ($bigtree["in_callout"]) {
			$clean_key = str_replace(array("[","]"),"_",$key);
			$bigtree["datetimepickers"][] = "field_$clean_key";
			$bigtree["datetimepicker_values"]["field_$clean_key"] = array("date" => date("m/d/Y",strtotime($value)), "time" => date("g:i a",strtotime($value)));	
	?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<div id="field_<?=$clean_key?>"></div>
	<?
		} else {
			$bigtree["datetimepickers"][] = "field_$key";
	?>
	<input type="text" tabindex="<?=$tabindex?>" name="<?=$key?>" value="<?=date("m/d/Y h:i a",strtotime($value))?>" autocomplete="off" id="field_<?=$key?>" class="date_picker<?=$validation?>" />
	<?
		}
	?>
</fieldset>