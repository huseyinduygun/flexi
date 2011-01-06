<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
	class XHTML
	{
		public static function html()
		{
			$args = func_get_args();
			return new XHTMLObj( 'html', implode('',$args) );
		}
		
		public static function head()
		{
			$args = func_get_args();
			return new XHTMLObj( 'head', implode('',$args) );
		}
		
		public static function body()
		{
			$args = func_get_args();
			return new XHTMLObj( 'body', implode('',$args) );
		}
		
		public static function link( $url='', $rel='', $type='' )
		{
			$link = new XHTMLObj( 'link', false );
			return $link->href($url)->rel($rel)->type($type);
		}
		
		public static function meta()
		{
			return new XHTMLObj( 'meta', false );
		}
		
		public static function object()
		{
			return new XHTMLObj( 'object', '' );
		}
		
		public static function title($title)
		{
			return new XHTMLObj( 'title', $title );
		}
		
		public static function h1( $text = '' )
		{
			return XHTML::h( '1', $text );
		}
		
		public static function h2( $text = '' )
		{
			return XHTML::h( '2', $text );
		}
		
		public static function h3( $text = '' )
		{
			return XHTML::h( '3', $text );
		}
		
		public static function h4( $text = '' )
		{
			return XHTML::h( '4', $text );
		}
		
		public static function h5( $text = '' )
		{
			return XHTML::h( '5', $text );
		}
		
		public static function h6( $text = '' )
		{
			return XHTML::h( '6', $text );
		}
		
		private static function h( $postfix, $text )
		{
			return new XHTMLObj( 'h'.$postfix, $text );
		}
		
		public static function p()
		{
			$args = func_get_args();
			return new XHTMLObj( 'p', implode('',$args) );
		}
		
		public static function div()
		{
			$args = func_get_args();
			return new XHTMLObj( 'div', implode('',$args) );
		}
		
		public static function a( $url='', $text='' )
		{
			$a = new XHTMLObj( 'a', $text );
			return $a->href( $url );
		}
		
		public static function ol()
		{
			$args = func_get_args();
			return new XHTMLObj( 'ol', implode('',$args) );
		}
		
		public static function ul()
		{
			$args = func_get_args();
			return new XHTMLObj( 'ul', implode('',$args) );
		}
		
		public static function li()
		{
			$args = func_get_args();
			return new XHTMLObj( 'li', implode('',$args) );
		}
		
		public static function dl()
		{
			$args = func_get_args();
			return new XHTMLObj( 'dl', implode('',$args) );
		}
		
		public static function dt()
		{
			$args = func_get_args();
			return new XHTMLObj( 'dt', implode('',$args) );
		}
		
		public static function dd()
		{
			$args = func_get_args();
			return new XHTMLObj( 'dl', implode('',$args) );
		}
		
		public static function hr()
		{
			return new XHTMLObj( 'hr', false );
		}
		
		public static function noscript()
		{
			$args = func_get_args();
			return new XHTMLObj( 'noscript', implode('',$args) );
		}
		
		public static function pre()
		{
			$args = func_get_args();
			return new XHTMLObj( 'pre', implode('',$args) );
		}
		
		public static function script()
		{
			$args = func_get_args();
			return new XHTMLObj( 'script', implode('',$args) );
		}
		
		public static function em()
		{
			$args = func_get_args();
			return new XHTMLObj( 'em', implode('',$args) );
		}
		
		public static function strong()
		{
			$args = func_get_args();
			return new XHTMLObj( 'strong', implode('',$args) );
		}
		
		public static function code()
		{
			$args = func_get_args();
			return new XHTMLObj( 'code', implode('',$args) );
		}
		
		public static function samp()
		{
			$args = func_get_args();
			return new XHTMLObj( 'samp', implode('',$args) );
		}
		
		public static function abbr()
		{
			$args = func_get_args();
			return new XHTMLObj( 'abbr', implode('',$args) );
		}
		
		public static function acronym()
		{
			$args = func_get_args();
			return new XHTMLObj( 'acronym', implode('',$args) );
		}
		
		public static function dfn()
		{
			$args = func_get_args();
			return new XHTMLObj( 'dfn', implode('',$args) );
		}
		
		public static function b()
		{
			$args = func_get_args();
			return new XHTMLObj( 'b', implode('',$args) );
		}
		
		public static function i()
		{
			$args = func_get_args();
			return new XHTMLObj( 'i', implode('',$args) );
		}
		
		public static function big()
		{
			$args = func_get_args();
			return new XHTMLObj( 'big', implode('',$args) );
		}
		
		public static function small()
		{
			$args = func_get_args();
			return new XHTMLObj( 'small', implode('',$args) );
		}
		
		public static function tt()
		{
			$args = func_get_args();
			return new XHTMLObj( 'tt', implode('',$args) );
		}
		
		public static function span()
		{
			$args = func_get_args();
			return new XHTMLObj( 'span', implode('',$args) );
		}
		
		public static function br()
		{
			return new XHTMLObj( 'br', false );
		}
		
		public static function bdo()
		{
			$args = func_get_args();
			return new XHTMLObj( 'bdo', implode('',$args) );
		}
		
		public static function cite()
		{
			$args = func_get_args();
			return new XHTMLObj( 'cite', implode('',$args) );
		}
		
		public static function del()
		{
			$args = func_get_args();
			return new XHTMLObj( 'del', implode('',$args) );
		}
		
		public static function ins()
		{
			$args = func_get_args();
			return new XHTMLObj( 'ins', implode('',$args) );
		}
		
		public static function q()
		{
			$args = func_get_args();
			return new XHTMLObj( 'q', implode('',$args) );
		}
		
		public static function sub()
		{
			$args = func_get_args();
			return new XHTMLObj( 'sub', implode('',$args) );
		}
		
		public static function sup()
		{
			$args = func_get_args();
			return new XHTMLObj( 'sup', implode('',$args) );
		}
		
		public static function area()
		{
			return new XHTMLObj( 'area', false );
		}
		
		public static function img( $src='' )
		{
			$img = new XHTMLObj( 'img', false );
			return $img->src( $src );
		}
		
		public static function map()
		{
			$args = func_get_args();
			return new XHTMLObj( 'map', implode('',$args) );
		}
		
		public static function param()
		{
			return new XHTMLObj( 'param', false );
		}
		
		public static function form( $action='.', $method='post', $onsubmit='' )
		{
			$form = new XHTMLObj( 'form', '' );
			
			$args = func_get_args();
			if ( count($args) > 3 ) {
				$args = array_slice( $args, 3 );
				$form->html( implode('', $args) );
			}
			
			return $form->action($action)->method($method)->onsubmit($onsubmit);
		}
		
		public static function checkbox( $name='', $value='', $checked=null )
		{
			return XHTML::checkable( 'checkbox', $name, $value, $checked );
		}
		
		public static function radio( $name='', $value='', $checked=null )
		{
			return XHTML::checkable( 'radio', $name, $value, $checked );
		}
		
		public static function input( $type='' )
		{
			$input = new XHTMLObj( 'input', false );
			return $input->type( $type );
		}
		
		private static function checkable( $type, $name='', $value='', $checked=null )
		{
			if ( $checked === true ) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
			
			return XHTML::input($type)->name($name)->value($value)->checked($checked);
		}
		
		public static function button( $text='', $name='', $onclick='' )
		{
			return XHTML::input( 'button' )->value( $text )->name( $name )->onclick( $onclick );
		}
		
		public static function submit( $text='', $name='', $onclick='' )
		{
			return XHTML::input( 'submit' )->value( $text )->name( $name )->onclick( $onclick );
		}
		
		public static function image($src='')
		{
			return XHTML::input('image')->src($src);
		}
		
		public static function reset($text='')
		{
			return XHTML::input('reset')->value($text);
		}
		
		public static function text( $name='', $size='', $maxlength='')
		{
			return XHTML::input('text')->name($name)->size($size)->maxlength($maxlength);
		}
		
		public static function password( $name='', $size='', $maxlength='')
		{
			return XHTML::input('password')->name($name)->size($size)->maxlength($maxlength);
		}
		
		public static function file( $name='' )
		{
			return XHTML::input('file')->name($name);
		}
		
		public static function hidden($name='', $value='')
		{
			return XHTML::input('hidden')->name($name)->value($value);
		}
		
		public static function label($for='', $text='')
		{
			$label = new XHTMLObj('label', $text);
			return $label->for( $for );
		}
		
		public static function legend()
		{
			return new XHTMLObj('legend');
		}
		
		public static function option($value='')
		{
			$option = new XHTMLObj('option', false);
			return $option->value( $value );
		}
		
		public static function optgroup()
		{
			$args = func_get_args();
			return new XHTMLObj( 'optgroup', implode('',$args) );
		}
		
		public static function select( $name='' )
		{
			$select = new XHTMLObj('select', '');
			return $select->name( $name );
		}
		
		public static function textarea( $name='', $cols='', $rows='' )
		{
			$textarea = new XHTMLObj('textarea', '');
			return $textarea->name($name)->cols( $cols )->rows( $rows );
		}
		
		public static function table()
		{
			$args = func_get_args();
			return new XHTMLObj( 'table', implode('',$args) );
		}
		
		public static function tr()
		{
			$args = func_get_args();
			return new XHTMLObj( 'tr', implode('',$args) );
		}
		
		public static function th()
		{
			$args = func_get_args();
			return new XHTMLObj( 'th', implode('',$args) );
		}
		
		public static function td()
		{
			$args = func_get_args();
			return new XHTMLObj( 'td', implode('',$args) );
		}
		
		public static function colgroup()
		{
			$args = func_get_args();
			return new XHTMLObj( 'colgroup', implode('',$args) );
		}
		
		public static function col()
		{
			$args = func_get_args();
			return new XHTMLObj( 'col', implode('',$args) );
		}
		
		public static function caption()
		{
			$args = func_get_args();
			return new XHTMLObj( 'caption', implode('',$args) );
		}
		
		public static function thead()
		{
			$args = func_get_args();
			return new XHTMLObj( 'thead', implode('',$args) );
		}
		
		public static function tbody()
		{
			$args = func_get_args();
			return new XHTMLObj( 'tbody', implode('',$args) );
		}
		
		public static function tfoot()
		{
			$args = func_get_args();
			return new XHTMLObj( 'tfoot', implode('',$args) );
		}
		
		public static function frameset($cols='', $rows='')
		{
			$obj = new XHTMLObj('frameset');
			return $obj->cols($cols)->rows($rows);
		}
		
		public static function frame($src='')
		{
			$obj = new XHTMLObj('frame');
			return $obj->src($src);
		}
		
		public static function noframes()
		{
			$args = func_get_args();
			return new XHTMLObj( 'noframes', implode('',$args) );
		}
		
		public static function iframe($src='')
		{
			$obj = new XHTMLObj('iframe');
			return $obj->src($src);
		}
		
		/**
		 * No constructor
		 */
		private function __construct()
		{
		}
	}
	
	class XHTMLObj
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
			
			if ( $children === false ) {
				$this->html = '';
			} else if ( is_array($children) ) {
				$this->html = implode( '', $children );
			} else {
				$this->html = (string)$children;
			}
		}
		
		public function __toString()
		{
			$str = '<'.$this->tag . ' ';
			foreach ( $this->attrs as $key => $val ) {
				if ( is_string($val) && $val !== '' ) {
					$str .= $key . "='$val' ";
				}
			}
			
			if ( $this->selfClosing ) {
				return $str . '/>';
			} else {
				return $str . '>' . $this->html . '</'.$this->tag.'>';
			}
		}
		
		public function html()
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