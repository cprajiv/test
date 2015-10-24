<?php

	/**********************************************************************
	*  Author: Justin Vincent (jv@jvmultimedia.com)
	*  Web...: http://twitter.com/justinvincent
	*  Name..: ezSQL_mysql
	*  Desc..: mySQL component (part of ezSQL databse abstraction library)
	*
	*/

	require_once 'ez_sql_core.php';
	
	/**********************************************************************
	*  ezSQL error strings - mySQL
	*/
    global $ezsql_mysql_str;

	$ezsql_mysql_str = array
	(
		1 => 'Require $dbuser and $dbpassword to connect to a database server',
		2 => 'Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?',
		3 => 'Require $dbname to select a database',
		4 => 'mySQL database connection is not active',
		5 => 'Unexpected error while trying to select database'
	);

	/**********************************************************************
	*  ezSQL Database specific class - mySQL
	*/

	if ( ! function_exists ('mysql_connect') ) die('<b>Fatal Error:</b> ezSQL_mysql requires mySQL Lib to be compiled and or linked in to the PHP engine');
	if ( ! class_exists ('ezSQLcore') ) die('<b>Fatal Error:</b> ezSQL_mysql requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');

	class ezSQL_mysql extends ezSQLcore
	{

		var $dbuser = false;
		var $dbpassword = false;
		var $dbname = false;
		var $dbhost = false;
		var $encoding = false;
		var $rows_affected = false;
		
		//Sky
		var $insert_id = 0;
		var $field_types = array();
		private static $instance = NULL;

		/**********************************************************************
		*  Constructor - allow the user to perform a qucik connect at the
		*  same time as initialising the ezSQL_mysql class
		*/

		function ezSQL_mysql($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost', $encoding='')
		{
			$this->dbuser = $dbuser;
			$this->dbpassword = $dbpassword;
			$this->dbname = $dbname;
			$this->dbhost = $dbhost;
			$this->encoding = $encoding;
		}

		/**********************************************************************
		*  Short hand way to connect to mySQL database server
		*  and select a mySQL database at the same time
		*/

		function quick_connect($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost', $encoding='')
		{
			$return_val = false;
			if ( ! $this->connect($dbuser, $dbpassword, $dbhost,true) ) ;
			else if ( ! $this->select($dbname,$encoding) ) ;
			else $return_val = true;
			return $return_val;
		}

		/**********************************************************************
		*  Try to connect to mySQL database server
		*/

		function connect($dbuser='', $dbpassword='', $dbhost='localhost')
		{
			global $ezsql_mysql_str; $return_val = false;
			
			// Keep track of how long the DB takes to connect
			$this->timer_start('db_connect_time');

			// Must have a user and a password
			if ( ! $dbuser )
			{
				$this->register_error($ezsql_mysql_str[1].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysql_str[1],E_USER_WARNING) : null;
			}
			// Try to establish the server database handle
			else if ( ! $this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword,true,131074) )
			{
				$this->register_error($ezsql_mysql_str[2].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysql_str[2],E_USER_WARNING) : null;
			}
			else
			{
				$this->dbuser = $dbuser;
				$this->dbpassword = $dbpassword;
				$this->dbhost = $dbhost;
				$return_val = true;
			}

			return $return_val;
		}

		/**********************************************************************
		*  Try to select a mySQL database
		*/

		function select($dbname='', $encoding='')
		{
			global $ezsql_mysql_str; $return_val = false;

			// Must have a database name
			if ( ! $dbname )
			{
				$this->register_error($ezsql_mysql_str[3].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysql_str[3],E_USER_WARNING) : null;
			}

			// Must have an active database connection
			else if ( ! $this->dbh )
			{
				$this->register_error($ezsql_mysql_str[4].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_mysql_str[4],E_USER_WARNING) : null;
			}

			// Try to connect to the database
			else if ( !@mysql_select_db($dbname,$this->dbh) )
			{
				// Try to get error supplied by mysql if not use our own
				if ( !$str = @mysql_error($this->dbh))
					  $str = $ezsql_mysql_str[5];

				$this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
			}
			else
			{
				$this->dbname = $dbname;
				if ( $encoding == '') $encoding = $this->encoding;
				if($encoding!='')
				{
					$encoding = strtolower(str_replace("-","",$encoding));
					$charsets = array();
					$result = mysql_query("SHOW CHARACTER SET");
					while($row = mysql_fetch_array($result,MYSQL_ASSOC))
					{
						$charsets[] = $row["Charset"];
					}
					if(in_array($encoding,$charsets)){
						mysql_query("SET NAMES '".$encoding."'");						
					}
				}
				
				$return_val = true;
			}

			return $return_val;
		}

		/**********************************************************************
		*  Format a mySQL string correctly for safe mySQL insert
		*  (no mater if magic quotes are on or not)
		*/

		function escape($str)
		{
			// If there is no existing database connection then try to connect
			if ( ! isset($this->dbh) || ! $this->dbh )
			{
				$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
				$this->select($this->dbname, $this->encoding);
			}

			return mysql_real_escape_string(stripslashes($str));
		}

		/**********************************************************************
		*  Return mySQL specific system date syntax
		*  i.e. Oracle: SYSDATE Mysql: NOW()
		*/

		function sysdate()
		{
			return 'NOW()';
		}

		/**********************************************************************
		*  Perform mySQL query and try to detirmin result value
		*/

		function query($query)
		{

			// This keeps the connection alive for very long running scripts
			if ( $this->num_queries >= 500 )
			{
				$this->num_queries = 0;
				$this->disconnect();
				$this->quick_connect($this->dbuser,$this->dbpassword,$this->dbname,$this->dbhost,$this->encoding);
			}

			// Initialise return
			$return_val = 0;

			// Flush cached values..
			$this->flush();

			// For reg expressions
			$query = trim($query);

			// Log how the function was called
			$this->func_call = "\$db->query(\"$query\")";

			// Keep track of the last query for debug..
			$this->last_query = $query;

			// Count how many queries there have been
			$this->num_queries++;
			
			// Start timer
			$this->timer_start($this->num_queries);

			// Use core file cache function
			if ( $cache = $this->get_cache($query) )
			{
				// Keep tack of how long all queries have taken
				$this->timer_update_global($this->num_queries);

				// Trace all queries
				if ( $this->use_trace_log )
				{
					$this->trace_log[] = $this->debug(false);
				}
				
				return $cache;
			}

			// If there is no existing database connection then try to connect
			if ( ! isset($this->dbh) || ! $this->dbh )
			{
				$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
				$this->select($this->dbname,$this->encoding);
				// No existing connection at this point means the server is unreachable
				if ( ! isset($this->dbh) || ! $this->dbh )
					return false;
			}

			// Perform the query via std mysql_query function..
			$this->result = @mysql_query($query,$this->dbh);

			// If there is an error then take note of it..
			if ( $str = @mysql_error($this->dbh) )
			{
				$this->register_error($str);
				$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
				return false;
			}

			// Query was an insert, delete, update, replace
			if ( preg_match("/^(insert|delete|update|replace|truncate|drop|create|alter|set)\s+/i",$query) )
			{
				$is_insert = true;
				$this->rows_affected = @mysql_affected_rows($this->dbh);

				// Take note of the insert_id
				if ( preg_match("/^(insert|replace)\s+/i",$query) )
				{
					$this->insert_id = @mysql_insert_id($this->dbh);
				}

				// Return number fo rows affected
				$return_val = $this->rows_affected;
			}
			// Query was a select
			else
			{
				$is_insert = false;

				// Take note of column info
				$i=0;
				while ($i < @mysql_num_fields($this->result))
				{
					$this->col_info[$i] = @mysql_fetch_field($this->result);
					$i++;
				}

				// Store Query Results
				$num_rows=0;
				while ( $row = @mysql_fetch_object($this->result) )
				{
					// Store relults as an objects within main array
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}

				@mysql_free_result($this->result);

				// Log number of rows the query returned
				$this->num_rows = $num_rows;

				// Return number of rows selected
				$return_val = $this->num_rows;
			}

			// disk caching of queries
			$this->store_cache($query,$is_insert);

			// If debug ALL queries
			$this->trace || $this->debug_all ? $this->debug() : null ;

			// Keep tack of how long all queries have taken
			$this->timer_update_global($this->num_queries);

			// Trace all queries
			if ( $this->use_trace_log )
			{
				$this->trace_log[] = $this->debug(false);
			}

			return $return_val;

		}
		
		/**********************************************************************
		*  Close the active mySQL connection
		*/

		function disconnect()
		{
			@mysql_close($this->dbh);	
		}
		
		// Sky
		function prepare( $query, $args ) {
			if ( is_null( $query ) )
				return;
	
			// This is not meant to be foolproof -- but it will catch obviously incorrect usage.
			if ( strpos( $query, '%' ) === false ) {
				sprintf( __( 'The query argument of %s must have a placeholder.' ), 'filesystem::prepare()');
			}
	
			$args = func_get_args();
			array_shift( $args );
			// If args were passed as an array (as in vsprintf), move them up
			if ( isset( $args[0] ) && is_array($args[0]) )
				$args = $args[0];
			$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
			$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
			$query = preg_replace( '|(?<!%)%f|' , '%F', $query ); // Force floats to be locale unaware
			$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
			array_walk( $args, array( $this, 'escape_by_ref' ) );
			return @vsprintf( $query, $args );
		}
	
		function escape_by_ref( &$string ) {
			if ( ! is_float( $string ) )
				//$string = $this->_real_escape( $string );
				$string = $this->escape( $string );
		}
		
		function _real_escape( $string ) {
			if($this->dbhost) {
			  return mysql_real_escape_string($string);
			}
			return addslashes( $string );
		}
		
		function insert( $table, $data, $format = null ) {
			return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
		}
		
		function replace( $table, $data, $format = null ) {
			return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
		}
		
		function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
			if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
				return false;
			$this->insert_id = 0;
			$formats = $format = (array) $format;
			$fields = array_keys( $data );
			$formatted_fields = array();
			foreach ( $fields as $field ) {
				if ( !empty( $format ) )
					$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
				elseif ( isset( $this->field_types[$field] ) )
					$form = $this->field_types[$field];
				else
					$form = '%s';
				$formatted_fields[] = $form;
			}
			$sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . implode( ",", $formatted_fields ) . ")";
			//print_r($this->prepare( $sql, $data ));exit;
			return $this->query( $this->prepare( $sql, $data ) );
		}
		
	   function update( $table, $data, $where, $format = null, $where_format = null ) {
			if ( ! is_array( $data ) || ! is_array( $where ) )
				return false;
	
			$formats = $format = (array) $format;
			$bits = $wheres = array();
			foreach ( (array) array_keys( $data ) as $field ) {
				if ( !empty( $format ) )
					$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
				elseif ( isset($this->field_types[$field]) )
					$form = $this->field_types[$field];
				else
					$form = '%s';
				$bits[] = "`$field` = {$form}";
			}
	
			$where_formats = $where_format = (array) $where_format;
			foreach ( (array) array_keys( $where ) as $field ) {
				if ( !empty( $where_format ) )
					$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
				elseif ( isset( $this->field_types[$field] ) )
					$form = $this->field_types[$field];
				else
					$form = '%s';
				$wheres[] = "`$field` = {$form}";
			}
	
			$sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
			
			//print_r($this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
			
			return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
		}
		
		
		function delete( $table, $where, $where_format = null ) {
			if ( ! is_array( $where ) )
				return false;
	
			$bits = $wheres = array();
	
			$where_formats = $where_format = (array) $where_format;
	
			foreach ( array_keys( $where ) as $field ) {
				if ( !empty( $where_format ) ) {
					$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
				} elseif ( isset( $this->field_types[ $field ] ) ) {
					$form = $this->field_types[ $field ];
				} else {
					$form = '%s';
				}
	
				$wheres[] = "$field = $form";
			}
	
			$sql = "DELETE FROM $table WHERE " . implode( ' AND ', $wheres );
			return $this->query( $this->prepare( $sql, $where ) );
		}
		
		
		static function getInstance() {

			if(!self::$instance){
			  self::$instance = new ezSQL_mysql(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
			  self::$instance->quick_connect(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
			  //self::$instance->show_errors();
			  //self::$instance->hide_errors();
			}
			
			return self::$instance;
	   }

	}


//$mysqldb = new ezSQL_mysql(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
//$mysqldb->quick_connect(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
//$mysqldb->show_errors();
//$mysqldb->hide_errors();

$mysqldb = ezSQL_mysql::getInstance();