<nav class="sub">
	<ul>
		<li><a href="<?=$sroot?>view/"<? if (end($path) == "view") { ?> class="active"<? } ?>><span class="icon_small icon_small_list"></span>View Settings</a></li>
		<li><a href="<?=$sroot?>add/"<? if (end($path) == "add") { ?> class="active"<? } ?>><span class="icon_small icon_small_add"></span>Add Setting</a></li>
	</ul>
</nav>