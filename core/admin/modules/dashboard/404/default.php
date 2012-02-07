<?
	$total = sqlfetch(sqlquery("SELECT COUNT(id) AS `total` FROM bigtree_404s WHERE ignored = '' AND redirect_url = ''"));
	$total = $total["total"];
	$breadcrumb[] = array("link" => "dashboard/404/", "title" => "Active 404s");
	$type = "404";
	$delete_action = "ignore";
?>
<h1><span class="page_404"></span>Active 404s</h1>
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
		<? include bigtree_path("admin/ajax/dashboard/404/search.php") ?>
	</ul>
</div>