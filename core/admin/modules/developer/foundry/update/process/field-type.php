<?
	$details = json_decode($_POST["details"],true);
	
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
	
	$files = mysql_real_escape_string(json_encode($files));
	
	sqlquery("UPDATE bigtree_field_types SET id = '$field_type_id', author = '".mysql_real_escape_string($details["author"]["name"])."', name = '$name', primary_version = '$primary_version', secondary_version = '$secondary_version', tertiary_version = '$tertiary_version', description = '$description', release_notes = '$release_notes', files = '$files', pages = '$pages', modules = '$modules', callouts = '$callouts', downloaded = 'on', last_updated = NOW() WHERE foundry_id = '$id'");
	
	$admin->growl("Developer","Updated Field Type");
	header("Location: ".$admin_root."developer/foundry/field-types/");
	die();
?>