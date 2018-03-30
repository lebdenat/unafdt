<?php
	
	if(! defined('TYPE_ACTEUR_UNAFDT'))
	{
		define('TYPE_ACTEUR_UNAFDT', 1) ;
		
		class TypeActeurBaseUnafdt extends ElementCircuitUnafdt
		{
			public function Titre()
			{
				return "Base" ;
			}
			public function SqlTitreValid()
			{
				return 'select \'Aucun\' titre' ;
			}
			public function ParamsTitreValid($lgnTache)
			{
				return array() ;
			}
			public function RemplitFormTache(& $form)
			{
			}
			public function ExprSqlAccesEtape(& $script, $idTache)
			{
				$cond = $this->CondSqlAccesEtape() ;
				$tabl = $this->TablSqlAccesEtape() ;
				$nomParam = "TypeActeur".ucfirst($this->NomElementApplication) ;
				$sqlTemp = "select t1.id, t1.titre
from fluxtaf_tache_valid t1".PHP_EOL ;
				if($tabl != '')
				{
					$sqlTemp .= $tabl.PHP_EOL ;
				}
				$sqlTemp .= "where t1.type_acteur = :".$nomParam." and t1.id=:idTache".PHP_EOL ;
				if($cond != "")
				{
					$sqlTemp .= " and (".$cond.")".PHP_EOL ;
				}
				$params = $this->ParamsSqlAccesEtape($script) ;
				$params[$nomParam] = $this->NomElementApplication ;
				$expr = new PvExpressionFiltre() ;
				$expr->Texte = $sqlTemp ;
				$expr->Parametres = array_merge($params, array("idTache" => $idTache)) ;
				return $expr ;
			}
			public function ExprSqlAccesTache(& $script, $idTache)
			{
				$cond = $this->CondSqlAccesTache() ;
				$tabl = $this->TablSqlAccesTache() ;
				$nomParam = "TypeActeur".ucfirst($this->NomElementApplication) ;
				$sqlTemp = "select t1.id, t1.titre
from fluxtaf_tache_valid t1".PHP_EOL ;
				if($tabl != '')
				{
					$sqlTemp .= $tabl.PHP_EOL ;
				}
				$sqlTemp .= "where t1.type_acteur = :".$nomParam." and t1.id=:idTache".PHP_EOL ;
				if($cond != "")
				{
					$sqlTemp .= " and (".$cond.")".PHP_EOL ;
				}
				$params = $this->ParamsSqlAccesTache($script) ;
				$params[$nomParam] = $this->NomElementApplication ;
				$expr = new PvExpressionFiltre() ;
				$expr->Texte = $sqlTemp ;
				$expr->Parametres = array_merge($params, array("idTache" => $idTache)) ;
				return $expr ;
			}
			public function ExprSqlAccesForm(& $script, $idTache)
			{
				$cond = $this->CondSqlAccesForm() ;
				$tabl = $this->TablSqlAccesForm() ;
				$nomParam = "TypeActeur".ucfirst($this->NomElementApplication) ;
				$sqlTemp = "select t1.id, t1.titre
from fluxtaf_tache_valid t1".PHP_EOL ;
				if($tabl != '')
				{
					$sqlTemp .= $tabl.PHP_EOL ;
				}
				$sqlTemp .= "where t1.type_acteur = :".$nomParam." and t1.id=:idTache".PHP_EOL ;
				if($cond != "")
				{
					$sqlTemp .= " and (".$cond.")".PHP_EOL ;
				}
				$params = $this->ParamsSqlAccesForm($script) ;
				$params[$nomParam] = $this->NomElementApplication ;
				$expr = new PvExpressionFiltre() ;
				$expr->Texte = $sqlTemp ;
				$expr->Parametres = array_merge($params, array("idTache" => $idTache)) ;
				return $expr ;
			}
			public function TablSqlAccesEtape()
			{
				return "" ;
			}
			public function CondSqlAccesEtape()
			{
				return "1 = 0" ;
			}
			public function ParamsSqlAccesEtape(& $script)
			{
				return array() ;
			}
			public function TablSqlAccesTache()
			{
				return "" ;
			}
			public function CondSqlAccesTache()
			{
				return "1 = 0" ;
			}
			public function ParamsSqlAccesTache(& $script)
			{
				return array() ;
			}
			public function TablSqlAccesForm()
			{
				return $this->TablSqlAccesTache() ;
			}
			public function CondSqlAccesForm()
			{
				return $this->CondSqlAccesTache() ;
			}
			public function ParamsSqlAccesForm(& $script)
			{
				return $this->ParamsSqlAccesTache($script) ;
			}
			public function SqlMembresNotif()
			{
				return 'select t1.* from membership_member t1 where 1=0' ;
			}
			public function IdProfilConnecte(& $script)
			{
				$idProfil = 0 ;
				if($script->ZoneParent->PossedeMembreConnecte())
				{
					$idProfil = $script->ZoneParent->Membership->MemberLogged->Profile->Id ;
				}
				return $idProfil ;
			}
		}
		
		class TypeActeurIndefUnafdt extends TypeActeurBaseUnafdt
		{
			public function Titre()
			{
				return "Indefini" ;
			}
			public function SqlTitreValid()
			{
				return 'select \'Indefini\' titre' ;
			}
			public function ParamsTitreValid($lgnTache)
			{
				return array() ;
			}
			public function TablSqlAccesEtape()
			{
				return "" ;
			}
			public function CondSqlAccesEtape()
			{
				return "1 = 0" ;
			}
		}
		
		class TypeActeurTousUnafdt extends TypeActeurBaseUnafdt
		{
			public function Titre()
			{
				return "Tout le monde" ;
			}
			public function SqlTitreValid()
			{
				return 'select \'Tout le monde\'' ;
			}
			public function ParamsTitreValid($lgnTache)
			{
				return array() ;
			}
			public function CondSqlAccesEtape()
			{
				return "1 = 1" ;
			}
			public function CondSqlAccesTache()
			{
				return "1 = 1" ;
			}
			public function ParamsSqlAccesEtape(& $script)
			{
				return array() ;
			}
			public function SqlMembresNotif()
			{
				return 'select t1.* from membership_member t1 where 1=1' ;
			}
		}
		class TypeActeurAutresUnafdt extends TypeActeurBaseUnafdt
		{
			public function Titre()
			{
				return "Autres" ;
			}
			public function SqlTitreValid()
			{
				return 'select concat(\'Tout le monde sauf \', login_member) titre from membership_member where id <> :idMembre' ;
			}
			public function ParamsTitreValid($lgnTache)
			{
				return array('idMembre' => $lgnTache["param1_acteur"]) ;
			}
			public function TablSqlAccesEtape()
			{
				return "" ;
			}
			public function CondSqlAccesEtape()
			{
				return "etp.id_membre_creation <> :idMembreConnecte" ;
			}
			public function ParamsSqlAccesEtape(& $script)
			{
				return array("idMembreConnecte" => $script->ZoneParent->IdMembreConnecte()) ;
			}
			public function TablSqlAccesTache()
			{
				return "inner join fluxtaf_etape_valid t2 on t2.id_tache_valid=t1.id
inner join fluxtaf_etape_valid etp on t2.id_etape_initiale=etp.id" ;
			}
			public function CondSqlAccesTache()
			{
				return "etp.id_membre_creation <> :idMembreConnecte" ;
			}
			public function ParamsSqlAccesTache(& $script)
			{
				return array("idMembreConnecte" => $script->ZoneParent->IdMembreConnecte()) ;
			}
			public function TablSqlAccesForm()
			{
				return "" ;
			}
			public function CondSqlAccesForm()
			{
				return "id_membre_creation <> :idMembreConnecte" ;
			}
			public function ParamsSqlAccesForm(& $script)
			{
				return $this->ParamsSqlAccesTache($script) ;
			}
			public function SqlMembresNotif()
			{
				return 'select t1.*
from membership_member t1
where t1.id <> :idMembreConnecte' ;
			}
		}
		class TypeActeurProfilUnafdt extends TypeActeurBaseUnafdt
		{
			public function Titre()
			{
				return "Profil" ;
			}
			public function SqlTitreValid()
			{
				return 'select concat(\'Un membre de profil \', title) titre from membership_profile where id=:idProfil' ;
			}
			public function ParamsTitreValid($lgnTache)
			{
				return array("idProfil" => $lgnTache["param1_acteur"]) ;
			}
			public function RemplitFormTache(& $form)
			{
				$membership = & $form->ZoneParent->Membership ;
				$this->FltParam1 = $form->InsereFltEditHttpPost('param1_acteur', 'param1_acteur') ;
				$this->FltParam1->Libelle = "Titre du profil" ;
				$this->CompParam1 = $this->FltParam1->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompParam1->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompParam1->FournisseurDonnees->RequeteSelection = '(select distinct t1.* from '.$membership->ProfileTable.' t1 inner join '.$membership->PrivilegeTable.' t2 on t1.id = t2.profile_id inner join '.$membership->RoleTable.' t3 on t2.role_id = t3.id where (t3.name=\'validation_formulaire\' and t2.active=1) or t1.id = 1)' ;
				$this->CompParam1->NomColonneValeur = $membership->IdProfileColumn ;
				$this->CompParam1->NomColonneLibelle = $membership->TitleProfileColumn ;
			}
			public function TablSqlAccesEtape()
			{
				return "inner join membership_profile t2 on t1.param1_acteur = t2.id" ;
			}
			public function CondSqlAccesEtape()
			{
				return "t2.id = :idProfil" ;
			}
			public function ParamsSqlAccesEtape(& $script)
			{
				return array("idProfil" => $this->IdProfilConnecte($script)) ;
			}
			public function TablSqlAccesTache()
			{
				return "inner join membership_profile t2 on t1.param1_acteur = t2.id" ;
			}
			public function CondSqlAccesTache()
			{
				return "t2.id = :idProfil" ;
			}
			public function ParamsSqlAccesTache(& $script)
			{
				return array("idProfil" => $this->IdProfilConnecte($script)) ;
			}
			public function SqlMembresNotif()
			{
				return 'select t2.*
from fluxtaf_tache_valid t0
inner join membership_profile t1 on t1.id = t0.param1_acteur
inner join membership_member t2 on t1.id = t2.profile_id
where :idMembreConnecte = :idMembreConnecte and t0.id = :idTacheValid' ;
			}
		}
		class TypeActeurMembreUnafdt extends TypeActeurBaseUnafdt
		{
			public function Titre()
			{
				return "Membre" ;
			}
			public function SqlTitreValid()
			{
				return 'select concat(\'Le membre \', login_member) titre from membership_member where id = :idMembre' ;
			}
			public function ParamsTitreValid($lgnTache)
			{
				return array('idMembre' => $lgnTache["param1_acteur"]) ;
			}
			public function RemplitFormTache(& $form)
			{
				$membership = & $form->ZoneParent->Membership ;
				$this->FltParam1 = $form->InsereFltEditHttpPost('param1_acteur', 'param1_acteur') ;
				$this->FltParam1->Libelle = "Login du membre" ;
				$this->CompParam1 = $this->FltParam1->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompParam1->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompParam1->FournisseurDonnees->RequeteSelection = $membership->MemberTable ;
				$this->CompParam1->NomColonneValeur = $membership->IdMemberColumn ;
				$this->CompParam1->NomColonneLibelle = $membership->LoginMemberColumn ;
			}
			public function TablSqlAccesEtape()
			{
				return "inner join membership_member t2 on t1.param1_acteur = t2.id" ;
			}
			public function CondSqlAccesEtape()
			{
				return "t2.id = :idMembreConnecte" ;
			}
			public function ParamsSqlAccesEtape(& $script)
			{
				return array("idMembreConnecte" => $script->ZoneParent->IdMembreConnecte()) ;
			}
			public function TablSqlAccesTache()
			{
				return "inner join membership_member t2 on t1.param1_acteur = t2.id" ;
			}
			public function CondSqlAccesTache()
			{
				return "t2.id = :idMembreConnecte" ;
			}
			public function ParamsSqlAccesTache(& $script)
			{
				return array("idMembreConnecte" => $script->ZoneParent->IdMembreConnecte()) ;
			}
			public function SqlMembresNotif()
			{
				return 'select distinct t1.*
from fluxtaf_tache_valid t0
inner join membership_member t1 on t1.id = t0.param1_acteur
where :idMembreConnecte = :idMembreConnecte and t0.id = :idTacheValid' ;
			}
		}
		class TypeActeurRoleUnafdt extends TypeActeurBaseUnafdt
		{
			public function Titre()
			{
				return html_entity_decode("R&ocirc;le") ;
			}
			public function RemplitFormTache(& $form)
			{
				$membership = & $form->ZoneParent->Membership ;
				$this->FltParam1 = $form->InsereFltEditHttpPost('param1_acteur', 'param1_acteur') ;
				$this->FltParam1->Libelle = "Titre du r&ocirc;le" ;
				$this->CompParam1 = $this->FltParam1->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompParam1->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompParam1->FournisseurDonnees->RequeteSelection = $membership->RoleTable ;
				$this->CompParam1->NomColonneValeur = $membership->IdRoleColumn ;
				$this->CompParam1->NomColonneLibelle = $membership->TitleRoleColumn ;
			}
			public function TablSqlAccesEtape()
			{
				return "inner join membership_role t2 on t1.param1_acteur = t2.id
inner join membership_privilege t3 on t2.id = t3.role_id" ;
			}
			public function CondSqlAccesEtape()
			{
				return "t3.profile_id = :idProfil and t3.active=1" ;
			}
			public function ParamsSqlAccesEtape(& $script)
			{
				return array("idProfil" => $this->IdProfilConnecte($script)) ;
			}
			public function TablSqlAccesTache()
			{
				return "inner join membership_role t2 on t1.param1_acteur = t2.id
inner join membership_privilege t3 on t2.id = t3.role_id" ;
			}
			public function CondSqlAccesTache()
			{
				return "t3.profile_id = :idProfil and t3.active=1" ;
			}
			public function ParamsSqlAccesTache(& $script)
			{
				return array("idProfil" => $this->IdProfilConnecte($script)) ;
			}
			public function SqlMembresNotif()
			{
				return 'select t4.*
from fluxtaf_tache_valid t0
inner join membership_role t1 on t1.id = t0.param1_acteur
inner join membership_privilege t2 on t1.id = t2.role_id
inner join membership_profile t3 on t3.id = t2.profile_id
inner join membership_member t4 on t4.id = t4.profile_id
where :idMembreConnecte = :idMembreConnecte and t0.id = :idTacheValid' ;
			}
		}
		
	}

?>