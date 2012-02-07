<h3 class="foundry">Foundry Submission</h3>
<?
	$type = $admin->getFieldType(end($commands));
	
	// Let's upload it to Foundry.
	$user = $admin->getUserById($admin->ID);
	
	$author = json_decode($user["foundry_author"],true);
	
	$data = array(
		"id" => $type["id"],
		"email" => $author["email"],
		"password" => $author["password"],
		"foundry_id" => $type["foundry_id"],
		"name" => $type["name"],
		"primary_version" => $type["primary_version"],
		"secondary_version" => $type["secondary_version"],
		"tertiary_version" => $type["tertiary_version"],
		"description" => $type["description"],
		"release_notes" => $type["release_notes"],
		"pages" => $type["pages"],
		"modules" => $type["modules"],
		"callouts" => $type["callouts"],
		"private" => $type["private"],
		"file" => "@".$server_root."cache/types/".$type["id"].".tar.gz"
	);
	
	$response = bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/upload-type/",$data);

	if (is_numeric($response)) {
		sqlquery("UPDATE bigtree_field_types SET foundry_id = '$response' WHERE id = '".$type["id"]."'");
		echo "<p>Successfully submitted Field Type to the Foundry.</p>";
	} else {
		echo "<p>$response</p>";
	}
?>