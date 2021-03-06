<?
	$total = $admin->get404Total("404");
	$breadcrumb[] = array("link" => "dashboard/vitals-statistics/404/", "title" => "Active 404s");
	$type = "404";
	$delete_action = "ignore";
?>
<h1>
	<span class="page_404"></span>Active 404s
	<? include BigTree::path("admin/modules/dashboard/vitals-statistics/_jump.php"); ?>
</h1>
<? include BigTree::path("admin/modules/dashboard/vitals-statistics/404/_nav.php") ?>
<div class="table">
	<summary class="taller">
		<input type="search" class="form_search" placeholder="Search" id="404_search" />
		<p><?=$total?> URL<? if ($total != 1) { ?>s<? } ?> reported that do not presently have 301 redirects &mdash; Redirect URLs save automatically as you type them.</p>
	</summary>
	<header>
		<span class="requests_404">Requests</span>
		<span class="url_404">404 URL</span>
		<span class="redirect_404">Redirect</span>
		<span class="ignore_404">Ignore</span>
	</header>
	<ul id="results">
		<? include BigTree::path("admin/ajax/dashboard/404/search.php") ?>
	</ul>
</div>