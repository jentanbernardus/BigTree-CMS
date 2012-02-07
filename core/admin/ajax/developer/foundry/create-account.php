<?
	header("Content-type: application/json");
	$result = bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/create-author/",$_POST);
	
	$i = json_decode($result,true);
	
	if ($i->error) {
		echo json_encode(array("success" => false));	
	} else {
		sqlquery("UPDATE bigtree_users SET foundry_author = '".mysql_real_escape_string($result)."'");
		echo json_encode(array("success" => true));
	}
?>