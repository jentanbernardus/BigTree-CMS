<?
	BigTree::globalizePOSTVars();

	$id = end($bigtree["path"]);

	if ($group_new) {
		$group = $admin->createModuleGroup($group_new,"on");
	} else {
		$group = $group_existing;
	}
	
	$admin->updateModule($id,$name,$group,$class,$gbp);	

	$admin->growl("Developer","Updated Module");
	BigTree::redirect($developer_root."modules/view/");
?>