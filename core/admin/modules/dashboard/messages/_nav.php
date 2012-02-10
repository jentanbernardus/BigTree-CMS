<nav class="sub">
	<a href="<?=$aroot?>dashboard/messages/"<? if (end($path) == "messages") { ?> class="active"<? } ?>><span class="icon_small icon_small_list"></span>View Messages</a>
	<a href="<?=$aroot?>dashboard/messages/new/" <? if (end($path) == "new") { ?> class="active"<? } ?>><span class="icon_small icon_small_add"></span>New Message</a>
</nav>