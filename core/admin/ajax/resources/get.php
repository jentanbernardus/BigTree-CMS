<?
	$entry = isset($_POST["entry"]) ? $_POST["entry"] : $entry;
	$table = isset($_POST["table"]) ? $_POST["table"] : $table;
	$type = isset($_POST["type"]) ? $_POST["type"] : "all";
	
	$entry = mysql_real_escape_string($entry);
	$table = mysql_real_escape_string($table);

	function is_image($file) {
		$f = explode(".",$file);
		$e = strtolower($f[count($f)-1]);
		if ($e == "jpg" || $e == "gif" || $e == "png" || $e == "jpeg" || $e == "bmp")
			return true;
		return false;
	}
	
	$images = array();
	$files = array();
	$inames = array();
	$fnames = array();
	
	if (!$table) {
		$q = sqlquery("SELECT * FROM bigtree_resources");
	} else {
		if ($entry == "all") {
			$q = sqlquery("SELECT * FROM bigtree_resources WHERE `table` = '$table'");
		} else {
			$q = sqlquery("SELECT * FROM bigtree_resources WHERE `table` = '$table' AND entry = '$entry'");
		}
	}
	
	while ($f = sqlfetch($q)) {
		$file = $f["file"];
		
		if (is_image($file) && $type != "files") {
			$data = '<li class="image"><img src="'.$aroot.'images/icon_drag.gif" class="left" alt="" /><h4>Drag Image to Content</h4>';
			// This needs to be changed to take module access into account instead... and wtf is $id?
			$r = $admin->getPageAccessLevelByUser($id,$admin->ID);
			if ($r == "p")
				$data .= '<a class="right delete_resource" href="#'.$f["id"].'"><img src="'.$aroot.'images/icon_delete.gif" alt="" /></a>';
			$data .= '<img src="'.$config["resource_root"]."files/resources/".$file.'" alt="" /></li>';
			$images[] = $data;
			$inames[] = strtolower($name);
		}
		if (!is_image($file) && $type != "images") {
			$data = '<li class="link">';
			// This needs to be changed to take module access into account instead... and wtf is $id?
			$r = $admin->getPageAccessLevelByUser($id,$admin->ID);
			if ($r == "p")
				$data .= '<img class="delete_resource" href="#'.$f["id"].'" src="'.$aroot.'images/icon_delete.gif"  alt="" />';
			$data .= '<a href="'.$config["resource_root"]."files/resources/".$file.'">'.$file.'</a></li>';
			$files[] = $data;
			$fnames[] = strtolower($name);
		}
	}
	array_multisort($inames,$images);
	array_multisort($fnames,$files);
	echo implode("",$files);
	echo implode("",$images);
?>
<script type="text/javascript">
	$(".delete_resource").click(function() {
		id = $(this).attr("href").substr(1);
		if (confirm("Are you sure you want to delete this file?")) {
			$(this).parents("li").remove();
			$.ajax("<?=$aroot?>ajax/resources/delete/", { type: "POST", data: { id: id } });
		}
		
		return false;
	});
	
	$("#resources_content li.link a").click(function() { return false; });
</script>