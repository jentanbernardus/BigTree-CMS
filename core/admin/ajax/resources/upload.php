<?
	header("HTTP/1.0 200 OK");
	
	$table = mysql_real_escape_string($_GET["table"]);
	$entry = mysql_real_escape_string($_GET["entry"]);
	
	if ($_FILES["Filedata"]["tmp_name"]) {
		$fname = get_safe_filename($site_root."files/resources/",$_FILES["Filedata"]["name"]);
		move_uploaded_file($_FILES["Filedata"]["tmp_name"],$site_root."files/resources/".$fname);
		chmod($site_root."files/resources/".$fname,0777);
		sqlquery("INSERT INTO bigtree_resources (`table`,`entry`,`file`) VALUES ('$table','$entry','$fname')");
		echo sqlid();
	} else {
		echo "Error";
	}
?>