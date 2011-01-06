<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	class Loader
	{
		public static $currentController = null;
		
		private $controller;
		
		/** 
		 * Creates a new Loader which is associated to work on the controller given.
		 * This means that any objects it loads (through the obj method) will be
		 * assigned to this given controller.
		 * 
		 * @param controller The controller to associate with this loader.
		 */
		public function __construct( $controller )
		{
			$this->controller = $controller;
		}
		
		/**
		 * Loads the given file, end of.
		 * 
		 * @param file The file to load.
		 * @param loadOnce True to not reload the file if it's already loaded, otherwise false to reload. Defaults true.
		 */
		public function load( $file, $loadOnce=true )
		{
			Flexi::loadFile( $file, $loadOnce );
		}
		
		/**
		 * Loads the given file and then creates a new instance of the class name
		 * given (the intention being that the class was inside the file).
		 * 
		 * An instance of the class will be set to the controller associated with
		 * this loader.
		 * 
		 * If ommitted then the class and variable names will be presumed to be
		 * the name of the file being opnened.
		 * 
		 * Any variables after the className are passed into the constructor of
		 * the class when it is created.
		 * 
		 * @param file The file to open.
		 * @param varName The name of the variable to set the file to, or null to omit.
		 * @param className The name of the class to initialize, or null to omit.
		 */
		public function obj( $file, $varName=null, $className=null )
		{
			$last = strrpos( $file, '/'  );
			if ( $last === false ) {
				$fileStr = $file;
			} else {
				$fileStr = substr( $file, $last+1 );
			}
			
			if ( $className !== null ) {
				$className = trim( $className );
			}
			if ( $className === null || $className === '' ) {
				$className = $fileStr;
			}
			
			if ( $varName !== null ) {
				$varName = trim( $varName );
			}
			if ( $varName === null || $varName === '' ) {
                            $varName = strtolower( $className );
			}
			
			$this->load( $file );
			
			// stored so models can get access to this obj
			Loader::$currentController = $this->controller;
			
			// if has parameters for the object being made
			if ( func_num_args() > 3 ) {
				$params = array_slice( func_get_args(), 3 );
				$reflection = new ReflectionClass( $className );
				$obj = $reflection->newInstanceArgs( $params );
			} else {
				$obj = new $className;
			}
			
			Loader::$currentController  = null;
			$this->controller->$varName = $obj;
			return $obj;
		}
		
		public function view( $file, $params=null )
		{
			$this->controller->__view( $file, $params );
		}
		
		public function getView( $file, $params=null )
		{
		    ob_start();
		    $this->controller->__view( $file, $params, false );
		    $html = ob_get_contents();
		    ob_clean();
		    
		    return $html;
		}
	}
?>