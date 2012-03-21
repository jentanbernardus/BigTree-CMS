<h3 class="foundry">Updating BigTree</h3>
<div id="update_messages">
	<p>Downloading...</p>
</div>
<?
	$updates = unserialize(bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/get-update-list/?version=".$GLOBALS["bigtree"]["version"]));
?>
<script type="text/javascript">
	var downloads = <?=json_encode($updates)?>;
	var currentDownload = 0;
	
	function startUpdateDownload() {
		new Ajax.Request("<?=$aroot?>ajax/developer/foundry/download-bigtree-update/", { parameters: downloads[currentDownload], onComplete: function(r) {
			$("update_messages").insert({ bottom: r.responseText });
			currentDownload++;
			if (currentDownload < downloads.length) {
				startUpdateDownload();
			} else {
				$("update_messages").insert({ bottom: "<br /><h4>Update Complete!</h4>" });
			}
		}});
	}
	Event.observe(window,"load",function(ev) {
		startUpdateDownload();
	});
</script>