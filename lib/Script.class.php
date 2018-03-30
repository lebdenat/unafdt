<?php
	
	if(! defined('SCRIPT_BASE_UNAFDT'))
	{
		if(! defined('SCRIPT_NOYAU_UNAFDT'))
		{
			include dirname(__FILE__)."/script/Noyau.class.php" ;
		}
		if(! defined('SCRIPT_REFERENCE_UNAFDT'))
		{
			include dirname(__FILE__)."/script/Reference.class.php" ;
		}
		if(! defined('SCRIPT_MEMBERSHIP_UNAFDT'))
		{
			include dirname(__FILE__)."/script/Membership.class.php" ;
		}
		if(! defined('SCRIPT_CIRCUIT_VALID_UNAFDT'))
		{
			include dirname(__FILE__)."/script/TacheValid.class.php" ;
		}
		if(! defined('SCRIPT_ETAPE_VALID_UNAFDT'))
		{
			include dirname(__FILE__)."/script/EtapeValid.class.php" ;
		}
		if(! defined('SCRIPT_STATS_UNAFDT'))
		{
			include dirname(__FILE__)."/script/Stats.class.php" ;
		}
		define('SCRIPT_BASE_UNAFDT', 1) ;
	}
	
?>