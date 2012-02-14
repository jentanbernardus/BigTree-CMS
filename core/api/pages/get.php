<?
	/*
	|Name: Get Page|
	|Description: Retrieves a page's information and parsed resources.|
	|Readonly: YES|
	|Level: 0|
	|Parameters: 
		id: Page's Database ID|
	|Returns:
		page: Page Object|
	*/

	$page = $cms->getPage($_POST["id"]);
	if ($page) {
		$template = $cms->getTemplate($page["template"]);
		if ($template["level"] > $admin->Level) {
			$page["template_locked"] = true;
		} else {
			$page["template_locked"] = false;
		}
		echo bigtree_api_encode(array("success" => true,"page" => $page));
	} else {
		echo bigtree_api_encode(array("success" => false,"error" => "Page not found."));
	}
?>