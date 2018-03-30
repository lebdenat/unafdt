<?php
	
	if(! defined('BD_UNAFDT'))
	{
		define('BD_UNAFDT', 1) ;
		
		class BdPrincUnafdt extends MysqlDB
		{
			public $AutoSetCharacterEncoding = 1 ;
			public $MustSetCharacterEncoding = 1 ;
			public $SetCharacterEncodingOnFetch = 1 ;
			public $CharacterEncoding = 'latin1' ;
			public function InitConnectionParams()
			{
				parent::InitConnectionParams() ;
				$this->ConnectionParams["server"] = HOTE_BD_UNAFDT ;
				$this->ConnectionParams["user"] = USER_BD_UNAFDT ;
				$this->ConnectionParams["password"] = PWD_BD_UNAFDT ;
				$this->ConnectionParams["schema"] = SCHEMA_BD_UNAFDT ;
			}
			public function DecodeRowValue($value)
			{
				if(! is_string($value))
				{
					return parent::DecodeRowValue($value) ;
				}
				return html_entity_decode(htmlentities($value, ENT_COMPAT, 'ISO-8859-1')) ;
			}
			public function EncodeParamValue($value)
			{
				if(! is_string($value))
				{
					return parent::EncodeParamValue($value) ;
				}
				return html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'ISO-8859-1') ;
			}
		}
	}
	
?>