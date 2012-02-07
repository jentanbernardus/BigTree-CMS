<?
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
	
	$query = mysql_real_escape_string($_POST["query"]);
	
	$q = sqlquery("SELECT * FROM bigtree_resources WHERE file LIKE '%$query%' OR title LIKE '%$query%' OR description LIKE '%$query%' LIMIT 5");
	while ($f = sqlfetch($q)) {
		$file = $f["file"];
		
		if (is_image($file)) {
			$data = '<li class="image"><img src="'.$aroot.'images/icon_drag.gif" class="left" alt="" /><h4>Drag Image to Content</h4>';
			$data .= '<div class="list_image_holder"><img src="'.$resource_root."files/resources/".$file.'" alt="" /></div></li>';
			$images[] = $data;
			$inames[] = strtolower($name);
		} else {
			$data = '<li class="link">';
			$data .= '<a href="'.$resource_root."files/resources/".$file.'">'.$file.'</a></li>';
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
	$("#resources_search_results li.link a").click(function() { return false; });
</script>