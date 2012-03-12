<?
	$details = json_decode(BigTree::cURL("http://developer.bigtreecms.com/ajax/foundry/get-module-details/",array("id" => $_GET["id"])),true);
?>
<div style="width: 400px; height: 300px; overflow: auto;">
	<h4><?=$details["name"]?> <small>Version <?=$details["primary_version"]?>.<?=$details["secondary_version"]?>.<?=$details["tertiary_version"]?></small></h4>
	<p><strong>Created by: <?=$details["author"]["name"]?><? if ($details["author"]["company"]) { ?> &mdash; <?=$details["author"]["company"]?><? } ?></strong></p>
	<p><?=$details["description"]?></p>
	<? if ($details["release_notes"]) { ?>
	<h4>Release Notes</h4>
	<?=$details["release_notes"]?>
	<? } ?>
</div>