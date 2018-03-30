<?php
	
	if(! defined('CIRCUIT_UNAFDT'))
	{
		if(! defined('NOYAU_CIRCUIT_UNAFDT'))
		{
			include dirname(__FILE__)."/circuit/Noyau.class.php" ;
		}
		if(! defined('TYPE_ALERTE_UNAFDT'))
		{
			include dirname(__FILE__)."/circuit/TypeAlerte.class.php" ;
		}
		if(! defined('TYPE_DOC_UNAFDT'))
		{
			include dirname(__FILE__)."/circuit/TypeDoc.class.php" ;
		}
		if(! defined('TYPE_ACTEUR_UNAFDT'))
		{
			include dirname(__FILE__)."/circuit/TypeActeur.class.php" ;
		}
		if(! defined('TYPE_ACTION_UNAFDT'))
		{
			include dirname(__FILE__)."/circuit/TypeAction.class.php" ;
		}
		define('CIRCUIT_UNAFDT', 1) ;
	}
	
?>