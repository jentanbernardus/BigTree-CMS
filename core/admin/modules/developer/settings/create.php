<?
	$success = $admin->createSetting($_POST);
	if ($success) {
		$admin->growl("Developer","Created Setting");
		header("Location: ".$saroot."settings/view/");
		die();
	} else {
		$_SESSION["bigtree"]["developer"]["setting_data"] = $_POST;
		$_SESSION["bigtree"]["developer"]["error"] = "The ID you specified is already in use by another Setting.";
		header("Location: ".$saroot."settings/add/");
		die();
	}
?>