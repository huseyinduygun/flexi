<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	class Str
	{
		public static function endsWith($Haystack, $Needle)
		{
			// Recommended version, using strpos
			return strrpos($Haystack, $Needle) === strlen($Haystack)-strlen($Needle);
		}
	}
?>