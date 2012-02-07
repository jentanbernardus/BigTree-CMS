<?
	$breadcrumb[] = array("title" => "Edit Group", "link" => "developer/modules/groups/edit/$id/");

	$id = end($path);
	$item = $admin->getModuleGroup($id);
?>
<h1><span class="icon_developer_modules"></span>Edit Group</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>

<div class="form_container">
	<form method="post" action="<?=$saroot?>modules/groups/update/<?=$id?>/" class="module">
		<header><h2>Group Details</h2></header>
		<section>
			<fieldset>
				<label class="required">Name</label>
				<input type="text" name="name" value="<?=htmlspecialchars($item["name"])?>" class="required" />
			</fieldset>
		</section>
		<footer>
			<input type="submit" class="button blue" value="Update" />
		</footer>
	</form>
</div>