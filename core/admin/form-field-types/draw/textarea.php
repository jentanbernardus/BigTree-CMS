<fieldset>
	<? if ($title) { ?><label<?=$label_validation_class?>><?=$title?><? if ($subtitle) { ?> <small><?=$subtitle?></small><? } ?></label><? } ?>
	<textarea<?=$input_validation_class?> name="<?=$key?>" tabindex="<?=$tabindex?>" id="field_<?=$key?>"<? if ($options["rows"]) { ?> rows="<?=$options["rows"]?>"<? } ?><? if ($options["columns"]) { ?> cols="<?=$options["columns"]?>"<? } ?>><?=$value?></textarea>
</fieldset>