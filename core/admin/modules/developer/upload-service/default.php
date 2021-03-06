<?
	$service = new BigTreeUploadService;
	$breadcrumb[] = array("title" => "Upload Service", "link" => "#");
	
	if ($service->Service == "s3") {
		$currently = "Amazon S3";
		$class = "icon_developer_upload_amazon";
	} elseif ($service->Service == "rackspace") {
		$currently = "Rackspace Cloud Files";
		$class = "icon_developer_upload_rackspace";
	} else {
		$currently = "Local Storage";
		$class = "icon_developer_upload_service";
	}
?>
<h1><span class="<?=$class?>"></span>Upload Service</h1>

<div class="table">
	<summary><h2>Currently Using<small><?=$currently?></small></h2></summary>
	<section>
		<a class="box_select" href="local/">
			<span class="icon_developer_upload_local"></span>
			<p>Local Storage</p>
		</a>
		<a class="box_select" href="amazon/">
			<span class="icon_developer_upload_amazon"></span>
			<p>Amazon S3</p>
		</a>
		<? if (function_exists("mb_strlen")) { ?>
		<a class="box_select" href="rackspace/">
			<span class="icon_developer_upload_rackspace"></span>
			<p>Rackspace Cloud Files</p>
		</a>
		<? } ?>
	</section>
</div>