<h1>Unsupported Browser Warning</h1>
<p>
	You are currently using an unsupported and/or outdated browser.  Though some (or perhaps all) of BigTree may function in this browser, it is not supported.
</p>

<p><strong>We recommend using one of the following supported browsers:</strong></p>

<ul class="browser_list">
	<li>
		<a href="http://www.google.com/chrome/" target="_blank">
			<img src="<?=$aroot?>images/logo-chrome.jpg" alt="Google Chrome" />
			<strong>Google</strong>
			<h3>Chrome</h3>
		</a>
	</li>
	<li>
		<a href="http://www.getfirefox.com/" target="_blank">
			<img src="<?=$aroot?>images/logo-firefox.jpg" alt="Mozilla Firefox" />
			<strong>Mozilla</strong>
			<h3>Firefox</h3>
		</a>
	</li>
	<li>
		<a href="http://www.apple.com/safari/" target="_blank">
			<img src="<?=$aroot?>images/logo-safari.jpg" alt="Apple Safari" />
			<strong>Apple</strong>
			<h3>Safari</h3>
		</a>
	</li>
</ul>

<? $_SESSION["ignore_browser_warning"] = true; ?>
<a href="<?=$aroot?>" class="button">Ignore Warning</a>