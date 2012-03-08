<?
	$mpage = $admin_root.$module["route"]."/";
	bigtree_clean_globalize_array($view);

	$options = json_decode($options,true);
	$fields = json_decode($fields,true);
	$actions = json_decode($actions,true);
	
	$suffix = $suffix ? "-".$suffix : "";
?>
<div class="table" id="" class="image_list">
	<summary>
		<p>Click an image to edit it.</p>
	</summary>
	<section>
		<ul id="image_list_<?=$view["id"]?>" class="image_list">
			<?
				foreach ($items as $item) {
					$style = "";
					if (file_exists($site_root.$options["directory"].$options["prefix"].$item[$options["image"]])) {
						list($w,$h) = getimagesize($site_root.$options["directory"].$options["prefix"].$item[$options["image"]]);

						if ($w > $h) {
							$perc = 108 / $w;
							$h = $perc * $h;
							$style = "margin: ".floor((108 - $h) / 2)."px 0 0 0;";
						} else {
							$perc = 108 / $h;
							$w = $perc * $w;
							$style = "margin: 0 0 0 ".floor((108 - $w) / 2).";";
						}
					}
			?>
			<li id="row_<?=$item["id"]?>">
				<a class="image" href="<?=$mpage?>edit<?=$suffix?>/<?=$item["id"]?>/"><img src="<?=$www_root.$options["directory"].$options["prefix"].$item[$options["image"]]?>" alt="" style="<?=$style?>" /></a>
				<?
					foreach ($actions as $action => $data) {
						if ($action != "edit") {
							$class = $admin->getActionClass($action,$item);
							$link = "#".$item["id"];
							
							if ($data != "on") {
								$data = json_decode($data,true);
								$class = $data["class"];
								$link = $mpage.$data["route"]."/".$item["id"]."/";
								if ($data["function"]) {
									eval('$link = '.$data["function"].'($item);');
								}
							}
				?>
				<a href="<?=$link?>" class="<?=$class?>"></a>
				<?
						}
					}
				?>
			</li>
			<?
				}
			?>
		</ul>
	</section>
</div>
<script type="text/javascript">
	var deleteConfirm,deleteTimer,deleteId;
	
	$("#image_list_<?=$view["id"]?> .icon_edit").click(function() {
		document.location.href = "<?=$mpage."edit".$suffix?>/" + $(this).attr("href").substr(1) + "/";
		return false;
	});
	
	$("#image_list_<?=$view["id"]?> .icon_delete").click(function() {
		new BigTreeDialog("Delete Item",'<p class="confirm">Are you sure you want to delete this item?',$.proxy(function() {
			$.ajax("<?=$admin_root?>ajax/auto-modules/views/delete/?view=<?=$view["id"]?>&id=" + $(this).attr("href").substr(1));
			$(this).parents("li").remove();
		},this),"delete",false,"OK");
		
		return false;
	});
	
	$("#image_list_<?=$view["id"]?> .icon_approve").click(function() {
		$.ajax("<?=$admin_root?>ajax/auto-modules/views/approve/?view=<?=$view["id"]?>&id=" + $(this).attr("href").substr(1));
		$(this).toggleClass("icon_approve_on");
		return false;
	});
	
	$("#image_list_<?=$view["id"]?> .icon_feature").click(function() {
		$.ajax("<?=$admin_root?>ajax/auto-modules/views/feature/?view=<?=$view["id"]?>&id=" + $(this).attr("href").substr(1));
		$(this).toggleClass("icon_feature_on");
		return false;
	});
	
	$("#image_list_<?=$view["id"]?> .icon_archive").click(function() {
		$.ajax("<?=$admin_root?>ajax/auto-modules/views/archive/?view=<?=$view["id"]?>&id=" + $(this).attr("href").substr(1));
		$(this).toggleClass("icon_archive_on");
		return false;
	});
</script>