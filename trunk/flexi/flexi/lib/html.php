<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	class HTML
	{
		public static function html()
		{
			$args = func_get_args();
			return new HTMLObj( 'html', implode('',$args) );
		}
		
		public static function head()
		{
			$args = func_get_args();
			return new HTMLObj( 'head', implode('',$args) );
		}
		
		public static function body()
		{
			$args = func_get_args();
			return new HTMLObj( 'body', implode('',$args) );
		}
		
		public static function link( $url='', $rel='', $type='' )
		{
			$link = new HTMLObj( 'link', false );
			return $link->href($url)->rel($rel)->type($type);
		}
		
		public static function meta()
		{
			return new HTMLObj( 'meta', false );
		}
		
		public static function object()
		{
			return new HTMLObj( 'object', '' );
		}
		
		public static function title($title)
		{
			return new HTMLObj( 'title', $title );
		}
		
		public static function h1( $text = '' )
		{
			return HTML::h( '1', $text );
		}
		
		public static function h2( $text = '' )
		{
			return HTML::h( '2', $text );
		}
		
		public static function h3( $text = '' )
		{
			return HTML::h( '3', $text );
		}
		
		public static function h4( $text = '' )
		{
			return HTML::h( '4', $text );
		}
		
		public static function h5( $text = '' )
		{
			return HTML::h( '5', $text );
		}
		
		public static function h6( $text = '' )
		{
			return HTML::h( '6', $text );
		}
		
		private static function h( $postfix, $text )
		{
			return new HTMLObj( 'h'.$postfix, $text );
		}
		
		public static function p()
		{
			$args = func_get_args();
			return new HTMLObj( 'p', implode('',$args) );
		}
		
		public static function div()
		{
			$args = func_get_args();
			return new HTMLObj( 'div', implode('',$args) );
		}
		
		public static function a( $url='', $text='' )
		{
			$a = new HTMLObj( 'a', $text );
			return $a->href( $url );
		}
		
		public static function ol()
		{
			$args = func_get_args();
			return new HTMLObj( 'ol', implode('',$args) );
		}
		
		public static function ul()
		{
			$args = func_get_args();
			return new HTMLObj( 'ul', implode('',$args) );
		}
		
		public static function li()
		{
			$args = func_get_args();
			return new HTMLObj( 'li', implode('',$args) );
		}
		
		public static function dl()
		{
			$args = func_get_args();
			return new HTMLObj( 'dl', implode('',$args) );
		}
		
		public static function dt()
		{
			$args = func_get_args();
			return new HTMLObj( 'dt', implode('',$args) );
		}
		
		public static function dd()
		{
			$args = func_get_args();
			return new HTMLObj( 'dl', implode('',$args) );
		}
		
		public static function hr()
		{
			return new HTMLObj( 'hr', false );
		}
		
		public static function noscript()
		{
			$args = func_get_args();
			return new HTMLObj( 'noscript', implode('',$args) );
		}
		
		public static function pre()
		{
			$args = func_get_args();
			return new HTMLObj( 'pre', implode('',$args) );
		}
		
		public static function script()
		{
			$args = func_get_args();
			return new HTMLObj( 'script', implode('',$args) );
		}
		
		public static function em()
		{
			$args = func_get_args();
			return new HTMLObj( 'em', implode('',$args) );
		}
		
		public static function strong()
		{
			$args = func_get_args();
			return new HTMLObj( 'strong', implode('',$args) );
		}
		
		public static function code()
		{
			$args = func_get_args();
			return new HTMLObj( 'code', implode('',$args) );
		}
		
		public static function samp()
		{
			$args = func_get_args();
			return new HTMLObj( 'samp', implode('',$args) );
		}
		
		public static function abbr()
		{
			$args = func_get_args();
			return new HTMLObj( 'abbr', implode('',$args) );
		}
		
		public static function acronym()
		{
			$args = func_get_args();
			return new HTMLObj( 'acronym', implode('',$args) );
		}
		
		public static function dfn()
		{
			$args = func_get_args();
			return new HTMLObj( 'dfn', implode('',$args) );
		}
		
		public static function b()
		{
			$args = func_get_args();
			return new HTMLObj( 'b', implode('',$args) );
		}
		
		public static function i()
		{
			$args = func_get_args();
			return new HTMLObj( 'i', implode('',$args) );
		}
		
		public static function big()
		{
			$args = func_get_args();
			return new HTMLObj( 'big', implode('',$args) );
		}
		
		public static function small()
		{
			$args = func_get_args();
			return new HTMLObj( 'small', implode('',$args) );
		}
		
		public static function tt()
		{
			$args = func_get_args();
			return new HTMLObj( 'tt', implode('',$args) );
		}
		
		public static function span()
		{
			$args = func_get_args();
			return new HTMLObj( 'span', implode('',$args) );
		}
		
		public static function br()
		{
			return new HTMLObj( 'br', false );
		}
		
		public static function bdo()
		{
			$args = func_get_args();
			return new HTMLObj( 'bdo', implode('',$args) );
		}
		
		public static function cite()
		{
			$args = func_get_args();
			return new HTMLObj( 'cite', implode('',$args) );
		}
		
		public static function del()
		{
			$args = func_get_args();
			return new HTMLObj( 'del', implode('',$args) );
		}
		
		public static function ins()
		{
			$args = func_get_args();
			return new HTMLObj( 'ins', implode('',$args) );
		}
		
		public static function q()
		{
			$args = func_get_args();
			return new HTMLObj( 'q', implode('',$args) );
		}
		
		public static function sub()
		{
			$args = func_get_args();
			return new HTMLObj( 'sub', implode('',$args) );
		}
		
		public static function sup()
		{
			$args = func_get_args();
			return new HTMLObj( 'sup', implode('',$args) );
		}
		
		public static function area()
		{
			return new HTMLObj( 'area', false );
		}
		
		public static function img( $src='' )
		{
			$img = new HTMLObj( 'img', false );
			return $img->src( $src );
		}
		
		public static function map()
		{
			$args = func_get_args();
			return new HTMLObj( 'map', implode('',$args) );
		}
		
		public static function param()
		{
			return new HTMLObj( 'param', false );
		}
		
		public static function form( $action='.', $method='post', $onsubmit='' )
		{
			$form = new HTMLObj( 'form', '' );
			
			$args = func_get_args();
			if ( count($args) > 3 ) {
				$args = array_slice( $args, 3 );
				$form->html( implode('', $args) );
			}
			
			return $form->action($action)->method($method)->onsubmit($onsubmit);
		}
		
		public static function checkbox( $name='', $value='', $checked=null )
		{
			return HTML::checkable( 'checkbox', $name, $value, $checked );
		}
		
		public static function radio( $name='', $value='', $checked=null )
		{
			return HTML::checkable( 'radio', $name, $value, $checked );
		}
		
		public static function input( $type='' )
		{
			$input = new HTMLObj( 'input', false );
			return $input->type( $type );
		}
		
		private static function checkable( $type, $name='', $value='', $checked=null )
		{
			if ( $checked === true ) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
			
			return HTML::input($type)->name($name)->value($value)->checked($checked);
		}
		
		public static function button( $text='', $name='', $onclick='' )
		{
			return HTML::input( 'button' )->value( $text )->name( $name )->onclick( $onclick );
		}
		
		public static function submit( $text='', $name='', $onclick='' )
		{
			return HTML::input( 'submit' )->value( $text )->name( $name )->onclick( $onclick );
		}
		
		public static function image($src='')
		{
			return HTML::input('image')->src($src);
		}
		
		public static function reset($text='')
		{
			return HTML::input('reset')->value($text);
		}
		
		public static function text( $name='', $size='', $maxlength='')
		{
			return HTML::input('text')->name($name)->size($size)->maxlength($maxlength);
		}
		
		public static function password( $name='', $size='', $maxlength='')
		{
			return HTML::input('password')->name($name)->size($size)->maxlength($maxlength);
		}
		
		public static function file( $name='' )
		{
			return HTML::input('file')->name($name);
		}
		
		public static function hidden($name='', $value='')
		{
			return HTML::input('hidden')->name($name)->value($value);
		}
		
		public static function label($for='', $text='')
		{
			$label = new HTMLObj('label', $text);
			return $label->for( $for );
		}
		
		public static function legend()
		{
			return new HTMLObj('legend');
		}
		
		public static function option($value='')
		{
			$option = new HTMLObj('option', false);
			return $option->value( $value );
		}
		
		public static function optgroup()
		{
			$args = func_get_args();
			return new HTMLObj( 'optgroup', implode('',$args) );
		}
		
		public static function select( $name='' )
		{
			$select = new HTMLObj('select', '');
			return $select->name( $name );
		}
		
		public static function textarea( $name='', $cols='', $rows='' )
		{
			$textarea = new HTMLObj('textarea', '');
			return $textarea->name($name)->cols( $cols )->rows( $rows );
		}
		
		public static function table()
		{
			$args = func_get_args();
			return new HTMLObj( 'table', implode('',$args) );
		}
		
		public static function tr()
		{
			$args = func_get_args();
			return new HTMLObj( 'tr', implode('',$args) );
		}
		
		public static function th()
		{
			$args = func_get_args();
			return new HTMLObj( 'th', implode('',$args) );
		}
		
		public static function td()
		{
			$args = func_get_args();
			return new HTMLObj( 'td', implode('',$args) );
		}
		
		public static function colgroup()
		{
			$args = func_get_args();
			return new HTMLObj( 'colgroup', implode('',$args) );
		}
		
		public static function col()
		{
			$args = func_get_args();
			return new HTMLObj( 'col', implode('',$args) );
		}
		
		public static function caption()
		{
			$args = func_get_args();
			return new HTMLObj( 'caption', implode('',$args) );
		}
		
		public static function thead()
		{
			$args = func_get_args();
			return new HTMLObj( 'thead', implode('',$args) );
		}
		
		public static function tbody()
		{
			$args = func_get_args();
			return new HTMLObj( 'tbody', implode('',$args) );
		}
		
		public static function tfoot()
		{
			$args = func_get_args();
			return new HTMLObj( 'tfoot', implode('',$args) );
		}
		
		public static function frameset($cols='', $rows='')
		{
			$obj = new HTMLObj('frameset');
			return $obj->cols($cols)->rows($rows);
		}
		
		public static function frame($src='')
		{
			$obj = new HTMLObj('frame');
			return $obj->src($src);
		}
		
		public static function noframes()
		{
			$args = func_get_args();
			return new HTMLObj( 'noframes', implode('',$args) );
		}
		
		public static function iframe($src='')
		{
			$obj = new HTMLObj('iframe');
			return $obj->src($src);
		}
		
		/**
		 * No constructor
		 */
		private function __construct()
		{
		}
	}
	
	class HTMLObj
	{
		private $html;
		private $tag;
		private $attrs;
		private $selfClosing;
		
		public function __construct( $tag, $children )
		{
			$this->tag   = $tag;
			$this->attrs = array();
			$this->selfClosing = ( $children === false );
			
			if ( is_string($children) ) {
				$this->html = $children;
			} else if ( is_array($children) ) {
				$this->html = implode( '', $children );
			} else {
				$this->html = '';
			}
		}
		
		public function __toString()
		{
			;
			$str = '<'.$this->tag . ' ';
			foreach ( $this->attrs as $key => $val ) {
				if ( is_string($val) && $val !== '' ) {
					$str .= $key . "='$val' ";
				}
			}
			
			if ( $this->selfClosing ) {
				return $str . '>';
			} else {
				return $str . '>' . $this->html . '</'.$this->tag.'>';
			}
		}
		
		public function html( $html )
		{
			$args = func_get_args();
			$this->html = implode( '', $args );
			return $this;
		}
		
		public function __call( $func, $arguments )
		{
			$this->attrs[ $func ] = implode( ' ', $arguments );
			return $this;
		}
	}
?>