<h3 class="foundry">Foundry Submission</h3>
<?
	$package = $admin->getModulePackage(end($commands));
	
	// Let's upload it to Foundry.
	$user = $admin->getUser($admin->ID);
	
	$author = json_decode($user["foundry_author"],true);
	
	$data = array(
		"id" => $package["id"],
		"email" => $author["email"],
		"password" => $author["password"],
		"foundry_id" => $package["foundry_id"],
		"name" => $package["name"],
		"primary_version" => $package["primary_version"],
		"secondary_version" => $package["secondary_version"],
		"tertiary_version" => $package["tertiary_version"],
		"description" => $package["description"],
		"release_notes" => $package["release_notes"],
		"private" => $package["private"],
		"file" => "@".$server_root."cache/packages/".$package["id"].".tar.gz"
	);
	
	$response = bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/upload-module/",$data);

	if (is_numeric($response)) {
		sqlquery("UPDATE bigtree_module_packages SET foundry_id = '$response' WHERE id = '".$package["id"]."'");
		echo "<p>Successfully submitted Module to the Foundry.</p>";
	} else {
		echo "<p>$response</p>";
	}
?>