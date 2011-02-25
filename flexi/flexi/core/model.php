<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	/**
	 * The Model, this represents data.
	 * 
	 * It's intended for this to be extended and for the developer to add
	 * their functions to make this into a specific model, say for
	 * dealing with users data on a forum or video data on a video site.
	 * 
	 * On creation this will have a database stored under it's 'db' field.
	 * You can access it as '$this->db'.
	 */
	class Model extends LoaderObject
	{
		/**
		 * Creates a new Model.
		 * 
		 * If 'dbName' is null then it will use the default database (the first one in the config file).
		 * 
		 * If 'dbName' is null and there is no default database, then none is picked and the 'db' field
		 * will not be present in this object.
		 */
	    public function __construct( $dbName=null )
		{
			parent::__construct();
			
            $flexi = Flexi::getFlexi();
            
			// setup the db
			if ( $dbName == null ) {
				$dbConfig = $flexi->getDefaultDatabase();
			} else {
				$dbConfig = $flexi->getDatabase( $dbName );
			}
			
			if ( $dbConfig == null ) {
				if ( $dbName != null ) {
					throw new Exception( 'Database configuration not found, database: '.$dbName );
				}
			} else {
				$this->load->obj( 'obj/database', 'db', null, $dbConfig );
			}
		}
	}
?>