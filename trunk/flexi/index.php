<?php
	// stop scripts from auto-exiting
	define( 'ACCESS_OK', true );
	
    include_once( 'flexi/flexi.php' );
    
    $flexi = new Flexi();
	$flexi->loadConfig( 'config.php' );
	
	// start the website!
	try {
		$flexi->run( $_SERVER['REQUEST_URI'] );
	// display error message
	} catch ( Exception $ex ) { ?>
		<h1 id="error_title">Error!</h1>
		<h2 id="error_message"><?php echo $ex->getMessage(); ?></h2>
		<div id="error_stack_Trace" style="white-space: pre"><?php echo $ex->getTraceAsString(); ?></div>
	<?php }