<?
	$perm = $admin->getResourceFolderPermission($_POST["folder"]);
	if ($perm != "p") {
		die("You don't have permission to make a folder here.");
	}
	
	$folder = mysql_real_escape_string($_POST["folder"]);
	$name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
	
	sqlquery("INSERT INTO bigtree_resource_folders (`name`,`parent`) VALUES ('$name','$folder')");
?>
<html>
	<head>
		<link rel="stylesheet" href="<?=$admin_root?>css/main.css" />
	</head>
	<body style="background: transparent;">
		<p class="file_browser_response">Successfully Created Folder</p>
		<script type="text/javascript">
			parent.BigTreeFileManager.finishedUpload();
		</script>
	</body>
</html>