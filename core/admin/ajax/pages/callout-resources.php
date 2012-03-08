<?
	if (isset($_POST["data"])) {
		$resources = json_decode(base64_decode($_POST["data"]),true);
		foreach ($resources as &$val) {
			if (is_array(json_decode($val,true))) {
				$val = bigtree_untranslate_array(json_decode($val,true));
			} else {
				$val = $cms->replaceInternalPageLinks($val);
			}
		}
		
		$type = $resources["type"];
	}
	
	if (isset($_POST["count"])) {
		$bigtree_sidelet_count = $_POST["count"];
	}
	
	$s = sqlfetch(sqlquery("SELECT * FROM bigtree_callouts WHERE id = '$type'"));
	$fields = json_decode($s["resources"],true);
	
	if ($s["description"]) {
?>
<p><?=htmlspecialchars($s["description"])?></p>
<?
	}
	
	$tabindex = 1000;
	
	if (count($fields) > 0) {
		foreach ($fields as $options) {
			$key = "callouts[$count][".$options["id"]."]";
			$type = $options["type"];
			$title = $options["name"];
			$subtitle = $options["subtitle"];
			$options["directory"] = "files/pages/";
			$value = $resources[$options["id"]];
			$currently_key = "callouts[$bigtree_sidelet_count][currently_".$options["id"]."]";
			include bigtree_path("admin/form-field-types/draw/$type.php");
			$tabindex++;
		}
	}
?>

<script type="text/javascript">
	BigTreeCustomControls();
	
	if (!tinyMCE) {
		tiny = new Element("script");
		tiny.src = "<?=$admin_root?>js/tiny_mce/tiny_mce.js";
		$("body").append(tiny);
	}
</script>
<?
	$mce_width = 400;
	$mce_height = 150;
	
	if (count($htmls)) {
		include bigtree_path("admin/layouts/_tinymce_specific.php");
	}
	if (count($simplehtmls)) {
		include bigtree_path("admin/layouts/_tinymce_specific_simple.php");
	}
?>