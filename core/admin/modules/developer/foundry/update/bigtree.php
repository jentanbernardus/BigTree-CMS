<h3 class="foundry">Updating BigTree</h3>
<div id="update_messages">
	<p>Downloading...</p>
</div>
<?
	$updates = json_decode(bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/get-update-list/?version=".$GLOBALS["bigtree"]["version"]),true);
?>
<script type="text/javascript">
	var downloads = <?=json_encode($updates)?>;
	var currentDownload = 0;
	
	function startUpdateDownload() {
		$.ajax("<?=$admin_root?>ajax/developer/foundry/download-bigtree-update/", { type: "POST", data: downloads[currentDownload], complete: function(r) {
			$("#update_messages").append(r.responseText);
			currentDownload++;
			if (currentDownload < downloads.length) {
				startUpdateDownload();
			} else {
				$("#update_messages").append("<br /><h4>Update Complete!</h4>");
			}
		}});
	}
	$(window).load(function() {
		startUpdateDownload();
	});
</script>