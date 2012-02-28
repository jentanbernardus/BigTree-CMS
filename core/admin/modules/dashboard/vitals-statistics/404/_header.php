<?
	if (!$admin->Level) {
		die();
	}
	
	$breadcrumb = array(
		array("link" => "dashboard/", "title" => "Pages"),
		array("link" => "dashboard/404/", "title" => "404 Report")
	);
?>