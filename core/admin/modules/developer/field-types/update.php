<?
	BigTree::globalizePOSTVars(array("mysql_real_escape_string"));
	
	sqlquery("UPDATE bigtree_field_types SET name = '$name', pages = '$pages', modules = '$modules', callouts = '$callouts', primary_version = '$primary_version', secondary_version = '$secondary_version', tertiary_version = '$tertiary_version', description = '$description', release_notes = '$release_notes', last_updated = NOW() WHERE id = '$id'");
	
	$admin->growl("Developer","Updated Field Type");
	header("Location: ../view/");
	die();
?>