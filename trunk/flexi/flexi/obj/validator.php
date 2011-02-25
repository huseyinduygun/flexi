<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
    /**
     * This is for validating values stored in a given array, namely $_GET and
     * $_POST. If an array is not given when it is created then it will work on
     * $_POST values by default.
     *
     * Most methods return the Validator object you are calling on. This is to
     * allow method chaining.
     *
     * On each validation operation you can pass in an error message, which will
     * be returned by 'getError'.
     */
    class Validator
    {
        private $array;
        private $hasValue;
        private $isValid;
        private $error;
        private $alt;

        /**
         *
         * @param <type> $array null to use $_POST (the default), otherwise an array of values for this to validate.
         */
        public function __construct( $array=null )
        {
            if ( $array ) {
                if ( ! is_array($array) ) {
                    throw new Exception( "'array' must be an Array or null; object of type '" . gettype($array) . "' was given." );
                }

                $this->array = $array;
            } else {
                $this->array = $_POST;
            }

            $this->clear();
        }

        /**
         * Cleares all form validation calls made so far.
         */
        public function clear()
        {
            $this->hasValue = false;
            $this->isValid  = false;
            $this->error    = null;
            $this->alt      = false;

            return $this;
        }

        /*  Starter Functions
         *
         * $this->form->required( 'username' );
         * $this->form->optional( 'location' );
         */

        /**
         * This is cleared of all previous settings before the value is retrieved.
         * 
         * @param <type> $field
         * @param <type> $error
         */
        public function field( $field, $error=null )
        {
            $this->clear();

            $this->setValue( $field );
            $this->isValid = isset( $this->value );
            
            if ( !$this->isValid ) {
                $this->setError( $error );
            }

            return $this;
        }

        /**
         * Allows people to state which field they are working on via property
         * grabbing. i.e. $this->username is the same as $this->field('username')
         * 
         * @param <type> $field
         * @return <type>
         */
        public function __get( $field )
	{
            return $this->field( $field );
        }

        /**
         * Gets the value from the field stated.
         *
         * This is cleared of all previous information before the value is retrieved.
         * 
         * @param $field The name of the field to retrieve from the validators array.
         * @param $alt An alternate value if the given field is missing.
         */
        public function optional( $field, $alt )
        {
            $this->clear();
            
            $this->setValue( $field );
            if ( ! $this->value ) {
                $this->value = $alt;
            }

            $this->isValid = true;

            return $this;
        }

        private function setValue( $field )
        {
            $arrVal = $this->array[ $field ];
            if ( !isset($arrVal) ) {
                $arrVal = false;
            }

            $this->value = $arrVal;
            $this->hasValue = true;
        }

        /**
         * If this is being called without a value being set, then this will
         * throw an exception. The exception will say that the method named
         * should not be called without a value being set first.
         *
         * @param <type> $method The name of the method being called.
         */
        private function ensureValue( $method )
        {
            if ( !$this->hasValue ) {
                throw new Exception( "Calling method " . $method . " without setting a field (call 'required' or 'optional' first!)." );
            }
        }

        /*  Altering Functions
         *
         * These functions alter the state of the value stored in some way.
         *
         * $this->form->required( 'username' )->len( 1, 20 )->isAlphaNumeric();
         * $this->form->required( 'password' )->minLen( 8 );
         */

        /**
         * Sets an alternate value for the field, but only if the field was not
         * provided.
         *
         * @param $alt
         * @return This object to allow you to chain methods.
         */
        public function alt( $alt )
        {
            $this->ensureValue( 'alt' );

            if ( $this->value === false ) {
                $this->alt = $alt;
            }

            return $this;
        }

        /**
         * Trims the value stored, if there is one.
         * @return This validator to allow method chaining.
         */
        public function trim()
        {
            $this->ensureValue( 'trim' );

            if ( $this->value ) {
                $this->value = trim( $this->value );
            }

            return $this;
        }
        
        /*  Validation Functions
         *
         * $this->form->required( 'username' )->len( 1, 20 )->isAlphaNumeric();
         * $this->form->required( 'password' )->minLen( 8 );
         */

        /**
         * Tests if this has a numeric value.
         * 
         * @param <type> $error
         */
        public function isNumeric( $error=null )
        {
            $this->ensureValue( 'isNumeric' );

            if ( !is_numeric($this->value) ) {
                $this->setError( $error );
            }

            return $this;
        }

        public function isAlphaNumeric( $error=null )
        {
            $this->ensureValue( 'isAlphaNumeric' );

            if ( !ctype_alnum($this->value) ) {
                $this->setError( $error );
            }

            return $this;
        }

        public function isAlpha( $error=null )
        {
            $this->ensureValue( 'isAlpha' );
            
            if ( !ctype_alpha($this->value) ) {
                $this->setError( $error );
            }

            return $this;
        }

        /**
         * Ensures that the value exists with a length of at least 1.
         * This does not take into account whitespace, you'll have to
         * call trim first to do this.
         */
        public function exists( $error=null )
        {
            return $this->minLen( 1, $error );
        }
        
        public function minLen( $len, $error=null )
        {
            $this->ensureValue( 'minLen' );

            if ( !$this->value || strlen($this->value) < $len ) {
                $this->setError( $error );
            }

            return $this;
        }

        /**
         *
         * @param <type> $len The maximum length for the value, inclusive.
         * @param <type> $error
         */
        public function maxLen( $len, $error=null )
        {
            $this->ensureValue( 'maxLen' );

            if ( !$this->value || strlen($this->value) > $len ) {
                $this->setError( $error );
            }

            return $this;
        }

        /**
         * Both min and max are inclusive.
         *
         * @param <type> $min The minimum length for the value, inclusive.
         * @param <type> $max The maximum length for the value, inclusive.
         * @param <type> $error Optional, the error to set if this condition is broken.
         */
        public function len( $min, $max, $error=null )
        {
            $this->ensureValue( 'len' );

            if ( !$this->value ) {
                $this->setError( $error );
            } else {
                $strlen = strlen($this->value);
                if ( $strlen < $min || $strlen > $max ) {
                    $this->setError( $error );
                }
            }

            return $this;
        }

        /**
         * Tests if the regex given matches the value, somewhere.
         * Using the '^' and '$' you can match against the whole of value.
         * 
         * @param <type> $regex The pattern to test against the stored value.
         * @param <type> $error Optional, the error to set if the regex does not match.
         */
        public function regex( $regex, $error=null )
        {
            $this->ensureValue( 'regex' );

            if ( !$this->value || !preg_match($regex, $this->value) ) {
                $this->setError( $error );
            }

            return $this;
        }

        /**
         * Tests if this value matches the string given, entirely.
         * 
         * @param <type> $other A string which the value must match.
         * @param <type> $error Optional, the error to set if the strings don't match.
         */
        public function equals( $other, $error=null )
        {
            $this->ensureValue( 'equals' );

            if ( $this->value !== $other ) {
                $this->setError( $error );
            }

            return $this;
        }

        /**
         * Runs the given function and passes in the value.
         * The function should then return true or false to state if it is error'd
         * or not.
         * 
         * @param $fun The function to apply to the given form.
         */
        public function check( $fun, $error=null )
        {
            $this->ensureValue( 'check' );

            if ( ! $fun($this->value) ) {
                $this->setError( $error );
            }

            return $this;
        }

        /*  Finishing Methods
         *
         * These are for retrieving the final values.
         */

        /**
         * If no value was stored in the field and an alt was set, then the alt
         * is returned.
         * 
         * If this object is valid, then it is returned.
         * Otherwise the 'alt' value is returned, but if it's missing then null
         * is returned instead.
         * 
         * @param $alt An alternate value to return if this is invalid.
         * @return The value being validated, alt or null depending on if it's valid or not.
         */
        public function get( $alt=null )
        {
            $this->ensureValue( 'get' );

            if ( $this->alt !== false ) {
                return $this->alt;
            } else if ( $this->isValid() ) {
                return $this->value;
            } else {
                return $alt;
            }
        }
        
        /**
         * Same as get, only this will run the value to be parsed as an int first.
         * If the return value is not a numeric value, then this will return null.
         */
        public function getInt( $alt=null )
        {
            $this->ensureValue( 'get' );

            if ( $this->alt !== false ) {
                return $this->alt;
            } else if ( $this->isValid() && is_numeric($this->value) ) {
                return intval( $this->value, 10 );
            } else {
                return $alt;
            }
        }

        /**
         * The error will only be stored on the first time this is called.
         * Regardless of if there is an error message or not, this will be set
         * as being invalid.
         * 
         * @param <type> $error Null to not set an error message, otherwise a message for this error.
         */
        private function setError( $error )
        {
            if ( $error && $this->error === null ) {
                $this->error = $error;
            }
            
            $this->isValid = false;
        }

        /**
         * The error returned is the first error that was recorded.
         * All errors since then will be lost.
         * 
         * @return Null if there is no error, otherwise a stored message for the first error that occurred.
         */
        public function getError()
        {
            if ( $this->alt !== false ) {
                return null;
            } else {
                return $this->error;
            }
        }

        /**
         * If no value was stored in the field, but an alt was provided, then
         * this is valid.
         *
         * @return True if there were no errors, otherwise false.
         */
        public function isValid()
        {
            $this->ensureValue( 'isValid' );
            return $this->alt !== false || $this->isValid;
        }
    }
?>