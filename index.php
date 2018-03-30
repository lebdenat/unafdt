<?php
	
	if(! defined('APPLICATION_UNAFDT'))
	{
		include dirname(__FILE__)."/lib/Application.class.php" ;
	}
	
	$app = new ApplicationUnafdt() ;
	$app->Execute() ;
	
?>