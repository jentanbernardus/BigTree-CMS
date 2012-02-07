<?
	$module = $admin->getModuleById(end($commands));
?>
<h1><span class="icon_developer_modules"></span>Module Designer</h1>
<? include bigtree_path("admin/modules/developer/modules/_nav.php"); ?>
<div class="form_container">
	<header>
		<p>Complete!</p>
	</header>
	<section>
		<p>Your module is created.  You may access it <a href="<?=$aroot.$module["route"]?>/">by clicking here</a>.</p>
	</section>
</div>