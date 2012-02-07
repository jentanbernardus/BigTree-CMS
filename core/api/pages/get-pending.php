<?
	/*
	|Name: Get Page with Pending Changes|
	|Description: Retrieves a page's information and parsed resources as well as unpublished changes.|
	|Readonly: YES|
	|Level: 0|
	|Parameters: 
		id: Page's Database ID|
	|Returns:
		page: Page Object|
	*/

	$page = $cms->getPendingPageById($_POST["id"]);
	if ($page) {
		$template = $cms->getTemplateById($page["template"]);
		if ($template["level"] > $admin->Level) {
			$page["template_locked"] = true;
		} else {
			$page["template_locked"] = false;
		}
		$page["resources"] = $cms->decodeResources($page["resources"]);
		$page["callouts"] = $cms->decodeCallouts($page["callouts"]);
		echo bigtree_api_encode(array("success" => true,"page" => $page));
	} else {
		echo bigtree_api_encode(array("success" => false,"error" => "Page not found."));
	}
?>