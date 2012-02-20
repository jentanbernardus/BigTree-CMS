<?
	$breadcrumb[] = array("title" => "Created Field Type", "link" => "#");
	$type = $admin->getFieldType(end($commands));
?>
<h1><span class="icon_developer_field_types"></span>Field Type Created</h1>
<div class="form_container">
	<section>
		<p>Your new field type is setup and ready to use.</p>
		<ul class="styled clear">
			<li><?=$server_root?>custom/admin/form-field-types/draw/<?=$type["file"]?> &mdash; Your drawing file.</li>
			<li><?=$server_root?>custom/admin/form-field-types/process/<?=$type["file"]?> &mdash; Your processing file.</li>
		</ul>
		<p>For more information on what variables are available to you in these files, please see the <a href="http://developer.bigtreecms.com/advanced/field-types/" target="_blank">Field Types</a> documentation.</p>
	</section>
</div>

