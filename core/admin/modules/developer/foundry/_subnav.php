<?
	$js[] = "foundry.js";
	$mroot = $saroot."foundry/";
	$actions = array(
		"view" => "View Installed",
		"modules" => "Download Modules",
		"field-types" => "Download Field Types",
		"package/module" => "Package Module",
		"package/field-type" => "Package Field Type"
	);
?>
<ul class="related_nav">
	<? foreach ($actions as $k => $d) { ?>
	<li><a href="<?=$mroot.$k?>/"<? if (strpos($mroot.$k."/",$GLOBALS["domain"].$_SERVER["REQUEST_URI"]) !== false) { ?> class="active"<? } ?>><?=$d?></a></li>
	<? } ?>
</ul>