<?
	// - MySQL Call Wrapper Functions -
	// Support for splitting reads/writes and handling error throwing automatically.

	$sqlerrors = array();
	$sqlqueries = array();
	
	function bigtree_setup_sql_connection($read_write = "read") {
		global $bigtree;
		
		if ($read_write == "read") {
			$connection = mysql_connect($bigtree["config"]["db"]["host"],$bigtree["config"]["db"]["user"],$bigtree["config"]["db"]["password"]);
			mysql_select_db($bigtree["config"]["db"]["name"],$connection);
			mysql_query("SET NAMES 'utf8'",$connection);
		} else {
			$connection = mysql_connect($bigtree["config"]["db_write"]["host"],$bigtree["config"]["db_write"]["user"],$bigtree["config"]["db_write"]["password"]);
			mysql_select_db($bigtree["config"]["db_write"]["name"],$connection);
			mysql_query("SET NAMES 'utf8'",$connection);
		}
		return $connection;
	}

	/*
		Function: sqlquery
			Equivalent to mysql_query in most cases.
			If BigTree has enabled splitting off to a separate write server this function will send all write related queries to the write server and all read queries to the read server.
			If BigTree has not enabled a separate write server the type parameter does not exist.
		
		Paramters:
			query - A query string.
			connection - An optional MySQL connection (normally this is chosen automatically)
			type - Chosen automatically if a connection isn't passed. "read" or "write" to specify which server to use.
			
		Returns:
			A MySQL query resource.
	*/
	
	if (isset($bigtree["config"]["db_write"]) && $bigtree["config"]["db_write"]["host"]) {
		function sqlquery($query,$connection = false,$type = "read") {
			global $sqlerrors,$bigtree;
			
			if (!$connection) {
				$commands = explode(" ",$query);
				$fc = strtolower($bigtree["commands"][0]);
				if ($fc == "create" || $fc == "drop" || $fc == "insert" || $fc == "update" || $fc == "set" || $fc == "grant" || $fc == "flush" || $fc == "delete" || $fc == "alter" || $fc == "load" || $fc == "optimize" || $fc == "repair" || $fc == "replace" || $fc == "lock" || $fc == "restore" || $fc == "rollback" || $fc == "revoke" || $fc == "truncate" || $fc == "unlock") {
					$connection = &$bigtree["mysql_write_connection"];
					$type = "write";
				} else {
					$connection = &$bigtree["mysql_read_connection"];
					$type = "read";
				}
			}
			
			if ($connection === "disconnected") {
				$connection = bigtree_setup_sql_connection($type);
			}	
			
			$q = mysql_query($query,$connection);
			$e = mysql_error();
			if ($e) {
				$sqlerror = "<b>".$e."</b> in query &mdash; ".$query;
				array_push($sqlerrors,$sqlerror);
				return false;
			}
			
			return $q;
		}
	} else {
		function sqlquery($query,$connection = false) {
			global $sqlerrors,$bigtree;
			
			if (!$connection) {
				$connection = &$bigtree["mysql_read_connection"];
			}
			
			if ($connection === "disconnected") {
				$connection = bigtree_setup_sql_connection();
			}
			
			$q = mysql_query($query,$connection);
			$e = mysql_error();
			if ($e) {
				$sqlerror = "<b>".$e."</b> in query &mdash; ".$query;
				array_push($sqlerrors,$sqlerror);
				return false;
			}
			
			return $q;
		}
	}
	
	/*
		Function: sqlfetch
			Equivalent to mysql_fetch_assoc.
			Throws an exception if it is called on an invalid query resource which includes the most recent MySQL errors.
		
		Parameters:
			query - The mysql query resource (returned via sqlquery or mysql_query or mysql_db_query)
			ignore_errors - If set to true an exception will not be thrown on a bad query resource.
		
		Returns:
			A row from the query in array format with key/value pairs.
	*/

	function sqlfetch($query,$ignore_errors = false) {
		// If the query is boolean, it's probably a "false" from a failed sql query.
		if (is_bool($query) && !$ignore_errors) {
			global $sqlerrors;
			throw new Exception("sqlfetch() called on invalid query resource. The most likely cause is an invalid sqlquery() call. Last error returned was: ".$sqlerrors[count($sqlerrors)-1]);
		} else {
			return mysql_fetch_assoc($query);
		}
	}
	
	/*
		Function: sqlrows
			Equivalent to mysql_num_rows.
	*/

	function sqlrows($result) {
		return mysql_num_rows($result);
	}
	
	/*
		Function: sqlid
			Equivalent to mysql_insert_id.
	*/

	function sqlid() {
		return mysql_insert_id();
	}
?>