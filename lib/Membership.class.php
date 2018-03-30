<?php
	
	if(! defined('MEMBERSHIP_UNAFDT'))
	{
		if(! defined('BD_UNAFDT'))
		{
			include dirname(__FILE__)."/BD.class.php" ;
		}
		define('MEMBERSHIP_UNAFDT', 1) ;
		
		class MembershipUnafdt extends AkSqlMembership
		{
			public $PasswordMemberExpr = "PASSWORD" ;
			public $RootMemberId = "1" ;
			protected function InitConfig(& $parent)
			{
				parent::InitConfig($parent) ;
				$this->Database = new BDPrincUnafdt() ;
			}
		}
	}
	
?>