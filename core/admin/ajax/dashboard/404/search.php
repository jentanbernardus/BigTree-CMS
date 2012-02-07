<?
	if (isset($_POST["search"])) {
		$s = mysql_real_escape_string($_POST["search"]);
		if ($_POST["type"] == "301") {
			$q = sqlquery("SELECT * FROM bigtree_404s WHERE ignored = '' AND (broken_url LIKE '%$s%' OR redirect_url LIKE '%$s%') AND redirect_url != '' ORDER BY requests DESC LIMIT 50");
		} elseif ($_POST["type"] == "ignored") {
			$q = sqlquery("SELECT * FROM bigtree_404s WHERE ignored != '' AND (broken_url LIKE '%$s%' OR redirect_url LIKE '%$s%') ORDER BY requests DESC LIMIT 50");
		} else {
			$q = sqlquery("SELECT * FROM bigtree_404s WHERE ignored = '' AND broken_url LIKE '%$s%' AND redirect_url = '' ORDER BY requests DESC LIMIT 50");
		}
	} else {
		if ($type == "301") {
			$q = sqlquery("SELECT * FROM bigtree_404s WHERE ignored = '' AND redirect_url != '' ORDER BY requests DESC LIMIT 50");
		} elseif ($type == "ignored") {
			$q = sqlquery("SELECT * FROM bigtree_404s WHERE ignored != '' ORDER BY requests DESC LIMIT 50");
		} else {
			$q = sqlquery("SELECT * FROM bigtree_404s WHERE ignored = '' AND redirect_url = '' ORDER BY requests DESC LIMIT 50");
		}
	}

	$tabindex = 0;
	while ($f = sqlfetch($q)) {
		$tabindex++;
?>
<li>
	<section class="requests_404"><?=$f["requests"]?></section>
	<section class="url_404"><?=$f["broken_url"]?></section>
	<section class="redirect_404">
		<input type="text" tabindex="<?=$tabindex?>" name="<?=$f["id"]?>" id="404_<?=$f["id"]?>" class="autosave" value="<?=htmlspecialchars($f["redirect_url"])?>" />
	</section>
	<section class="ignore_404"><a href="#<?=$f["id"]?>" class="icon_delete"></a></section>
</li>
<?
	}
?>