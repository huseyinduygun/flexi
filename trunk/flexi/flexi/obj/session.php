<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
    /**
     * A wrapper for simplifying using Sessions, and to make them more object
     * oriented.
     *
     * Creating an instance of the Session starts the current session. You can
     * destroy it using the 'destroy' method.
     *
     * To set and get session fields you just access them as properties on this
     * Session object.
     */
    class Session
    {
        public function __construct()
        {
            session_start();
        }

        public function __get( $field )
        {
            return $_SESSION[$field];
        }

        public function __set( $field, $val )
        {
            $_SESSION[$field] = $val;
        }

        public function __isset( $field )
        {
            return isset( $_SESSION[$field] );
        }

        public function __unset( $field )
        {
            unset( $_SESSION[$field] );
        }

        public function getID()
        {
            return session_id();
        }

        public function setID($id)
        {
            session_id( $id );
        }

        public function destroy()
        {
            foreach ( $_SESSION as $key => $value ) {
                unset( $_SESSION[$key] );
            }

            session_destroy();
        }
    }
?>