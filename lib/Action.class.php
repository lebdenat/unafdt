<?php
	
	if(! defined('ACTION_WEB_UNAFDT'))
	{
		define('ACTION_WEB_UNAFDT', 1) ;
		
		class ActionAlerteEtapeValidUnafdt extends PvActionBaseZoneWebSimple
		{
			protected $IdEtape ;
			protected $LgnEtape ;
			protected $IdMembreConnecte ;
			protected $BdSupport ;
			protected function OuvreQueryPrinc($sql, $params=array())
			{
				$this->BdSupport = new BDPrincUnafdt() ;
				$this->BdSupport->AutoCloseConnection = false ;
				$this->QueryPrinc = $this->BdSupport->OpenQuery($sql, $params) ;
				register_shutdown_function(array(& $this, "FermeQueryPrinc"), array()) ;
				return is_resource($this->QueryPrinc) ;
			}
			protected function LitQueryPrinc()
			{
				return $this->BdSupport->ReadQuery($this->QueryPrinc) ;
			}
			public function FermeQueryPrinc()
			{
				if(is_resource($this->QueryPrinc))
				{
					$this->BdSupport->CloseQuery($this->QueryPrinc) ;
				}
			}
			public function Execute()
			{
				$this->IdEtape = _GET_def("idEtape") ;
				$this->IdMembreConnecte = _GET_def("idMembre") ;
				$bd = $this->ApplicationParent->BDPrinc ;
				$lgnEtape = $bd->FetchSqlRow("select t1.*, t2.type_acteur
from fluxtaf_etape_valid t1
inner join fluxtaf_tache_valid t2 on t1.id_tache_valid = t2.id
where t1.id_membre_creation = :idMembreConnecte and t1.statut_validation=0
and t1.id=:idEtape", array("idMembreConnecte" => $this->IdMembreConnecte, "idEtape" => $this->IdEtape)
				) ;
				if(! count($lgnEtape))
				{
					exit ;
				}
				$typeActeur = & $this->ApplicationParent->TypesActeur[$lgnEtape["type_acteur"]] ;
				$lgnsMembre = $bd->FetchSqlRows($typeActeur->SqlMembresNotif(), array("idMembreConnecte" => $this->IdMembreConnecte, "idTacheValid" => $lgnEtape["id_tache_valid"])) ;
				if(count($lgnsMembre) > 0)
				{
					foreach($lgnsMembre as $i => $lgnMembre)
					{
						// print_r($lgnMembre) ;
						foreach($this->ApplicationParent->TypesAlerte as $n => $typeAlerte)
						{
							$typeAlerte->NotifieEtapeAValider($lgnMembre, $lgnEtape) ;
						}
					}
				}
				exit ;
			}
		}
		
		class ActionAlerteEtapeTermineeUnafdt extends PvActionBaseZoneWebSimple
		{
			protected $IdEtape ;
			protected $LgnEtape ;
			protected $IdMembreConnecte ;
			public function Execute()
			{
				$this->IdEtape = _GET_def("idEtape") ;
				$this->IdMembreConnecte = _GET_def("idMembre") ;
				$bd = new BDPrincUnafdt() ;
				$lgnEtape = $bd->FetchSqlRow("select t1.*, t2.id_membre_creation id_membre_initiateur
from fluxtaf_etape_valid t1
inner join fluxtaf_etape_valid t2 on t1.id_etape_initiale = t2.id
where t1.id_membre_valid = :idMembreConnecte and t1.statut_validation <> 0
and t1.id=:idEtape", array("idMembreConnecte" => $this->IdMembreConnecte, "idEtape" => $this->IdEtape)
				) ;
				if(! count($lgnEtape))
				{
					exit ;
				}
				$lgnMembre = $bd->FetchSqlRow("select * from membership_member where id=:id", array("id" => $lgnEtape["id_membre_initiateur"])) ;
				foreach($this->ApplicationParent->TypesAlerte as $n => $typeAlerte)
				{
					$typeAlerte->NotifieEtapeValidee($lgnMembre, $lgnEtape) ;
				}
				echo "Envoyé !!!" ;
				exit ;
			}
		}
	}
	
?>