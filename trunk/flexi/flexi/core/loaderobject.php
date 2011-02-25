<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	/**
	 * Flexi Object
	 * 
	 * <p>This is a generic building block for other objects to be built on top of.
	 * It is built as an object that is easy to add new objects too.</p>
	 * 
	 * <p>It's magic get and set methods are overrided to allow fields to be added
	 * more freely. It also has a loads for loading objects into it.</p>
	 */
	class LoaderObject extends FlexiObject
	{
		public $load;
		
		public function __construct()
		{
			$this->load = new Loader( $this );
		}
	}
?>