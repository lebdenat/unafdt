<?php
	
	if(! defined('NOYAU_CIRCUIT_UNAFDT'))
	{
		define('NOYAU_CIRCUIT_UNAFDT', 1) ;
		
		class ElementCircuitUnafdt extends PvElementApplication
		{
			public function BDPrinc()
			{
				return $this->ApplicationParent->BDPrinc ;
			}
			public function CreeBDPrinc()
			{
				return new BDPrincUnafdt() ;
			}
			protected function CreeFournBDPrinc()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->ApplicationParent->BDPrinc ;
				return $fourn ;
			}
		}
	}
	
?>