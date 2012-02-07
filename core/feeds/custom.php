<feed>
	<?
		$sort = $feed["options"]["sort"] ? $feed["options"]["sort"] : "id desc";
		$limit = $feed["options"]["limit"] ? $feed["options"]["limit"] : "15";
		$q = sqlquery("SELECT * FROM ".$feed["table"]." ORDER BY $sort LIMIT $limit");
		
		while ($f = sqlfetch($q)) {
			$f = BigTreeModule::get($f);
	?>
	<item>
		<?
			foreach ($feed["fields"] as $key => $options) {
				$value = $f[$key];
				if ($options["parser"])
					eval($options["parser"]);
		?>
		<<?=$key?>><![CDATA[<?=$value?>]]></<?=$key?>>
		<?
			}
		?>
	</item>
	<?
		}
	?>
</feed>