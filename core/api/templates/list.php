<?
	/*
	|Name: Get Template List|
	|Description: Retrieves a list of available templates.|
	|Readonly: YES|
	|Level: 0|
	|Parameters: |
	|Returns:
		page: Page Templates,
		module: Module Templates|
	*/

	echo bigtree_api_encode(array("success" => true,"page" => $admin->getPageTemplates(), "module" => $admin->getModuleTemplates()));
?>