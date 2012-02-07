<?
	if (!is_writable($server_root."core/"))
		die("Please make the /core/ folder and everything in it writable by Apache.");
	$cache_dir = $server_root."cache/update/";
	exec("rm -rf $cache_dir; mkdir $cache_dir;");
	file_put_contents($server_root."cache/update/update.tar.gz",file_get_contents("http://developer.bigtreecms.com/files/updates/".$_POST["patch"]));
	exec("cd $cache_dir; tar -xf update.tar.gz;");
	
	if (file_exists($cache_dir."run_before.php"));
		include $cache_dir."run_before.php";
	if (file_exists($cache_dir."new_tables.sql"))
		exec("mysql --user=".$config["db"]["user"]." --password=".$config["db"]["password"]." ".$config["db"]["name"]." < ".$cache_dir."new_tables.sql");
	if (file_exists($cache_dir."changed_tables.sql"))
		exec("mysql --user=".$config["db"]["user"]." --password=".$config["db"]["password"]." ".$config["db"]["name"]." < ".$cache_dir."changed_tables.sql");
	$files_to_copy = explode("\n",file_get_contents($cache_dir."files.txt"));
	foreach ($files_to_copy as $file) {
		bigtree_copy($cache_dir."changes/$file",$server_root.$file);
	}
	if (file_exists($cache_dir."run_after.php"));
		include $cache_dir."run_after.php";
	exec("rm -rf $cache_dir;");
	
	echo "<p>Completed patch to <strong>".$_POST["name"]."</strong></p>";
?>