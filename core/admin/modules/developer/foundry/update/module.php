<?
	$details = json_decode(bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/get-module-details/",array("id" => end($path))),true);	
?>
<h3 class="foundry">Updating Module</h3>
<h4>Unpacking &ldquo;<?=$details["name"]?>&rdquo;</h4>
<p>Version <?=$details["primary_version"]?>.<?=$details["secondary_version"]?>.<?=$details["tertiary_version"]?> by <?=$details["author"]["name"]?></p>
<p>
	<strong>Release Notes</strong><br />
	<?=$details["release_notes"]?>
</p>
<?
	if (!is_writable($server_root."cache/")) {
		echo "<p>Your cache/ directory must be writable.</p>";
	} else {
		$existing_package = $admin->getModulePackageByFoundryId(end($path));
		$existing_id = $existing_package["id"];
		$existing_tables = json_decode($existing_package["tables"],true);
		$existing_files = json_decode($existing_package["files"],true);
		$cr = $server_root."cache/unpack/";
		if (!file_exists($cr))
			mkdir($cr);
		file_put_contents($cr."module.tar.gz",file_get_contents("http://developer.bigtreecms.com/files/foundry/modules/".$details["file"]));
		exec("cd $cr; tar zxvf module.tar.gz");
		$index = file_get_contents($cr."index.bpz");
		$lines = explode("\n",$index);
		$module_name = $lines[0];
		$package_info = $lines[1];
		
		echo "<p><strong>$module_name</strong><br />$package_info</p>";
		
		$errors = array();
		$warnings = array();
		next($lines);
		next($lines);
		foreach ($lines as $line) {
			$parts = explode("::||::",$line);
			$type = $parts[0];
			$data = json_decode($parts[1],true);
			if ($type == "Group") {
				$r = sqlrows(sqlquery("SELECT * FROM bigtree_module_groups WHERE package != '$existing_id' AND LOWER(name) = '".mysql_real_escape_string(strtolower($data["name"]))."'"));
				if ($r)
					$warnings[] = "A module group already exists with the name &ldquo;".$data["name"]."&rdquo; &mdash; some modules may be placed in this group.";				
			}
			if ($type == "Module") {
				$r = sqlrows(sqlquery("SELECT * FROM bigtree_modules WHERE package != '$existing_id' AND LOWER(name) = '".mysql_real_escape_string(strtolower($data["name"]))."'"));
				if ($r)
					$warnings[] = "A module already exists with the name &ldquo;".$data["name"]."&rdquo;";
			}
			if ($type == "Template") {
				$r = sqlrows(sqlquery("SELECT * FROM bigtree_templates WHERE package != '$existing_id' AND id = '".mysql_real_escape_string($data["id"])."'"));
				if ($r)
					$warnings[] = "A template already exists with the id &ldquo;".$data["id"]."&rdquo; &mdash; the template will be overwritten.";
			}
			if ($type == "Callout") {
				$r = sqlrows(sqlquery("SELECT * FROM bigtree_callouts WHERE package != '$existing_id' AND id = '".mysql_real_escape_string($data["id"])."'"));
				if ($r)
					$warnings[] = "A callout already exists with the id &ldquo;".$data["id"]."&rdquo; &mdash; the callout will be overwritten.";
			}
			if ($type == "Setting") {
				$r = sqlrows(sqlquery("SELECT * FROM bigtree_settings WHERE package != '$existing_id' AND id = '".mysql_real_escape_string($data["id"])."'"));
				if ($r)
					$warnings[] = "A setting already exists with the id &ldquo;".$data["id"]."&rdquo; &mdash; the setting will be overwritten.";
			}
			if ($type == "Feed") {
				$r = sqlrows(sqlquery("SELECT * FROM bigtree_feeds WHERE package != '$existing_id' AND route = '".mysql_real_escape_string($data["route"])."'"));
				if ($r)
					$warnings[] = "A feed already exists with the route &ldquo;".$data["route"]."&rdquo; &mdash; the feed will be overwritten.";
			}
			if ($type == "SQL") {
				$table = $parts[1];
				if (!in_array($table,$existing_tables)) {
					$r = sqlrows(sqlquery("SHOW TABLES LIKE '$table'"));
					if ($r)
						$warnings[] = "A table named &ldquo;$table&rdquo; already exists &mdash; the table will be overwritten.";
				}
			}
			if ($type == "File") {
				$location = $parts[2];
				if (!bigtree_is_writable($server_root.$location))
					$errors[] = "Cannot write to $location &mdash; please make the root directory writable.";
				if (file_exists($server_root.$location) && !in_array($location,$existing_files))
					$warnings[] = "A file already exists at $location &mdash; the file will be overwritten.";
			}
		}
?>

<? if (count($warnings)) { ?>
<strong class="import_warnings">Warnings</strong>
<ul class="import_warnings">
	<? foreach ($warnings as $w) { ?>
	<li>&raquo; <?=$w?></li>
	<? } ?>
</ul>
<? } ?>
<? if (count($errors)) { ?>
<strong class="import_errors">Errors</strong>
<ul class="import_errors">
	<? foreach ($errors as $e) { ?>
	<li>&raquo; <?=$e?></li>
	<? } ?>
</ul>
<p><strong>ERRORS OCCURRED!</strong> &mdash; Please correct all errors.  You may not import this module while errors persist.</p>
<? } else { ?>
<form method="post" action="<?=$developer_root?>foundry/update/process/module/<?=$details["id"]?>/" class="module">
	<input type="hidden" name="details" value="<?=htmlspecialchars(json_encode($details))?>" />
	<input type="submit" class="button white" value="Update Now" name="submit" />
</form>
<? } ?>

<?
	}
?>