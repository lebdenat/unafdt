<?php
	
	if(! defined('TYPE_DOC_UNAFDT'))
	{
		define('TYPE_DOC_UNAFDT', 1) ;
		
		class TypeActionBaseUnafdt extends ElementCircuitUnafdt
		{
			public function Titre()
			{
				return "Base" ;
			}
			public function RemplitFormTache(& $form)
			{
			}
			public function Execute()
			{
			}
		}
		
		class TypeActionIndefUnafdt extends TypeActionBaseUnafdt
		{
			public function Titre()
			{
				return "Indefini" ;
			}
		}
		
		class TypeActionValidProcessUnafdt extends TypeActionBaseUnafdt
		{
			protected $ValeurValid = 1 ;
			public function Titre()
			{
				return "Valider le document" ;
			}
			public function RemplitFormTache(& $form)
			{
			}
		}
		class TypeActionConfirmProcessUnafdt extends TypeActionValidProcessUnafdt
		{
			protected $ValeurValid = 1 ;
			public function Titre()
			{
				return "Confirmer le document" ;
			}
		}
		class TypeActionRejetProcessUnafdt extends TypeActionValidProcessUnafdt
		{
			protected $ValeurValid = 2 ;
			public function Titre()
			{
				return "Rejeter le document" ;
			}
		}
		
	}

?>