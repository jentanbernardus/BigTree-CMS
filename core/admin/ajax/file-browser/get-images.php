<div class="file_browser_images">
	<?
		$upload_service = new BigTreeUploadService;
		
		$minWidth = $_POST["minWidth"];
		$minHeight = $_POST["minHeight"];
	
		$itype_exts = array(IMAGETYPE_PNG => ".png", IMAGETYPE_JPEG => ".jpg", IMAGETYPE_GIF => ".gif");
		
		if ($_POST["query"]) {
			$q = sqlquery("SELECT * FROM bigtree_resources WHERE name LIKE '%".mysql_real_escape_string($_POST["query"])."%' AND is_image = 'on' ORDER BY id DESC");
		} else {
			$q = sqlquery("SELECT * FROM bigtree_resources WHERE is_image = 'on' ORDER BY id DESC");
		}
		
		while ($f = sqlfetch($q)) {
			$file = str_replace("{wwwroot}",$site_root,$f["file"]);
			$thumbs = json_decode($f["thumbs"],true);
			$thumb = $thumbs["bigtree_internal_list"];
			$margin = $f["list_thumb_margin"];
			$thumb = str_replace("{wwwroot}",$www_root,$thumb);
			$disabled = (($minWidth && $minWidth !== "false" && $f["width"] < $minWidth) || ($minHeight && $minHeight !== "false" && $f["height"] < $minHeight)) ? " disabled" : "";
			
			// Find the available thumbnails for this image if we're dropping it in a WYSIWYG area.
			$available_thumbs = array();
			foreach ($thumbs as $tk => $tu) {
				if (substr($tk,0,17) != "bigtree_internal_") {
					$available_thumbs[] = array(
						"name" => $tk,
						"file" => $tu
					);
				}
			}
			
			$data = htmlspecialchars(json_encode(array(
				"file" => $f["file"],
				"thumbs" => $available_thumbs
			)));
	?>
	<a href="<?=$data?>" class="image<?=$disabled?>"><img src="<?=$thumb?>" alt="" style="margin-top: <?=$margin?>px;" /></a>
	<?
		}
	?>
</div>