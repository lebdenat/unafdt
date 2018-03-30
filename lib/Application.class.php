<?php
	
	if(! defined('APPLICATION_UNAFDT'))
	{
		if(! defined('CONSTS_UNAFDT'))
		{
			include dirname(__FILE__)."/Consts.php" ;
		}
		if(! defined('COMMON_HTTP_SESSION_INCLUDED'))
		{
			include dirname(__FILE__)."/".CHEMIN_PVIEW_UNAFDT."/Common/HttpSession.class.php" ;
		}
		if(! defined('PV_BASE'))
		{
			include dirname(__FILE__)."/".CHEMIN_PVIEW_UNAFDT."/Pv/Base.class.php" ;
		}
		if(! defined('BASE_SWS'))
		{
			include dirname(__FILE__)."/".CHEMIN_PVIEW_UNAFDT."/Sws/Base.class.php" ;
		}
		if(! defined('PV_ZONE_SB_ADMIN2'))
		{
			include dirname(__FILE__)."/".CHEMIN_PVIEW_UNAFDT."/Pv/IHM/SbAdmin2.class.php" ;
		}
		if(! class_exists("PHPMailer"))
		{
			include dirname(__FILE__)."/".CHEMIN_PVIEW_UNAFDT."/misc/phpmailer/class.phpmailer.php" ;
		}
		if(! defined('BD_UNAFDT'))
		{
			include dirname(__FILE__)."/BD.class.php" ;
		}
		if(! defined('MEMBERSHIP_UNAFDT'))
		{
			include dirname(__FILE__)."/Membership.class.php" ;
		}
		if(! defined('CIRCUIT_UNAFDT'))
		{
			include dirname(__FILE__)."/Circuit.class.php" ;
		}
		if(! defined('ZONE_UNAFDT'))
		{
			include dirname(__FILE__)."/Zone.class.php" ;
		}
		
		class ApplicationUnafdt extends PvApplication
		{
			public $ZonePrinc ;
			public $BDPrinc ;
			public $TypesDoc = array() ;
			public $TypesActeur = array() ;
			public $TypesAlerte = array() ;
			public function ChargeConfig()
			{
				$this->ChargeTypesAlerte() ;
				$this->ChargeTypesDoc() ;
				$this->ChargeTypesActeur() ;
				parent::ChargeConfig() ;
			}
			public function & ObtientTypeActeur($nom)
			{
				$result = new TypeActeurIndefUnafdt() ;
				$result->AdopteApplication("indefini", $this) ;
				if(isset($this->TypesActeur[$nom]))
				{
					$result = & $this->TypesActeur[$nom] ;
				}
				return $result ;
			}
			public function & ObtientTypeDoc($nom)
			{
				$result = new TypeDocIndefUnafdt() ;
				$result->AdopteApplication("indefini", $this) ;
				if(isset($this->TypesDoc[$nom]))
				{
					$result = & $this->TypesDoc[$nom] ;
				}
				return $result ;
			}
			public function CreeFournTypesDoc()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$vals = array() ;
				foreach($this->TypesDoc as $n => $typeDoc)
				{
					$vals[] = array("nom" => $n, "titre" => $typeDoc->Titre()) ;
				}
				$fourn->Valeurs['TypesDoc'] = & $vals ;
				return $fourn ;
			}
			public function CreeFournTypesActeur()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$vals = array() ;
				foreach($this->TypesActeur as $n => $typeActeur)
				{
					$vals[] = array("nom" => $n, "titre" => $typeActeur->Titre()) ;
				}
				$fourn->Valeurs['TypesActeur'] = & $vals ;
				return $fourn ;
			}
			public function CreeFournStatutPubl()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$fourn->Valeurs["statutPubl"] = array(
					array("id" => "0", "titre" => "En cours"),
					array("id" => "1", "titre" => html_entity_decode("Confirm&eacute;")),
					array("id" => "2", "titre" => html_entity_decode("Rejet&eacute;")),
				) ;
				return $fourn ;
			}
			public function CreeFournStatutValid()
			{
				$fourn = new PvFournisseurDonneesDirect() ;
				$fourn->Valeurs["statutPubl"] = array(
					array("id" => "1", "titre" => "Confirmer"),
					array("id" => "2", "titre" => "Rejeter"),
				) ;
				return $fourn ;
			}
			protected function ChargeTypesAlerte()
			{
				$this->InsereTypeAlerte("mail1", new TypeAlerteSendMailUnafdt()) ;
				// $this->InsereTypeAlerte("mail1", new TypeAlerteMailShellUnafdt()) ;
				// $this->InsereTypeAlerte("sms1", new TypeAlerteWgetSmsUnafdt()) ;
			}
			protected function InsereTypeAlerte($nom, $alerte)
			{
				$this->TypesAlerte[$nom] = & $alerte ;
				$alerte->AdopteApplication($nom, $this) ;
			}
			protected function ChargeTypesDoc()
			{
				$this->InsereTypeDoc("piece_jointe", new TypeDocPieceJointeUnafdt()) ;
				$this->InsereTypeDoc("sortie_caisse", new TypeDocSortieCaisseUnafdt()) ;
				$this->InsereTypeDoc("demande_absence", new TypeDocDemandeAbsenceUnafdt()) ;
				$this->InsereTypeDoc("demande_conge", new TypeDocDemandeCongeUnafdt()) ;
			}
			protected function ChargeTypesActeur()
			{
				$this->InsereTypeActeur("tous", new TypeActeurTousUnafdt()) ;
				$this->InsereTypeActeur("autres", new TypeActeurAutresUnafdt()) ;
				$this->InsereTypeActeur("membre", new TypeActeurMembreUnafdt()) ;
				$this->InsereTypeActeur("profil", new TypeActeurProfilUnafdt()) ;
				// $this->InsereTypeActeur("role", new TypeActeurRoleUnafdt()) ;
			}
			protected function InsereTypeDoc($nom, $typeDoc)
			{
				$this->TypesDoc[$nom] = & $typeDoc ;
				$typeDoc->AdopteApplication($nom, $this) ;
				return $typeDoc ;
			}
			protected function InsereTypeActeur($nom, $typeActeur)
			{
				$this->TypesActeur[$nom] = & $typeActeur ;
				$typeActeur->AdopteApplication($nom, $this) ;
				return $typeActeur ;
			}
			public function ExprSqlProcessValidsDispos(& $script)
			{
				$sql = '' ;
				$i = 0 ;
				$params = array() ;
				foreach($this->TypesActeur as $n => $typeActeur)
				{
					$cond = $typeActeur->CondSqlAccesForm() ;
					$tabl = $typeActeur->TablSqlAccesForm() ;
					$membership = & $script->ZoneParent->Membership ;
					$nomParam = "TypeActeur".ucfirst($n) ;
					$sqlTemp = "select t1.id, t1.titre, mb.".$membership->LoginMemberColumn." login_creation
from fluxtaf_tache_valid t1".PHP_EOL ;
					if($tabl != '')
					{
						$sqlTemp .= $tabl.PHP_EOL ;
					}
					$sqlTemp .= "left join ".$membership->Database->EscapeTableName($membership->MemberTable)." mb on t1.id_membre_creation = mb.".$membership->IdMemberColumn. PHP_EOL ;
					$sqlTemp .= "where t1.type_acteur = :".$nomParam." and t1.est_tache_initiale=1".PHP_EOL ;
					if($cond != "")
					{
						$sqlTemp .= " and (".$cond.")".PHP_EOL ;
					}
					if($sql != '')
					{
						$sql .= ' union '.PHP_EOL ;
					}
					$sql .= $sqlTemp ;
					$params = array_merge($params, $typeActeur->ParamsSqlAccesForm($script)) ;
					$params[$nomParam] = $n ;
				}
				$expr = new PvExpressionFiltre() ;
				$expr->Texte = $sql ;
				$expr->Parametres = $params ;
				// print_r($expr) ;
				return $expr ;
			}
			public function ExprSqlProcessValidsVerifs(& $script)
			{
				$sql = '' ;
				$i = 0 ;
				$params = array() ;
				foreach($this->TypesActeur as $n => $typeActeur)
				{
					$cond = $typeActeur->CondSqlAccesEtape() ;
					$tabl = $typeActeur->TablSqlAccesEtape() ;
					$nomParam = "TypeActeur".ucfirst($n) ;
					$sqlTemp = "select t0.id, t0.date_creation, t0.titre, t1.id id_tache_valid, t1.titre titre_tache_valid, mb.login_member login_membre_creation
from fluxtaf_etape_valid t0
inner join fluxtaf_etape_valid etp on t0.id_etape_initiale = etp.id
inner join membership_member mb on mb.id = etp.id_membre_creation
inner join fluxtaf_tache_valid t1 on t0.id_tache_valid = t1.id".PHP_EOL ;
					if($tabl != '')
					{
						$sqlTemp .= $tabl.PHP_EOL ;
					}
					$sqlTemp .= "where t1.type_acteur = :".$nomParam." and t0.statut_validation=0".PHP_EOL ;
					if($cond != "")
					{
						$sqlTemp .= " and (".$cond.")".PHP_EOL ;
					}
					if($sql != '')
					{
						$sql .= ' union '.PHP_EOL ;
					}
					$sql .= $sqlTemp ;
					$params = array_merge($params, $typeActeur->ParamsSqlAccesEtape($script)) ;
					$params[$nomParam] = $n ;
				}
				$expr = new PvExpressionFiltre() ;
				$expr->Texte = $sql ;
				$expr->Parametres = $params ;
				return $expr ;
			}
			public function CreeBDPrinc()
			{
				return new BDPrincUnafdt() ;
			}
			public function CreeFournBDPrinc()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->BDPrinc ;
				return $fourn ;
			}
			protected function ChargeBasesDonnees()
			{
				$this->BDPrinc = $this->InsereBaseDonnees("bdPrinc", $this->CreeBDPrinc()) ;
			}
			protected function ChargeIHMs()
			{
				$this->ZonePrinc = $this->InsereIHM("zonePrinc", new ZonePrincUnafdt()) ;
			}
		}
	}
	
?>