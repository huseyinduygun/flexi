<?php
	// stop scripts from auto-exiting
	define( 'ACCESS_OK', true );
	
	Flexi::loadConfig( 'config.php' );
	
	// start the website!
	try {
		Flexi::start();
	// display error message
	} catch ( Exception $ex ) { ?>
		<h1 id="error_title">Error!</h1>
		<h2 id="error_message"><?php echo $ex->getMessage(); ?></h2>
		<div id="error_stack_Trace" style="white-space: pre"><?php echo $ex->getTraceAsString(); ?></div>
	<?php }
	
	/**
	 * Flexi
	 * 
	 * The framework runs on top of a fully static class used for holding sitewide details.
	 * This is mainly used in two phases. First for setting up the framework from the users
	 * config file and as a repository for getting information from.
	 * 
	 * For example the user might add their database info in their config file which will be
	 * retrieved and used later during a webpage.
	 */
	class Flexi
	{
		private static $frames = null;
		
		/**
		 * There should only be one frame handler, but it's also made on the fly
		 * to avoid creating it when it's not in use.
		 * 
		 * This function either returns the frame handler if it exists, or makes
		 * it if it doesn't (and returns the one it makes).
		 * 
		 * @return The FrameHandler for storing all frame configurations.
		 */
		private static function getFrameHandler()
		{
			if ( Flexi::$frames == null ) {
				Flexi::$frames = new FramesHandler();
			}
			
			return Flexi::$frames;
		}
		
		/**
		 * Sets a frame to use against a controller/function combination.
		 * 
		 * A frame is just an array with mappings from 'section' to 'views'.
		 * But a view can be null to say that a section is left empty.
		 * 
		 * If the controller is null then this will means 'all controllers',
		 * and leaving function as null means 'all functions'.
		 * 
		 * When a Controller is made at the start of a page request, it's
		 * name and the function selected will be used to find a Frame to
		 * apply to it.
		 * 
		 * @param controller The name of a controller to apply the frame to, or null for all controllers.
		 * @param function The name of a function to apply the frame to, or null for all functions.
		 * @param config A frame setup to be applied to the controller/function combination.
		 */
		public static function setFrame( $controller, $function, $config )
		{
			$frames = Flexi::getFrameHandler();
			$frames->setFrame( $controller, $function, $config );
		}
		
		/**
		 * If a frame is applied to a controller, and if that controller performs a normal view
		 * (that does not involve it's frame), then the 'default section' is the place in that
		 * frame where their view will appear.
		 * 
		 * You can use this function to state which section is the default section. This is
		 * applied to all frames.
		 * 
		 * @param section The name of the default section in all frames.
		 */
		public static function setDefaultFrameView( $section )
		{
			$frames = Flexi::getFrameHandler();
			$frames->setDefaultFrameView( $section );
		}
		
		private static $variables = array();
		
		/**
		 * Environment variables can be stored in Flexi for use globally.
		 * These are typically set in the config file and then retrieved from
		 * wherever that uses them.
		 * 
		 * @param key The key to store the value under.
		 * @param value The value to store under the key.
		 */
		public static function set( $key, $value )
		{
			Flexi::$variables[ $key ] = $value;
		}
		
		/**
		 * @param key The stored value to get.
		 * @return Null if the key is not set, otherwise the value stored under the key.
		 */
		public static function get( $key )
		{
			$val = Flexi::$variables[ $key ];
			
			if ( isset($val) ) {
				return $val;
			} else {
				return null;
			}
		}
		
		private static $databases = array();
		
		/**
		 * @return Null if the database config is not stored, otherwise an array containing it's configuration.
		 */
		public static function getDatabase( $name )
		{
			$dbConfig = Flexi::$databases[ $name ];
			
			if ( isset($dbConfig) ) {
				return $dbConfig;
			} else {
				return null;
			}
		}
		
		/**
		 * The default database is the first config stored.
		 * 
		 * @return Null if there are no database configs stored, otherwise the first database config found.
		 */
		public static function getDefaultDatabase()
		{
			// return first element
			foreach ( Flexi::$databases as $dbConfig ) {
				return $dbConfig;
			}
			
			return null;
		}
		
		/**
		 * Adds a database config to be stored for use by the Models.
		 * The config should be an associative array containing mappings for:
		 * 'username', 'password', 'database' and 'hostname'.
		 * 
		 * @param name The name to store the config under.
		 * @param config An associative array containing the settings needed to connect to the DB.
		 */
		public static function addDatabase( $name, $config )
		{
			Flexi::$databases[ $name ] = $config;
		}
		
		private static $searchPaths = array();
		private static $controllerPaths = array();
		
		/**
		 * All of the paths passed into this are added as folders to search
		 * for files within.
		 * 
		 * It has variable length arguments.
		 */
		public static function addPaths()
		{
			$numArgs = func_num_args();
			for ( $i = 0; $i < $numArgs; $i++ ) {
				Flexi::$searchPaths[] = Flexi::ensureEndingSlash( func_get_arg($i) );
			}
		}
		
		/**
		 * There are paths to say which folders should be searched from
		 * (which you set using 'addPaths'). But within those Flexi needs
		 * to know which folders contain the Controller classes.
		 * 
		 * This function is used to perform that task.
		 */
		public static function addControllerPaths()
		{
			$numArgs = func_num_args();
			for ( $i = 0; $i < $numArgs; $i++ ) {
				Flexi::$controllerPaths[] = Flexi::ensureEndingSlash( func_get_arg($i) );
			}
		}
		
		/**
		 * Load all of the files stated, using the paths given from 'addPaths'
		 * as a basis for where to look.
		 */
		public static function load()
		{
			$numArgs = func_num_args();
			for ( $i = 0; $i < $numArgs; $i++ ) {
				$file = func_get_arg( $i );
				Flexi::loadFile( $file );
			}
		}
		
		private static $defaultFunction   = 'index';
		private static $defaultController = 'index';
		private static $defaultParam      = null;
		
		private static $rootURI = '/';
		
		/**
		 * When a controller is not found Flexi must run something instead
		 * to act as the default page to serve.
		 * 
		 * The controller (and which method) to run on those occasions is stated here.
		 * 
		 * If the controller was found and it was only the method that was missing,
		 * then the default method stated here is applied to that controller.
		 * 
		 * @param class The name of the controller to run by default, when the selected one isn't found.
		 * @param method The method to run on that controller.
		 * @param defaultParam An array of default parameters to pass into that controller, otherwise null for no parameters. Defaults to null.
		 */
		public static function setDefaultController( $class, $method, $defaultParam=null )
		{
			Flexi::$defaultController = $class;
			Flexi::$defaultFunction = $method;
			Flexi::$defaultParam = $defaultParam;
		}
		
		/**
		 * Sets the root location of this site.
		 * By default this is '/', but it can be changed if the site is living within a sub-folder
		 * of the site.
		 * 
		 * For example if you wanted the site running at 'example.com/blah/' then you would set this
		 * to '/blah'.
		 * 
		 * @param root The root location of Flexi within your site.
		 */
		public static function setRootURI( $root )
		{
			Flexi::$rootURI = Flexi::ensureEndingSlash( $root );
		}
		
		/**
		 * This returns whatever has been set using 'setRootURI'.
		 * 
		 * @return The root URI set for this site.
		 */
		public static function getRootURI()
		{
			return Flexi::$rootURI;
		}
		
		/**
		 * Loads and runs the config file stated.
		 * This is relative to the index.php file.
		 * 
		 * By default 'config.php' is always run, but you can use this to run more
		 * if you have split your configurations across multiple files.
		 * 
		 * @param configFile A path to the config file to run.
		 */
		public static function loadConfig( $configFile )
		{
			if ( file_exists($configFile) ) {
				require( $configFile );
			} else {
				throw new Exception( 'Configuration file not found: ' . $configFile );
			}
		}
		
		/**
		 * Breaks the request URI used into it's parts and then returns them
		 * split up in an array.
		 * 
		 * They are split by the '/' delimiter.
		 * 
		 * @return An array containing each of the parts that make up the request URI.
		 */
		public static function getURISplit()
		{
			// first explode removed the query data
			$uri = explode( '?', $_SERVER['REQUEST_URI'] );
			$uri = $uri[0];
			
			// skip the root URI
			$rootURI = Flexi::getRootURI();
			if ( isset($rootURI) ) {
				$left = substr( $uri, 0, strlen($rootURI) );
				
				if ( $rootURI === $left ) {
					$uri = substr( $uri, strlen($rootURI) );
				}
			}
			
			return explode( '/', $uri );
		}
		
		/**
		 * Parses the URI for this page and finds the appropriate controller to run.
		 * This is the last thing that should happen in the index and should never be
		 * called again.
		 * 
		 * This is called automatically by the index.php script.
		 */
		public static function start()
		{
			$uriParts = Flexi::getURISplit();
			
			$name     = null;
			$function = null;
			$params   = array();
			
			foreach ( $uriParts as $part ) {
				if ( $part !== '' ) {
                    $part = urldecode( $part );
                    
					if ( $name == null ) {
						$name = $part;
					} else if ( $function == null ) {
						$function = $part;
					} else {
						$params[]= $part;
					}
				}
			}
			
			// check we have a name and it's not for this script
			if ( $name == null || ($rootURI.$name) == $_SERVER['PHP_SELF'] ) {
				$name = Flexi::$defaultController;
			}
			
			// ignore functions beginning with an underscore
			if ( strpos($function, '_') === 0 ) {
				$function = null;
			}
			
			if ( $function == null ) {
				$function = Flexi::$defaultFunction;
			}
			
			$controllerPath = Flexi::findController( $name );
			if ( $controllerPath === false ) {
				$name           = Flexi::$defaultController;
				$controllerPath = Flexi::findController( $name );
			}
			
			if ( $controllerPath === false ) {
				throw new Exception( 'Default controller not found: ' . Flexi::$defaultController );
			} else {
				require $controllerPath;
				
				if ( class_exists($name) ) {
					$controller = new $name;
					
					if ( ! method_exists($controller, $function) ) {
						$function = Flexi::$defaultFunction;
					}
					
					$frame = null;
					if ( Flexi::$frames != null ) {
						$frame = Flexi::$frames->getFrame( $name, $function );
						$controller->__setFrame( $frame );
					}
					
					if ( ! Flexi::tryControllerMethod($controller, $name, $function, $params) ) {
						throw new Exception( 'Default method \'' . $function . '\' not found in controller: ' . $name . '\'.' );
					} else if ( $frame !== null ) {
						$frame->_runToEnd();
					}
				} else {
					throw new Exception( 'Controller loaded but class not found: ' . $name );
				}
			}
		}
		
		/**
		 * Attempts to invokve the method stated on the controller given. If the method is
		 * not found then false is returned. If it is found then the method is called and
		 * then true is returned after the call has ended.
		 * 
		 * If the method requires more parameters then those in params then it will be
		 * padded with the default parameter.
		 * 
		 * @param controller The controller to run the method on.
		 * @param class The class of the controller.
		 * @param method The method to call on the controller.
		 * @param params An array containing each of the parameters to pass in to the method.
		 */
		private static function tryControllerMethod( $controller, $class, $method, $params )
		{
			if ( method_exists($controller, $method) ) {
				$method = new ReflectionMethod( $class, $method );
				$num = $method->getNumberOfRequiredParameters();
				
				while ( count($params) < $num ) {
					$params[]= Flexi::$defaultParam;
				}
				
				$method->invokeArgs( $controller, $params );
				
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Given the name of a Controller class, this will search with it
		 * using all of the paths it has stored.
		 * 
		 * @param name The name of the controller to load.
		 */
		private static function findController( $name )
		{
			$success = false;
			
			foreach ( Flexi::$controllerPaths as $subFolder ) {
				$path = Flexi::findFile( $subFolder . $name );
				
				if ( $path !== false ) {
					return $path;
				}
			}
			
			return false;
		}
		
		/**
		 * Looks for the stated file in the frameworks set paths.
		 * If found then it is loaded.
		 * 
		 * The reload is to state if it should be required, or required_once.
		 * When reload is false then required_once is used.
		 * 
		 * @param file The file to load.
		 * @param loadOnce True (the default) if the file should only ever be loaded once, otherwise false to load again.
		 */
		public static function loadFile( $file, $loadOnce=true )
		{
			$filePath = Flexi::findFile( $file );
			
			if ( $filePath === false ) {
				throw new Exception( 'File not found: ' . $file );
			} else {
				if ( $loadOnce ) {
					require_once( $filePath );
				} else {
					require( $filePath );
				}
				
				$locals = get_defined_vars();
				
				unset( $locals['filePath'] );
				unset( $locals['file'] );
				
				foreach ( $locals as $local => $val ) {
					$GLOBALS[ $local ] = $val;
				}
			}
		}
		
		/**
		 * Using the name given this will search for the file.
		 * A path to that file is returned if it is found,
		 * otherwise false is return.
		 * 
		 * @param file The file to search for.
		 * @return false if the file is not found, otherwise a path to the stated file.
		 */
		public static function findFile( $file )
		{
			$countPaths = count( Flexi::$searchPaths );
			for ( $i = 0; $i < $countPaths; $i++ ) {                
                $path = Flexi::$searchPaths[$i] . $file . '.php';
				if ( file_exists($path) ) {
					return $path;
				}
				
				$path2 = Flexi::$searchPaths[$i] . $file;
				if ( file_exists($path2) ) {
					return $path2;
				}
			}
			
			return false;
		}
		
		/**
		 * Checks if the given path ends with a slash. A path ending with a slash is returned,
		 * this is either the original path unaltered (if it already ended with one) or just
		 * the original path with a slash appended to it.
		 * 
		 * @param The path to check.
		 * @return The path given but ensured to end with a slash (/).
		 */
		private static function ensureEndingSlash( $path )
		{
			if (
					$path !== ''
				 && strrpos($path, '/') !== (strlen($path)-1)
			) {
				return $path . '/';
			} else {
				return $path;
			}
		}
		
		/*
		 * No constructor.
		 */
		private function __construct()
		{
		}
	}
	
	/**
	 * Stores and retrieves your frames.
	 */
	class FramesHandler
	{
		private $defaultSection = 'content';
		private $frames = array();
		
		private function newFrameItem( $controller, $function, &$frame )
		{
			return array(
					'controller' => $controller,
					'function'   => $function  ,
					'frame'      => $frame
			);
		}
		
		/**
		 * Sets a frame to be applied to the controller or function stated.
		 */
		public function setFrame( $controller, $function, &$frame )
		{
			if ( $frame == null ) {
				throw new Exception( "Given frame cannot be null!" );
			}
			
			$this->frames[]= $this->newFrameItem( $controller, $function, $frame );
		}
		
		/**
		 * @return Null if no frame is found, otherwise a new Frame object for a Controller to use.
		 */
		public function getFrame( $controller, $function )
		{
			$controller = strtolower($controller);
			$function   = strtolower($function);
			
			for ( $i = count($this->frames)-1; $i >= 0; $i-- ) {
				$frameItem = $this->frames[$i];
				$frameConn = strtolower( $frameItem['controller'] );
				$frameFun  = strtolower( $frameItem['function'] );
				
				if (
		    		   ( $frameConn == null || $frameConn === $controller )
					&& ( $frameFun == null || $frameFun === $function )
				) {
					$frame = new Frame();
					$frame->_setConfig( $frameItem['frame'], $this->defaultSection );
					
					return $frame;
				}
			}
			
			return null;
		}
		
		public function setDefaultFrameView( $section )
		{
			$this->defaultSection = $section;
		}
	}
?>
