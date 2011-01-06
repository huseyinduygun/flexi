<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	/**
	 * Database
	 * 
	 * A database interface to make it simpler to perform queries. It's methods for describing
	 * SQL queries return this instance to allow the methods to be chained.
	 * 
	 * First all methods and properties return the same database instance. This is to allow you
	 * to chain query methods.
	 * 
	 * For example the SQL statement:
	 *     'SELECT name, artist FROM albums WHERE release_date > 2009 LIMIT 10'
	 * can be auto-generated as:
	 *     $db->albums->select('name', 'artist')->where('release_date > 2009')->limit(10)->query();
	 * 
	 * You can access tables directly as properties of the DB. These allow you to do several things.
	 * 
	 * ## Setting ##
	 * First you can insert values directly by just setting an array of properties to the table.
	 * 
	 * For example:
	 *     $db->albums = array( 'name' => 'OK Computer', 'artist' => 'Radiohead' );
	 * In this example the query is run directly without the user having to call query().
	 * 
	 * You can also set multiple sets of values in one to the table. You can do this if the
	 * array you are setting is 2D.
	 * For example:
	 *     $db->albums = array(
	 *             array( 'name' => 'OK Computer', 'artist' => 'Radiohead' ),
	 *             array( 'name' => 'You Forget it in People', 'artist' => 'Broken Social Scene' )
	 *     );
	 * In this example two rows of data are entered into the albume table.
	 * 
	 * ## Getting ##
	 * You can also retrieve properties to state which table you are using. The first property
	 * will state which table you are accessing and any more state what values you are selecting.
	 * 
	 * For example:
	 *     $db->albums->name->artist;
	 * This states that the user is selecting name and artist from the albums table.
	 * The query is not performed automatically when getting properties (only when setting).
	 * Note also that the above is the same as:
	 *     $db->albums;
	 *     $db->name;
	 *     $db->artist;
	 * 
	 * ## Examples ##
	 * 
	 * All of the following inserts do the same thing:
	 * 
	 *     $db->artists->insert( array('name' => 'Rancid'), array('name' => 'Caribou') )->query();
	 *     $db->insert( array('name' => 'Rancid'), array('name' => 'Caribou') )->artists->query();
	 *     $db->insert( 'artists', array('name' => 'Rancid'), array('name' => 'Caribou') )->query();
	 *     $db->insert( 'artists', array(array('name' => 'Rancid'), array('name' => 'Caribou')) )->query();
	 *     $db->artists->insert( array(array('name' => 'Rancid'), array('name' => 'Caribou')) )->query();
	 *     $db->insert( array(array('name' => 'Rancid'), array('name' => 'Caribou')) )->artists->query();
	 *     $db->artists = array( array('name' => 'Rancid'), array('name' => 'Caribou') );
	 * 
	 */
	class Database
	{
		private $username;
		private $password;
		private $database;
		private $host;
		
		private $activerecord;
		
        private $events;
        
		/**
		 * Tests if the given variable is set, and if it's not then an exception is thrown.
		 * 
		 * <p>The given var is taken and tested if it is set. If it is then it is returned.
		 * If it is not set then an exception is thrown that uses the errorTxt given as
		 * it's description.</p>
		 * 
		 * @param var The variable to test.
		 * @param errorTxt The text for the exception being thrown if the variable is not set.
		 * @return Returns the var parameter, but this only occurs if it's set.
		 */
		private static function issetError( $var, $errorTxt )
		{
			if ( isset($var) ) {
				return $var;
			} else {
				throw new Exception( $errorTxt );
			}
		}
		
		/**
		 * Tests if the given array is at least two-dimensional.
		 * 
		 * <p>If the given array contians another array as it's first element
		 * then it is considered to be two-dimensional and true will be returned.
		 * Otherwise false is returned.</p>
		 * 
		 * @param The array to check if it is multidimensional.
    	 * @return True if the array is at least 2D, otherwise false in all cases.
		 */
		private static function isArray2D( &$array )
		{
			return ( is_array($array) && count($array) > 0 && is_array($array[0]) );
		}
		
		/**
		 * Takes an array containing settings stored under 'username', 'password', 'database' and 'host' for
		 * the matching database settings for setting up a connection.
		 */
		public function __construct( $configs )
		{
			$this->username = Database::issetError( $configs['username'], 'Username missing from database config map' );
			$this->password = Database::issetError( $configs['password'], 'Password missing from database config map' );
			$this->database = Database::issetError( $configs['database'], 'Database to use is missing from database config map' );
			$this->host     = Database::issetError( $configs['hostname'], 'Hostname missing from database config map' );
			
			$this->activerecord = array();
            
            $this->events = null;
		}
		
		public function __set( $field, $value )
		{
			$this->insert( $field, $value );
			$this->query();
		}
		
		public function __get( $field )
		{
			if ( ! isset($this->activerecord['table']) ) {
				return $this->from( $field );
			} else {
				return $this->select( $field );
			}
		}
		
		/**
		 * Creates and returns a new database connection.
		 * 
		 * <p>Based on the configuration values given when this database object was made,
		 * this creates and then returns a database connection object for you to use in
		 * order to communicate with the database.</p>
		 * 
		 * @return A mySQL database connection reference.
		 */
		public function newConnection()
		{
			$conn = mysql_connect( $this->host, $this->username, $this->password );
			mysql_select_db( $this->database );
			return $conn;
		}
        
        /**
         * Adds an event to be called when the stated tables are touched.
         * This is run on every row of the results of the select.
         * 
         * The event can add or change the properties of the returned value,
         * this is mainly what it's intended for. If the event returns null
         * then the object is not added to the results, allowing filtering,
         * however it's highly advised to do this via the database where possible.
         * 
         * If the event fails to return a value then an exception will be thrown,
         * as it is presumed that the developer has forgotten to bear in mind that
         * this alters the result.
         * 
         * If no table is stated then the event is applied to all results of all
         * select queries. If multiple tables are stated then the results must be
         * from a select that touched all of those tables.
         * 
         * If the select touches more tables then the event needs, but does touch
         * all of the events tables, then the event is still run.
         * 
         * The event will need to take at least 1 parameter for the row being passed in.
         */
        public function addSelectEvent( $event )
        {
            if ( ! is_callable( $event ) ) {
                throw new Exception( "Given event is not callable (it's not a function)." );
            }
            
            $tables = array();
            $numTables = func_num_args();
            for ( $i = 1; $i < $numTables; $i++ ) {
                $tables[]= strtolower( func_get_arg( $i ) );
            }
            
            $tablesEvent = array( 'tables' => $tables, 'event' => $event );
            if ( $this->events == null ) {
                $this->events = array( $tablesEvent );
            } else {
                $this->events[]= $tablesEvent;
            }
        }
		
		/**
		 * Performs the SQL query given, or the one stored based on this object.
		 * 
		 * <p>If sql is passed in then this will perform the SQL value given.
		 * If there is no SQL value then this will generate an SQL statement
		 * based on the values stored within this database object. The result
		 * is returned.</p>
		 * 
		 * @return A DBResult object containing the results (if any) from the query performed.
		 */
		public function query( $sql=null )
		{
			$conn = $this->newConnection();
            $events = null;
			
			if ( $sql == null ) {
				$sqlResult = $this->generateSQL();
                $sql = $sqlResult->sql;
                
                if ( $sqlResult->isSelect && $sqlResult->tables && $this->events != null ) {
                    foreach ( $this->events as $tablesEvent ) {
                        $tables = $tablesEvent['tables'];
                        
                        $count = count( $tables );
                        if ( $count > 0 ) {
                            foreach ( $sqlResult->tables as $table ) {
                                if ( in_array( strtolower($table), $tables) ) {
                                    $count--;
                                }
                            }
                        }
                        
                        if ( $count <= 0 ) {
                            $event = $tablesEvent['event'];
                            
                            if ( $events == null ) {
                                $events = array( $event );
                            } else {
                                $events[]= $event;
                            }
                        }
                    }
                }
                
				$this->clear();
			}
			$results =& $this->queryInner( $sql, $conn, $events );
			
			return new DBResult( $results );
		}
		
		private function queryInner( $sql, $conn, $events )
		{
			$results = array();
			
			$mysqlResults = mysql_query( $sql, $conn );
			
			if ( $mysqlResults === false ) {
				throw new Exception( "mySQL error: " . mysql_error() );
			} else if ( $mysqlResults !== true ) {
                if ( $events != null ) {
                    while ( $resultObj = mysql_fetch_object($mysqlResults) ) {
                        foreach ( $events as $event ) {
                            $resultObj = $event( $resultObj );
                            
                            if ( !isset($resultObj) || $resultObj === undefined ) {
                                throw new Exception( "Event failed to return a value, must be null or a row." );
                            }
                        }
                        
                        if ( $resultObj != null ) {
                            $results[]= $resultObj;
                        }
                    }
                } else {
                    while ( $resultObj = mysql_fetch_object($mysqlResults) ) {
                        $results[]= $resultObj;
                    }
                }
			}
			mysql_close( $conn );
			
			return $results;
		}
		
		/**
		 * Performs a delete query based on the table and fields currently selected.
		 * 
		 * WARNING! calling delete when only a table is selectd will delete all of
		 * it's contents! i.e. $db->table->delete()
		 */
		public function delete()
		{
			$this->activerecord['delete'] = true;
			return $this->query();
		}
		
		/**
		 * Generates the SQL query from the settings placed onto this database object
		 * and returns it. No query is performed and the database is left unaltered.
		 * 
		 * This is mainly for debugging purposes and so you can get copies of the SQL
		 * that your queries will make.
		 * 
		 * @return The SQL code to perform the query currently setup on this database object.
		 */
		private function generateSQL()
		{
			$sql = '';
			$ar =& $this->activerecord;
			$isSelect = false;
			
			if ( ! isset($ar['table']) ) {
				throw new Exception("No table selected in query.");
			}
			
			// INSERT values
			if ( isset($ar['update_on']) ) {
				$sql = $this->generateSQLUpdate();
			} else if ( isset($ar['insert']) ) {
				if ( count($ar['table']) > 1 ) {
					throw new Exception("Multiple tables selected on insert (can only select one).");
				}
				$sql = $this->generateSQLInsert();
			} else if ( isset($ar['delete']) ) {
				$sql = $this->generateSQLDelete();
			} else {
				$sql = $this->generateSQLSelect();
                $isSelect = true;
			}
			
			$debugSaveSQL = Flexi::get( 'database_save_sql' );
			if ( $debugSaveSQL ) {
				$file = fopen( $debugSaveSQL, 'a' );
				fwrite( $file, $sql . "\n" );
				fclose( $file );
			}
            
			return new DBSQLQuery( $sql, $ar['table'], $isSelect );
		}
		
		private function generateSQLUpdate()
		{
			$ar =& $this->activerecord;
			$sql = 'UPDATE ';
			$sql .= $this->generateSQLFromList( $ar );
			
			$sql .= ' SET ';
			
			$isFirstIteration = true;
			
			// error checking
			if ( isset($ar['insert']) && count($ar['insert']) > 0 ) {
				$updateRow = $ar['insert'][0];
			} else {
				throw new Exception( "No rows given in update." );
			}
			
			foreach ( $updateRow as $field => $value ) {
				if ( $isFirstIteration ) {
					$isFirstIteration = false;
				} else {
					$sql .= ', ';
				}
				
				$sql .= $field . ' = "' . $this->dbSafe($value) . '"';
			}
			
			$sql .= $this->generateSQLWheres( $ar );
			
			if ( count($ar['table']) == 1 ) {
				$sql .= $this->generateSQLOrder( $ar );
				$sql .= $this->generateSQLLimitUpdate( $ar );
			}
			
			return $sql;
		}
		
		private function generateSQLInsert()
		{
			$sql = '';
			$ar =& $this->activerecord;
			$fields = array();
			$firstIterationOuter = true;
			
			foreach ( $ar['insert'] as $iArray ) {
				if ( $firstIterationOuter ) {
					$sql .= ' ("';
				} else {
					$sql .= ', ("';
				}
				
				$firstIterationInner = true;
				foreach ( $iArray as $field => $value ) {
					if ( ! $firstIterationOuter ) {
						if ( ! isset($fields[$field]) ) {
							throw new Exception( "Inconsistent field found '" . $field . "' (this field was found in one set of values but not in earlier insert values)." );
						}
					} else {
						if ( isset($fields[$field]) ) {
							throw new Exception( "Double field found in values, field: " . $field );
						} else {
							$fields[$field] = $field;
						}
					}
					
					if ( $firstIterationInner ) {
						$firstIterationInner = false;
					} else {
						$sql .= '", "';
					}
					
					$sql .= $this->dbSafe( $value );
				}
				
				$sql .= '")';
				$firstIterationOuter = false;
			}
			
			if ( isset($ar['override']) ) {
				$preSQL .= 'REPLACE';
			} else {
				$preSQL .= 'INSERT';
			}
			$preSQL .= ' INTO ' . $ar['table'][0]; // checked in outer generateSQL method to ensure this exists
			$preSQL .= ' (';
			
			$firstIteration = true;
			$preSQL .= implode( ', ', $fields );
			
			$sql = $preSQL . ') VALUES ' . $sql;
			return $sql;
		}
		
		private function generateSQLDelete()
		{
			$sql = 'DELETE ';
			$ar =& $this->activerecord;
			
			$sql .= $this->generateSQLFrom( $ar );
			$sql .= $this->generateSQLWheres( $ar );
			$sql .= $this->generateSQLOrder( $ar );
			$sql .= $this->generateSQLLimit( $ar );
			
			return $sql;
		}
		
		private function generateSQLSelect()
		{
			$sql = 'SELECT ';
			$ar =& $this->activerecord;
			
			$sql .= $this->generateSQLFields( $ar );
			$sql .= $this->generateSQLFrom( $ar );
			$sql .= $this->generateSQLWheres( $ar );
			$sql .= $this->generateSQLOrder( $ar );
			$sql .= $this->generateSQLLimit( $ar );
			
			return $sql;
		}
		
		private function generateSQLOrder( &$ar )
		{
			$sql = '';
			
			$sql = Database::issetConcat( $sql, ' ORDER BY ', '', $ar['order_field'] );
			$sql = Database::issetConcat( $sql, ' ', '', $ar['order_direction'] );
			
			return $sql;
		}
		
        private function generateSQLLimitUpdate( &$ar )
        {
            if ( isset($ar['limit_count']) ) {
                return 'LIMIT ' . $ar['limit_count'];
            } else {
                return '';
            }
        }
        
		private function generateSQLLimit( &$ar )
		{
			$sql = '';
			
			if ( isset($ar['limit_count']) && isset($ar['limit_offset']) ) {
				$sql .= ' LIMIT ' . $ar['limit_count'];
				
				if ( $ar['limit_offset'] !== null && !isset($ar['update']) && !isset($ar['delete']) ) {
					$sql .= ' OFFSET ' . $ar['limit_offset'];
				}
			}
			
			return $sql;
		}
		
		private function generateSQLFields( &$ar )
		{
			$sql = '';
			
			if ( isset($ar['field']) ) {
				$sql .= implode( ', ', $ar['field'] );
			} else {
				$sql .= ' * ';
			}
			
			return $sql;
		}
		
		private function generateSQLFrom( &$ar )
		{
			$sql = '';
			
			// FROM
			$sql .= ' FROM ';
			$sql .= $this->generateSQLFromList( $ar );
			
			return $sql;
		}
		
		private function generateSQLFromList( &$ar )
		{
			return implode( ',', $ar['table'] );
		}
		
		private function generateSQLWheres( &$ar )
		{
			$sql = '';
			
			// WHERE
			if (
					isset($ar['where']) ||
					isset($ar['where_or']) ||
					isset($ar['join_where']) ||
					isset($ar['equal']) ||
                    isset($ar['not_equal']) ||
					isset($ar['match'])
			) {
				$sql .= ' WHERE ';

				// First where is to state that there are no where clauses printed until this is false.
				$firstWhere = true;
				
				// This is for ensuring the join is seperate to the where's.
				// The non-join where's are bracketed to ensure they do not affect the join.
				$closeJoin = false;
			}
			
			if ( isset($ar['join_where']) ) {
				$firstIteration = true;
				$sql .= implode( ' AND ', $ar['join_where'] );
				
				if (
						isset($ar['where']) ||
						isset($ar['where_or']) ||
						isset($ar['equal']) ||
                        isset($ar['not_equal']) ||
						isset($ar['match'])
				) {
					$closeJoin  = true;
					$firstWhere = true; // stays true as were adding our own and below
					$sql .= ' AND ( ';
				}
			}
			
			if ( isset($ar['equal']) ) {
				if ( ! $firstWhere ) {
					$sql .= ' AND ';
				} else {
					$firstWhere = false;
				}
				
				$firstIteration = true;
				foreach ( $ar['equal'] as $field => $var ) {
					if ( $firstIteration ) {
						$firstIteration = false;
					} else {
						$sql .= ' AND ';
					}
					
					$varSql = '';
					if ( is_array($var) && count($var) > 0 ) {
						$sql .= '( ' . $field . ' = "';
						$sql .= implode( '" || ' . $field . ' = "', $var );
						$sql .= '" )';
					} else {
						$varSql = $field . ' = "' . $this->dbSafe($var) . '" ';
					}
					
					$sql .= $varSql;
				}
			}
            
			if ( isset($ar['not_equal']) ) {
				if ( ! $firstWhere ) {
					$sql .= ' AND ';
				} else {
					$firstWhere = false;
				}
				
				$firstIteration = true;
				foreach ( $ar['equal'] as $field => $var ) {
					if ( $firstIteration ) {
						$firstIteration = false;
					} else {
						$sql .= ' AND ';
					}
					
					$varSql = '';
					if ( is_array($var) && count($var) > 0 ) {
						$sql .= '( ' . $field . ' != "';
						$sql .= implode( '" || ' . $field . ' != "', $var );
						$sql .= '" )';
					} else {
						$varSql = $field . ' != "' . $this->dbSafe($var) . '" ';
					}
					
					$sql .= $varSql;
				}
			}
			
			if ( isset($ar['match']) ) {
				if ( ! $firstWhere ) {
					$sql .= ' AND ';
				} else {
					$firstWhere = false;
				}
				
				$firstIteration = true;
				foreach ( $ar['match'] as $clause ) {
					if ( $firstIteration ) {
						$firstIteration = false;
					} else {
						$sql .= ' AND ';
					}
					
					$fields = implode( ',', $clause['fields'] );
					$text = $this->dbSafe( $clause['text'] );
					
					$sql .= ' MATCH( ' . $fields . ' ) AGAINST("' . $text . '") ';
				}
			}
			
			if ( isset($ar['where']) ) {
				if ( ! $firstWhere ) {
					$sql .= ' AND ';
				} else {
					$firstWhere = false;
				}
				
				$sql .= implode( ' AND ', $ar['where'] );
			}
			
			if ( isset($ar['where_or']) ) {
				if ( ! $firstWhere ) {
					$sql .= ' OR ';
				} else {
					$firstWhere = false;
				}
				
				$sql .= implode( ' OR ', $ar['where_or'] );
			}
			
			if ( $closeJoin ) {
				$sql .= ' ) ';
			}
			
			return $sql;
		}
		
		private static function issetConcat( $sql, $prefix, $postfix, $variable )
		{
			if ( isset($variable) ) {
				return $sql . $prefix . $variable . $postfix;
			} else {
				return $sql;
			}
		}
		
		/**
		 * Clears all of the various settings set on this database object.
		 * 
		 * <p>Clears all of the various settings set on this database object.
		 * You can then create a new query using this object.
		 * Bear in mind this will be cleared automatically whenever you call
		 * the query method in order to perform a query built using this
		 * databases methods.</p>
		 */
		public function clear()
		{
			$this->activerecord = array();
			return $this;
		}
		
		/**
		 * This is a quick way to select some fields from a single table.
		 * 
		 * <p>A quick query builder. If fields is ommitted then all fields will be selected.</p>
		 * 
		 * <p>The condition can be used in one of two ways. If an array is given
		 * with the form of (array['field'] = 'value') then those mappings are
		 * used as the basis for the conditions in the SQL. The SQL will use
		 * the value's stored to match the fields that they are stored under.</p>
		 * 
		 * <p>The limitMax is for stating the maximum number of results to return,
		 * whilst limitOffset is for stating the offset to start the query from.</p>
		 * 
		 * <p>Finally ordered must be 'ASC' or 'DESC'.</p>
		 * 
		 * @param table The table you will be querying from.
		 * @param field An array containing the fields to retrieve.
		 * @param condition An array containing the matches to check for, or a custom SQL boolean condition which will be injected (i.e. 'name = "john").
		 * @param limitMax The maximum number of rows to return.
		 * @param limitOffset The row to start returning rows from, counts from 0.
		 * @param ordered ASC or DESC to state if the results should be ordered ascendingly or descendingly.
		 * @return A DBResult object which you can use to iterate over the results.
		 */
		public function query_select( $table, $fields=null, $condition=null, $limitMax=null, $limitOffset=null, $ordered=null )
		{
			$sql = 'SELECT ';
			if ( $fields == null ) {
				$sql .= '*';
			} else if ( is_array($fields) ) {
				$addComma = false;
				foreach ( $fields as $field ) {
					if ( $addComma ) {
						$sql .= ', ';
					} else {
						$addComma = true;
					}
					
					$sql .= $field;
				}
			} else if ( is_string($fields) ) {
				$sql .= $fields;
			}
			
			$sql .= ' FROM ' . $table;
			if ( $condition != null ) {
				$sql .= ' WHERE ';
				
				if ( is_array($condition) ) {
					$addAnd = false;
					foreach ( $condition as $field => $val ) {
						if ( $addAnd ) {
							$sql .= ' AND ';
						} else {
							$addAnd = true;
						}
						
						$sql .= ' ( ' . mysql_escape_string($field) . ' = "' . mysql_escape_string($val) . '" ) ';
					}
				} else {
					$sql .= $condition;
				}
			}
			
			// add the limit
			if ( $limitMax != null ) {
				if ( $limitOffset != null ) {
					$sql .= ' LIMIT '.$limitOffset.', '.$limitMax;
				} else {
					$sql .= ' LIMIT '.$limitMax;
				}
			} else {
				if ( $limitOffset != null ) {
					$sql .= ' LIMIT '.$limitOffset.', 18446744073709551615';
				}
			}
			
			return $this->query( $sql );
		}
		
		public function from()
		{
			if ( func_num_args() == 0 ) {
				throw new Exception( "No tables given." );
			}
			
			$args = func_get_args();
			$this->activerecord['table'] = $this->arrayMerge(
					$this->activerecord['table'],
					$args
			);
			
			return $this;
		}
		
		public function limit( $offset, $count=null )
		{
			if ( $count === null ) {
				$this->activerecord['limit_offset'] = 0;
				$this->activerecord['limit_count']  = $offset; // offset if actually count on single parameter
			} else {
				if ( $offset < 0 ) {
					throw new Exception( "Offset cannot be less then 0, was given: " . $offset );
				} else if ( $count < 0 ) {
					throw new Exception( "Count cannot be less then or equal to 0, was given: " . $count );
				}
				
				$this->activerecord['limit_offset'] = $offset;
				$this->activerecord['limit_count']  = $count;
			}
			
			return $this;
		}
		
		/**
		 * A multi-purpose insert method for inserting rows into a table.
		 * 
		 * <p>Can be used in one of two ways. First you can use this for stating a table and
		 * then pass in 1 or more arrays of values to be inserted into that table.
		 * For example: $db->insert( 'artists', array('name' => 'Radiohead'), array('name' => 'The Sea and Cake') );</p>
		 * 
		 * <p>Secondly if you have already stated the table your inserting too then you can just
		 * pass in multiple arrays.<br />
		 * For example $db->artists->insert( array('name' => 'Radiohead'), array('name' => 'The Sea and Cake') );</p>
		 * 
		 * <p>In both cases each array passed in is considered to be a seperate set of values
		 * to insert. The array should contain mappings of 'field' to 'value' where field
		 * refers to fields in the table.</p>
		 * 
		 * <p>If any of the arrays given are 2D then they will be presumed to be an array of rows to enter.
		 * This allows you to insert multiple rows like:
		 *     $db->artists->insert( array(
		 *             array('name' => 'Radiohead'),
		 *             array('name' => 'The Sea and Cake')
		 *     ) );</p>
		 */
		public function insert()
		{
			if ( func_num_args() == 0 ) {
				throw new Exception( "No insert values given." );
			}
			
			$table = func_get_arg( 0 );
			if ( ! is_array($table) ) {
				if ( func_num_args() == 1 ) {
					throw new Exception( "No insert values given." );
				}
				
				$this->from( $table );
				$startIndex = 1;
			} else {
				$startIndex = 0;
			}

			$paramArray = func_get_args();
			$numArgs = func_num_args();
			for ( $i = $startIndex; $i < $numArgs; $i++ ) {
				$this->insertArray( $paramArray[$i] );
			}
			
			return $this;
		}
		
		/**
		 * 
		 */
		public function update( $arr=null )
		{
			$this->activerecord['update_on'] = true;
			
			if ( $arr !== null ) {
				$this->insert( $arr );
			}
			
			return $this;
		}
		
		/**
		 * Inner helper function for inserting an associative array into
		 * the inserts bit of the database query building.
		 * This automatically expands any inner arrays it finds as elements
		 * to be re-entered.
		 * 
		 * 
		 */
		private function insertArray( &$array )
		{
			if ( Database::isArray2D($array) ) {
				foreach ( $array as $arr ) {
					$this->insertArray( $arr );
				}
			} else {
				$newValues = array();
				foreach ( $array as $key => $value ) {
					$newValues[ $key ] = $value;
				}
				
				if ( isset($this->activerecord['insert']) ) {
					$this->activerecord['insert'][] = $newValues;
				} else {
					$this->activerecord['insert'] = array( $newValues );
				}
			}
		}
		
		private function insertValue( $field, $value )
		{
			$lastInsert =& $this->activerecord['insert'];
			
			if ( isset($lastInsert) ) {
				$lastInsert[ $field ] = $value;
			} else {
				$this->insert( array($field => $value) );
			}
			
			return $this;
		}
		
		/**
		 * 
		 * @param field The field to order the results by.
		 * @param direction 'DESC' or 'ASC' for descending or ascending ordering.
		 */
		public function order( $field, $direction )
		{
			$this->activerecord['order_field']     = $field;
			$this->activerecord['order_direction'] = $direction;
			
			return $this;
		}
		
		public function select()
		{
			if ( func_num_args() == 0 ) {
				throw new Exception( "No select fields given." );
			}
			
			$args = func_get_args();
			$this->activerecord['field'] = $this->arrayMerge(
					$this->activerecord['field'],
					$args
			);
			
			return $this;
		}
        
        private function setEqual( $arField, $field, $value )
        {
            if ( isset($this->activerecord[$arField]) ) {
				$this->activerecord[$arField][$field] = $value;
			} else {
				$this->activerecord[$arField] = array( $field => $value );
			}
			
			return $this;
        }
		
        /**
         * Adds a not equal clause to match values which aren't the one given.
         */
        public function notEqual( $field, $value )
        {
            return $this->setEqual( 'not_equal', $field, $value );
        }
        
		/**
		 * Adds a where clause for 'field = value'. The value is presumed to be a literal
		 * and so will be wrapped in quotes. The field will be presumed to be a column name.
		 * 
		 * @param field The column for the match.
		 * @param The value to match the column against.
		 * @return This database object.
		 */
		public function equal($field, $value)
		{
            return $this->setEqual( 'equal', $field, $value );
		}
		
		/**
		 * This should only be used on fields set to use fulltext!
		 * 
		 * Variable length parameters but with a minimum of two.
		 * All parameters except the last one are seen as fields to
		 * match and search within. The last parameter is the search
		 * text to search for within those fields.
		 */
		public function match( $field, $text )
		{
			$numArgs = func_num_args();
			
			// last parameter is text
			$text    = func_get_arg( $numArgs-1 );
			
			// all parameters are fields except the last one
			$args = func_get_args();
			$fields  = array_slice( $args, 0, $numArgs-1 );
			
			$clause = array(
				'fields' => $fields,
				'text' => $text
			);
			
			if ( isset($this->activerecord['match']) ) {
				$this->activerecord['match'][] = $clause;
			} else {
				$this->activerecord['match'] = array( $clause );
			}
			
			return $this;
		}
		
		public function where()
		{
			if ( func_num_args() == 0 ) {
				throw new Exception( "No where conditions were given." );
			}
			
			$args = func_get_args();
			$this->activerecord['where'] = $this->arrayMerge(
					$this->activerecord['where'],
					$args
			);
			
			return $this;
		}
		
		/**
		 * This is used for inserting. When called this tells the database to override any rows that
	     * clash with the inserted data. This clash only occurs if there is a primary or unique key
	     * on tables (as duplicates are allowed if they do not exist).
		 */
		public function override()
		{
			$this->activerecord['override'] = true;
			return $this;
		}
		
		public function whereOr()
		{
			if ( func_num_args() == 0 ) {
				throw new Exception( "No where conditions were given." );
			}
			
			$args = func_get_args();
			$this->activerecord['where_or'] = $this->arrayMerge(
					$this->activerecord['where_or'],
					$args
			);
			
			return $this;
		}
		
		/**
		 * This method can work in one of two ways depending on if you give it 2 or 3 parameters.
		 * The first parameter always refers to the table you are joining with.
		 * 
		 * The first way to use this is to pass in two fields in the first and second parameter.
		 * These will then generate a where clause for them to be equal. For example
		 * join( 'artist', 'albums.id', 'artists.album_id' ) will generate the clause
		 * 'albums.id = artists.album_id'.
		 * 
		 * The second way is to give your own where clause in the second paramter, i.e.
		 * joine( 'artists', 'albums.id = artists.album_id' ).
		 * 
		 * @param table The table you are joining to for this query.
		 * @param selectField The first field to select values from, or your own clause for matching.
		 * @param tableField The second field you are joining against.
		 */
		public function join( $table, $selectField, $tableField=null )
		{
			$this->from( $table );
			
			if ( $tableField == null ) {
				$clause = $selectField;
			} else {
				$clause = $selectField . ' = ' . $tableField;
			}
			
			if ( isset($this->activerecord['join_where']) ) {
				$this->activerecord['join_where'][] = $clause;
			} else {
				$this->activerecord['join_where'] = array( $clause );
			}
			
			return $this;
		}
		
		/**
		 * Returns the number of elements in the currently selected table.
		 * i.e.
		 *     $db->table->count()
		 *     $db->count( 'table' )
		 * 
		 * Note this is will execute the query and clear the content of the
		 * database object.
		 * 
		 * This is returned as an actual value rather then as a result object.
		 */
		public function count( $table=null )
		{
			if ( $table != null ) {
				$this->from( $table );
			}
			
			return (int) ( $this->select( 'COUNT(*) AS `count`' )->query()->row()->count );
		}
		
		/**
		 * This is a query method, it will execute the query data so far.
		 * 
		 * It is for checking if a table has at least one row for the query described.
		 * 
		 * @return True if the query returns at least one row, otherwise false.
		 */
		public function exists()
		{
			$this->limit(1);
			return $this->count() > 0;
		}
		
		/**
		 * 
		 */
		private function dbSafe( $val )
		{
			return mysql_real_escape_string( $val );
		}
		
		/**
		 * Given two values this will merge the right one to the left one if they are both arrays.
		 * This is just like array_merge.
		 * 
		 * How this differs is that it expects one of left or right to possibly by a non-array
		 * (either null or undefined). 
		 */
		private function arrayMerge( &$left, &$right )
		{
			if ( is_array($left) ) {
				if ( is_array($right) ) {
					return array_merge( $left, $right );
				} else {
					return $left;
				}
			} else {
				if ( is_array($right) ) {
					return $right;
				} else {
					throw new Exception( 'Neither left or right are valid arrays.' );
				}
			}
		}
	}
    
    class DBSQLQuery
    {
        public $sql;
        public $tables;
        public $isSelect;
        
        public function __construct( $sql, $tables, $isSelect )
        {
            $this->sql = $sql;
            $this->tables = $tables;
            $this->isSelect = $isSelect;
        }
    }
    
	/**
	 * An object for handing viewing the results of a database query.
	 */
	class DBResult
	{
		private $innerIndex;
		private $results;
		
		public function __construct( $results )
		{
			$this->innerIndex = 0;
			$this->results = $results;
		}
		
		public function size()
		{
			return count( $this->results );
		}
		
		public function walk( $callback )
		{
			foreach ( $this->rows() as $user ) {
				$callback( $user );
			}
		}
		
		public function walkArray( $callback )
		{
			foreach ( $this->rowsArray() as $user ) {
				$callback( $user );
			}
		}
		
		public function rows()
		{
			return $this->results;
		}
		
		public function rowsArray()
		{
			$arrs = array();
			foreach ( $this->results as $result ) {
				$arrs[]= get_object_vars( $result );
			}
			return $arrs;
		}
		
		public function row( $index=null )
		{
			if ( $index == null ) {
				return $this->getRow( $this->innerIndex++ );
			} else {
				return $this->getRow( $index );
			}
		}
		
		public function rowArray( $index=null )
		{
			if ( $index == null ) {
				return get_object_vars( $this->getRow( $this->innerIndex++ ) );
			} else {
				return get_object_vars( $this->getRow( $index ) );
			}
		}
		
		private function getRow( $index )
		{
			if ( $index >= 0 && $index <= count($this->results) ) {
				return $this->results[ $index ];
			} else {
				return array();
			}
		}
	}
?>