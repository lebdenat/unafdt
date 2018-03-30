<?php
	
	if(! defined('CONSTS_UNAFDT'))
	{
		define('CHEMIN_PVIEW_UNAFDT', '../../_PVIEW') ;
		define('CHEMIN_FIC_REL_ZONE_PRINC_UNAFDT', "Unafdt/admin.php") ;
		define('HOTE_BD_UNAFDT', "localhost") ;
		define('SCHEMA_BD_UNAFDT', "unafdt") ;
		define('USER_BD_UNAFDT', "root") ;
		define('PWD_BD_UNAFDT', "") ;
		define('HOTE_SMTP_UNAFDT', "mail.vite-bd.com") ;
		define('PORT_SMTP_UNAFDT', 25) ;
		define('COMPTE_MAIL_VALID_UNAFDT', "infos@vite-bd.com") ;
		define('PWD_MAIL_VALID_UNAFDT', "AlhProg123") ;
		define('SIGN_MAIL_VALID_UNAFDT', "<a href='http://www.vite-bd.com/Unafdt/'>Work Flow Configuration des offres</a>") ;
		define('URL_ENVOI_SMS_UNAFDT', 'http://10.242.68.103/UssdInGateway/ClientGW-SVA/ws/wsSms.php?numero=${numeroDest}&exp=${numeroSource}&message=${contenu}') ;
		define('SOA_ENVOI_SMS_UNAFDT', 'OCIWORKFLOW') ;
		define('CONSTS_UNAFDT', 1) ;
	}
	
?>