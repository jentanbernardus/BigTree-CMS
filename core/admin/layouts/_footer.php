			</div>
		</div><!--End Wrapper-->
		<footer class="main">
			<section>
				<article class="bigtree">
					<a href="http://www.bigtreecms.com/" target="_blank" class="logo"></a>				
<!--
					<div class="links">
						<a href="http://support.bigtreecms.com/4/" target="_blank">Support</a>
						<span></span>
						<a href="http://developer.bigtreecms.com/" target="_blank">Developer</a>
					</div>
-->
				</article>
				<article class="fastspot">
					<p>
						Version <?=$GLOBALS["bigtree"]["version"]?>&nbsp;&nbsp;&middot;&nbsp;&nbsp;&copy; <?=date("Y")?> Fastspot
					</p>
					<a href="<?=$aroot?>credits/">Credits</a>&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href="http://www.bigtreecms.com/" target="_blank">Support</a>&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href="http://www.fastspot.com/agency/contact/" target="_blank">Contact Us</a>
				</article>
			</section>
		</footer>
		<?
			if (isset($_SESSION["bigtree"]["flash"])) {
		?>
		<script type="text/javascript">BigTree.growl("<?=htmlspecialchars($_SESSION["bigtree"]["flash"]["title"])?>","<?=htmlspecialchars($_SESSION["bigtree"]["flash"]["message"])?>",5000,"<?=htmlspecialchars($_SESSION["bigtree"]["flash"]["type"])?>");</script>
		<?
				unset($_SESSION["bigtree"]["flash"]);
			}
		?>
 	</body>
</html>