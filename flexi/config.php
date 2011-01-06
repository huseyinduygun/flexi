<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	/**
	 * Config
	 * 
	 * Site wide settings are set in this script. It is devided into different sections
	 * for each area.
	 */
     
	/* --- --- ---
	 *  Environment Settings
	 * --- --- --- */
	
	// Uncomment to have the DB save it's generated SQL in '/sql.txt'
//	Flexi::set( 'database_save_sql', 'sql.txt' );

    // when missing user activation is on by default
//    Flexi::set( 'disable_user_activation_links', true );
	
	/* --- --- ---
	 *  Setup
	 * --- --- --- */
	
	// The default controller and the default method to use when none is selected or found.
	Flexi::setDefaultController( 'home', 'index' );
	
	// The URI location of the root folder for your site. Only alter this
	// if it's located within a sub-folder.
	// In that case it should be '/sub-folder/'
	Flexi::setRootURI( "/" );
	
	/* --- --- ---
	 *  Paths - to find stuff
	 * --- --- --- */
	
	// Paths to search for files to import.
	// By default these are the flexi and app folders (library and your application).
	// These will be checked from top to bottom, in that order.
	Flexi::addPaths(
			'app',
			'flexi'
	);
	
	// The sub-directories where the controllers are found in one (or more) of any
	// of the above folders.
	Flexi::addControllerPaths(
			'controller'
	);
	
	/* --- --- ---
	 *  Databases - for the models
	 * --- --- --- */
	
	Flexi::addDatabase( 'my_database', array(
			'username' => 'my_username',
			'password' => 'my_password',
			'database' => 'my_db',
			'hostname' => 'localhost'
	) );
	
	/* --- --- ---
	 *  Frames - auto-views
	 * --- --- --- */
	Flexi::setFrame( null, null, array(
	        'start_page'    => 'view/frame/start_page',
	 		'content'       =>  null,
			'end_page'      => 'view/frame/end_page'
	) );
    
	// viewing automatically replaces this section
	Flexi::setDefaultFrameView( 'content' );
	
	/* --- --- ---
	 *  Pre-Load files
	 * 
	 * WARNING! _Must_ only be called _after_ setting the paths above!
	 * 
	 * They will be loaded right now, before any controllers are created.
	 * --- --- --- */
	
	// core Flexi files (don't remove these!)
	Flexi::load(
			'core/flexiobject',
			'core/loader',
			'core/controller',
			'core/model',
			'core/frame'
	);
	
	// extra Flexi files (optional)
	Flexi::load(
			'obj/session'
	);
	
	// files stored in app
	Flexi::load(
	);
?>
