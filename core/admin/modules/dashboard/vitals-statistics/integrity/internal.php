<?
	$q = sqlquery("SELECT * FROM bigtree_pages WHERE parent = '0'");
	while ($f = sqlfetch($q)) {
		$pages[] = $f["id"];
		_local_build_tree($f["id"]);
	}
	
	$external = false;
?>
<h1><span class="integrity"></span>Site Integrity Check</h1>
<div class="table">
	<summary>
		<div class="integrity_progress"><span id="progress">0%</span></div>
		<p>Running site integrity check with external link checking disabled.</p>
	</summary>
	<header>
		<span class="integrity_errors">Errors</span>
	</header>
	<ul id="updates"></ul>
</div>

