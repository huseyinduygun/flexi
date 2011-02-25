<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	/**
	 * A base Controller. The intention is for modules to be dynamically set to it's
	 * fields. These modules can be ones included with the libraries or ones you have
	 * created yourself.
	 * 
	 * By default it loads and stores a Loader object that can be access via it's
	 * 'load' field.
	 */
	class Controller extends LoaderObject
	{
        private $flexi;
		private $internalVars;
		private $isInsideView;
        
		/**
		 * Standard constructor. Creates a new Controller and it builds it's own Loader object.
		 */
		public function __construct()
		{
			parent::__construct();
            
            $this->flexi = Flexi::getFlexi();
            $isInsideView = false;
		}
        
        /**
         * 
         */
        public function getFlexi()
        {
            return $this->flexi;
        }
		
		/**
		 * Sets the frame for this controller to use.
		 * Setting it to null will mean 'no frame'.
		 * 
		 * @param frame Null to set no frame, otherwise the frame for this Controller to use.
		 */
		public function __setFrame( $frame )
		{
			$this->frame = $frame;
			if ( $this->frame != null ) {
				$this->frame->_setController( $this );
			}
		}
		
		/**
		 * Loads the stated view file as though it is being run from within this Controller.
		 * 
		 * The optional params array holds mappings from field to value. The fields are made
		 * into variables that hold their matching values for use within the view when it is
		 * run.
		 * 
		 * @param file The view file to run.
		 * @param params null for no parameters (default), otherwise an array of variableName => variableValue.
		 */
		public function __view( $file, &$params=null, $useFrame=true )
		{
            if ( !$isInsideView && $useFrame && $this->frame != null ) {
                $this->frame->_loseDefault();
                $this->frame->_runTo();
            }
			
			$this->__viewInner( $file, $params );
		}
		
		public function __viewInner( $file, &$params )
		{
			if ( $params != null ) {
				foreach ( $params as $var => $value ) {
					$$var = $value;
				}
			}
			
			$filePath = $this->getFlexi()->findFile( $file );
			if ( $filePath === false ) {
				throw new Exception( 'View not found: ' . $file );
			} else {
                $isInsideView = true;
				require( $filePath );
                $isInsideView = false;
			}
		}
		
		/**
		 * The default function which always exists, by default.
		 */
		public function index()
		{
			?>
				'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
				<html>
					<head>Flexi | Welcome</head>
					<body>
						<h1>Flexi is running!</h1>
						<p>Welcome, define your own controller and override the index method to get started.</p>
					</body>
				</html>
			<?php
		}
	}
?>