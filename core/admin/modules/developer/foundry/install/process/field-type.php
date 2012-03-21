<?
	$details = unserialize($_POST["details"]);
	
	$cache = $server_root."cache/unpack/";
	$index = file_get_contents($cache."index.bpz");
	$lines = explode("\n",$index);
	$files = array();
	foreach ($lines as $line) {
		$pieces = explode("::||::",$line);
		$file = $pieces[2];
		file_put_contents($server_root.$file,file_get_contents($cache.$pieces[1]));
		chmod($server_root.$file,0777);
		$files[] = $file;
	}
	
	bigtree_clean_globalize_array($details,array("mysql_real_escape_string"));
	
	$files = mysql_real_escape_string(serialize($files));
	
	sqlquery("INSERT INTO bigtree_field_types (`id`,`foundry_id`,`author`,`name`,`primary_version`,`secondary_version`,`tertiary_version`,`description`,`release_notes`,`files`,`pages`,`modules`,`sidelets`,`downloaded`,`last_updated`) VALUES ('$field_type_id','$id','".mysql_real_escape_string($details["author"]["name"])."','$name','$primary_version','$secondary_version','$tertiary_version','$description','$release_notes','$files','$pages','$modules','$sidelets','on',NOW())");
	
	$admin->growl("Developer","Installed Field Type");
	header("Location: ".$aroot."developer/foundry/field-types/");
	die();
?>