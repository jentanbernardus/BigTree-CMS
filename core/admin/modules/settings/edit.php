<?
	$breadcrumb[] = array("link" => "settings/edit/".end($path)."/", "title" => "Edit Setting");
	
	$item = $admin->getSetting(end($path));
	if ($item["encrypted"]) {
		$item["value"] = "";
	}
	
	if ($item["system"] || ($item["locked"] && $admin->Level < 2)) {
		die("<p>Unauthorized request.</p>");
	}
?>
<h1><span class="settings"></span>Edit Setting</h1>

<div class="form_container">
	<header>
		<h2><?=$item["name"]?></h2>
	</header>
	<? if ($item["encrypted"]) { ?>
	<aside>This setting is encrypted.  The current value cannot be shown.</aside>
	<? } ?>
	<form class="module" action="<?=$aroot?>settings/update/" method="post">	
		<input type="hidden" name="id" value="<?=htmlspecialchars(end($path))?>" />
		<section>
			<?
				echo $item["description"];
				
				$t = $item["type"];
				$title = "";
				$value = $item["value"];
				$key = $item["id"];
				include bigtree_path("admin/form-field-types/draw/".$t.".php");
			?>
		</section>
		<footer>
			<input type="submit" class="button blue" value="Update" />		
		</footer>
	</form>
</div>