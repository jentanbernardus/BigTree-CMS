<?
	$upload_service = new BigTreeUploadService;
	
	$minWidth = $_POST["minWidth"];
	$minHeight = $_POST["minHeight"];

	$itype_exts = array(IMAGETYPE_PNG => ".png", IMAGETYPE_JPEG => ".jpg", IMAGETYPE_GIF => ".gif");
	
	if ($_POST["query"]) {
		$q = sqlquery("SELECT * FROM bigtree_resources WHERE name LIKE '%".mysql_real_escape_string($_POST["query"])."%' ORDER BY id DESC");
	} else {
		$q = sqlquery("SELECT * FROM bigtree_resources ORDER BY id DESC");
	}
	
	while ($f = sqlfetch($q)) {
		$file = str_replace("{wwwroot}",$site_root,$f["file"]);
		if ($f["is_image"]) {
			$f["type"] = "image";
		}
?>
<a href="<?=$f["file"]?>" class="file"><span class="file_type file_type_<?=$f["type"]?>"></span> <?=$f["name"]?></a>
<?
	}
?>