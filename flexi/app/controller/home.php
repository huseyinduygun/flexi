<?php if ( ! defined('ACCESS_OK')) exit('Can\'t access scripts directly!');
    class Index extends Controller
    {
        public function __construct()
        {
            parent::__construct();

            // load models here
//            $this->load->obj( 'model/users' );
            // and any objects you need
//            $this->load->obj( 'obj/validator' );
//            $this->load->obj( 'obj/session' );
        }
		
        public function index()
        {
            $this->load->view( 'index.php' );
        }
    }
?>