<?
	if (!$admin->Level) {
		die();
	}
	
	$breadcrumb = array(
		array("link" => "dashboard/", "title" => "Pages"),
		array("link" => "dashboard/404/", "title" => "404 Report")
	);
?>
<nav class="tertiary">
	<ul>
		<li><a href="<?=$aroot?>dashboard/404/"><span class="icon_small icon_small_page_error"></span>Active 404s</a></li>
		<li><a href="<?=$aroot?>dashboard/404/ignored/"><span class="icon_small icon_small_bug"></span>Ignored 404s</a></li>
		<li><a href="<?=$aroot?>dashboard/404/301/"><span class="icon_small icon_small_page_link"></span>301 Redirects</a></li>
	</ul>
</div>