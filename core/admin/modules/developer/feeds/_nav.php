<nav class="sub">
	<ul>
		<li><a href="<?=$sroot?>view/"<? if (end($path) == "view") { ?> class="active"<? } ?>><span class="icon_small icon_small_list"></span>View Feeds</a></li>
		<li><a href="<?=$sroot?>add/"<? if (end($path) == "add") { ?> class="active"<? } ?>><span class="icon_small icon_small_add"></span>Add Feed</a></li>
	</ul>
</nav>