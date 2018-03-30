<?php
	
	if(! defined("SCRIPT_ETAPE_VALID_UNAFDT"))
	{
		define("SCRIPT_ETAPE_VALID_UNAFDT", 1) ;
		
		class ScriptChoixProcessValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Liste des Formulaires" ;
			public $Titre = "Liste des Formulaires" ;
			public $Privileges = array("poster_formulaire") ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			protected function ChargeTablPrinc()
			{
				parent::ChargeTablPrinc() ;
				$expr = $this->ApplicationParent->ExprSqlProcessValidsDispos($this) ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = '('.$expr->Texte.')' ;
				$this->TablPrinc->FournisseurDonnees->ParamsSelection = $expr->Parametres ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColTitre = $this->TablPrinc->InsereDefCol("titre", "Formulaire") ;
				// $this->DefColTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->DefColLoginCrea = $this->TablPrinc->InsereDefCol("login_creation", "Auteur") ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienCree = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=creeProcessValid&id_tache=${id}', "<i class='fa fa-plus fa-fw' title='Cr&eacute;er'></i>") ;
			}
		}
	
		class ScriptCreeProcessValidUnafdt extends ScriptEditBaseUnafdt
		{
			public $TitreBase = "Cr&eacute;er un Formulaire" ;
			public $PourAjout = 1 ;
			public $Privileges = array("poster_formulaire") ;
			public $NomScriptExecSucces = "suiviProcessValid" ;
			public $IdTacheValid ;
			public $LgnTacheValid ;
			public $TypeActeurSelect ;
			public $TypeDocSelect ;
			public $UrlRedirectAnnulFormPrinc = "?appelleScript=choixProcessValid" ;
			public $NomClasseCmdExecFormPrinc = "CmdCreeProcessValidUnafdt" ;
			protected function InitConfig()
			{
				parent::InitConfig() ;
				$this->TypeActeurSelect = new TypeActeurIndefUnafdt() ;
				$this->TypeDocSelect = new TypeDocIndefUnafdt() ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
			}
			protected function DetecteLgnTacheValid()
			{
				$bd = $this->BDPrinc() ;
				$this->IdTacheValid = _GET_def("id_tache") ;
				$this->LgnTacheValid = $bd->FetchSqlRow("select * from fluxtaf_tache_valid where id = :id", array("id" => $this->IdTacheValid)) ;
				if($this->LgnTacheValidTrouvee())
				{
					$this->Titre = $this->TitreBase." - ".htmlentities($this->LgnTacheValid["titre"]) ;
					$this->TitreDocument = $this->TitreBase." - ".htmlentities($this->LgnTacheValid["titre"]) ;
					$nomTypeActeur = $this->LgnTacheValid["type_acteur"] ;
					$typeActeur = $this->ApplicationParent->TypesActeur[$nomTypeActeur] ;
					$expr = $typeActeur->ExprSqlAccesTache($this, $this->LgnTacheValid["id"]) ;
					$lgn = $bd->FetchSqlRow($expr->Texte, $expr->Parametres) ;
					if(is_array($lgn) && count($lgn) > 0)
					{
						$this->TypeActeurSelect = & $typeActeur ;
						$this->TypeDocSelect = $this->ApplicationParent->TypesDoc[$this->LgnTacheValid["type_doc"]] ;
					}
					else
					{
						$this->LgnTacheValid = null ;
					}
				}
			}
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if($ok == false)
				{
					return $ok ;
				}
				$this->DetecteLgnTacheValid() ;
				return $this->LgnTacheValidTrouvee() ;
			}
			protected function LgnTacheValidTrouvee()
			{
				return (is_array($this->LgnTacheValid) && count($this->LgnTacheValid) > 0) ;
			}
			protected function ChargeFormPrinc()
			{
				$this->FltIdTache = $this->FormPrinc->InsereFltEditFixe("idTache", $this->IdTacheValid, "id_tache_valid") ;
				$this->FltIdCtrl = $this->FormPrinc->InsereFltEditFixe("idCtrl", uniqid(), "id_ctrl") ;
				$this->FltIdMembre = $this->FormPrinc->InsereFltEditFixe("idMembreCrea", $this->ZoneParent->IdMembreConnecte(), "id_membre_creation") ;
				$this->FltIdMembre = $this->FormPrinc->InsereFltEditFixe("idMembreValid", $this->ZoneParent->IdMembreConnecte(), "id_membre_valid") ;
				$this->FltStatutPubl = $this->FormPrinc->InsereFltEditFixe("statutPubl", 1, "statut_validation") ;
				$this->FltEtapeInit = $this->FormPrinc->InsereFltEditFixe("etapeInitiale", 0, "id_etape_initiale") ;
				$this->FltDateValid = $this->FormPrinc->InsereFltEditFixe("dateValid", date("Y-m-d H:i:s"), "date_valid") ;
				if($this->TypeDocSelect->InscrireTitre == 1)
				{
					$this->FltTitre = $this->FormPrinc->InsereFltEditHttpPost("titre", "titre") ;
					$this->FltTitre->Libelle = "Titre" ;
					$this->FltTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
					$this->CritrSpec = $this->FormPrinc->CommandeExecuter->InsereCritereNonVide(array("titre")) ;
				}
				$this->TypeDocSelect->RemplitFormEtape($this->FormPrinc) ;
				$this->FormPrinc->DessinateurFiltresEdition = new DessinFormEtapeValidUnafdt() ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="panel panel-default">
<div class="panel-heading"><h4>Type : '.$this->TypeDocSelect->Titre().'</h4></div>
<div class="panel-body">'.PHP_EOL ;
				$ctn .= parent::RenduSpecifique().PHP_EOL ;
				$ctn .= '</div>
</div>' ;
				return $ctn ;
			}
		}
	
		class ScriptSuiviProcessValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $Titre = "Suivi des demandes" ;
			public $Privileges = array("poster_formulaire", "consultation_formulaire") ;
			public $TitreDocument = "Suivi de vos requ&ecirc;tes" ;
			protected $ReqSelectTablPrinc = "(SELECT t2.*, t1.id id_etape, t3.id id_etape_valid, 
t2.date_creation date_affectation, t3.titre titre_tache,
t4.login_member login_membre_creation,
case when t1.statut_validation = 0 then 'En cours' when t1.statut_validation = 1 then 'Confirme' else 'Rejete' end titre_validation, case when t1.statut_validation = 0 then null else t1.date_valid end date_validation
FROM fluxtaf_etape_valid t2
inner join fluxtaf_etape_valid t1 on t1.id_etape_initiale = t2.id
left join fluxtaf_tache_valid t3 on t2.id_tache_valid = t3.id
left join membership_member t4 on t2.id_membre_creation = t4.id
WHERE t2.id_etape_initiale = 0
and (t1.statut_validation = 0 or t1.est_etape_finale=1))" ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			protected function ChargeTablPrinc()
			{
				if(! $this->ZoneParent->PossedePrivilege("consultation_formulaire"))
				{
					$this->TablPrinc->InsereFltSelectFixe("idMembre", $this->ZoneParent->IdMembreConnecte(), "id_membre_creation = <self>") ;
				}
				else
				{
					$this->FltLogin = $this->TablPrinc->InsereFltSelectHttpGet("loginMembre", "login_membre_creation like concat('%', <self>, '%')") ;
					$this->FltLogin->Libelle = "Initi&eacute; par :" ;
				}
				$this->TablPrinc->AccepterTriColonneInvisible = 1 ;
				$this->TablPrinc->SensColonneTri = "desc" ;
				$this->TablPrinc->InsereDefColCachee("date_affectation") ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->TablPrinc->InsereDefColCachee("id_etape") ;
				$this->TablPrinc->InsereDefColCachee("id_tache_valid") ;
				if($this->ZoneParent->PossedePrivilege("consultation_formulaire"))
				{
					$this->TablPrinc->InsereDefCol("login_membre_creation", "Initi&eacute; par") ;
				}
				$this->DefColTitre = $this->TablPrinc->InsereDefCol("titre", "Requ&ecirc;te") ;
				$this->DefColTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->TablPrinc->InsereDefColDateTimeFr("date_creation", "Date cr&eacute;ation") ;
				$this->TablPrinc->InsereDefCol("titre_tache", "Formulaire") ;
				$this->TablPrinc->InsereDefCol("titre_validation", "Statut") ;
				$this->TablPrinc->InsereDefColDateTimeFr("date_validation", "Date validation") ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienDetails = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, "?appelleScript=detailEtapeValid&id=\${id_etape}", "<i class='fa fa-list fa-fw'></i>") ;
			}
		}
		
		class ScriptDetailEtapeValidUnafdt extends ScriptEditBaseUnafdt
		{
			public $IdTacheValid ;
			public $LgnTacheValid ;
			public $TypeActeurSelect ;
			public $TypeDocSelect ;
			public $PourAjout = 0 ;
			public $IdEtapeInitiale = 0 ;
			public $Titre = "D&eacute;tails etape" ;
			public $TitreDocument = "D&eacute;tails etape" ;
			public $Privileges = array("poster_formulaire", "validation_formulaire", "consultation_formulaire") ;
			public $FormPrincEditable = 0 ;
			public $UrlRedirectAnnulFormPrinc = "?appelleScript=suiviProcessValid" ;
			public $InscrireCmdExecFormPrinc = 0 ;
			public $InitiateurConnecte = 0 ;
			public $ValidateurConnecte = 0 ;
			public $ConsulteurConnecte = 0 ;
			public $ReqSelectFormPrinc = "(select t1.*, t2.type_acteur, t2.param1_acteur, t2.param2_acteur, t2.param3_acteur, t2.param4_acteur, t2.type_doc, t3.id_membre_creation id_membre_initiateur, t4.login_member login_initiateur, t5.login_member login_validateur
from fluxtaf_etape_valid t1
inner join fluxtaf_tache_valid t2 on t1.id_tache_valid = t2.id
left join fluxtaf_etape_valid t3 on t1.id_etape_initiale = t3.id
inner join membership_member t4 on t3.id_membre_creation = t4.id
left join membership_member t5 on t1.id_membre_valid = t5.id)" ;
			public $TablEditFormPrinc = "fluxtaf_etape_valid" ;
			// public $NomClasseCmdExecFormPrinc = "CmdCreeProcessValidUnafdt" ;
			protected function InitFormPrinc()
			{
				if($this->ZoneParent->PossedePrivilege("validation_formulaire"))
				{
					$this->UrlRedirectAnnulFormPrinc = "?appelleScript=avisEtapesValid" ;
				}
				parent::InitFormPrinc() ;
			}
			protected function DetecteLgnTacheValid()
			{
				$bd = $this->BDPrinc() ;
				$this->IdEtapeValid = _GET_def("id") ;
				$this->LgnTacheValid = $bd->FetchSqlRow("select t1.id_etape_initiale, t1.id_membre_valid, t1.statut_validation, t2.*, t3.id_membre_creation id_membre_initiateur
from fluxtaf_etape_valid t1
left join fluxtaf_etape_valid t3 on t1.id_etape_initiale = t3.id
left join fluxtaf_tache_valid t2 on t1.id_tache_valid = t2.id
where t1.id = :id", array("id" => $this->IdEtapeValid)) ;
				// print_r($this->LgnTacheValid) ;
				if($this->LgnTacheValidTrouvee())
				{
					$this->InitiateurConnecte = ($this->LgnTacheValid["id_membre_initiateur"] == $this->ZoneParent->IdMembreConnecte()) ? 1 : 0 ;
					$this->ValidateurConnecte = ($this->LgnTacheValid["id_membre_valid"] == $this->ZoneParent->IdMembreConnecte()) ? 1 : 0 ;
					$this->ConsulteurConnecte = ($this->ZoneParent->PossedePrivilege("consultation_formulaire")) ? 1 : 0 ;
					$this->IdEtapeInitiale = $this->LgnTacheValid["id_etape_initiale"] ;
					$nomTypeActeur = $this->LgnTacheValid["type_acteur"] ;
					$typeActeur = $this->ApplicationParent->TypesActeur[$nomTypeActeur] ;
					if($this->InitiateurConnecte == 0 && $this->ValidateurConnecte == 0 && $this->ConsulteurConnecte == 0 && $lgn["statut_validation"] == 0)
					{
						$expr = $typeActeur->ExprSqlAccesTache($this, $this->LgnTacheValid["id"]) ;
						$lgn = $bd->FetchSqlRow($expr->Texte, $expr->Parametres) ;
						if(is_array($lgn) && count($lgn) > 0)
						{
							$this->TypeActeurSelect = & $typeActeur ;
							$this->TypeDocSelect = $this->ApplicationParent->TypesDoc[$this->LgnTacheValid["type_doc"]] ;
						}
						else
						{
							$this->LgnTacheValid = null ;
						}
					}
					elseif($lgn["statut_validation"] != 0)
					{
						$this->LgnTacheValid = null ;
					}
					else
					{
						$this->TypeActeurSelect = & $typeActeur ;
						$this->TypeDocSelect = $this->ApplicationParent->TypesDoc[$this->LgnTacheValid["type_doc"]] ;
					}
				}
			}
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if($ok == false)
				{
					return $ok ;
				}
				$this->DetecteLgnTacheValid() ;
				return ($this->LgnTacheValidTrouvee() || $this->InitiateurConnecte || $this->ValidateurConnecte) ;
			}
			protected function LgnTacheValidTrouvee()
			{
				return (is_array($this->LgnTacheValid) && count($this->LgnTacheValid) > 0) ;
			}
			protected function ChargeFormPrinc()
			{
				$this->FltId = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				if(! $this->ZoneParent->PossedePrivilege("consultation_formulaire"))
				{
					$this->FltIdMembre = $this->FormPrinc->InsereFltLgSelectFixe("idMembre", $this->ZoneParent->IdMembreConnecte(), "(id_membre_initiateur = <self> or id_membre_valid = <self>)") ;
					$this->FltIdMembre->Obligatoire = 1 ;
				}
				$this->FltIdAff = $this->FormPrinc->InsereFltEditHttpPost("idAff", "id") ;
				$this->FltIdAff->Libelle = "ID" ;
				$this->FltStatutPubl = $this->FormPrinc->InsereFltEditHttpPost("statutPubl", "statut_validation") ;
				$this->FltStatutPubl->Libelle = "Statut" ;
				$this->CompStatutPubl = $this->FltStatutPubl->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompStatutPubl->FournisseurDonnees = $this->ApplicationParent->CreeFournStatutPubl() ;
				$this->CompStatutPubl->NomColonneValeur = "id" ;
				$this->CompStatutPubl->NomColonneLibelle = "titre" ;
				if($this->TypeDocSelect->InscrireTitre == 1)
				{
					$this->FltTitre = $this->FormPrinc->InsereFltEditHttpPost("titre", "titre") ;
					$this->FltTitre->Libelle = "Titre" ;
					$this->FltTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				}
				$this->FltMembreCrea = $this->FormPrinc->InsereFltEditHttpPost("login_initiateur", "login_initiateur") ;
				$this->FltMembreCrea->NePasLierColonne = 1 ;
				$this->FltMembreCrea->Libelle = "Initi&eacute; par" ;
				$this->FltTypeDoc = $this->FormPrinc->InsereFltEditHttpPost("type_doc", "type_doc") ;
				$this->FltTypeDoc->Libelle = "Document support" ;
				$this->CompTypeDoc = $this->FltTypeDoc->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompTypeDoc->FournisseurDonnees = $this->ApplicationParent->CreeFournTypesDoc() ;
				$this->CompTypeDoc->NomColonneValeur = "nom" ;
				$this->CompTypeDoc->NomColonneLibelle = "titre" ;
				$this->TypeDocSelect->RemplitFormEtape($this->FormPrinc) ;
				$this->TypeActeur = $this->FormPrinc->InsereFltEditHttpPost("type_acteur", "type_acteur") ;
				$this->TypeActeur->Libelle = "Assign&eacute; &agrave;" ;
				$this->TypeActeur->ValeurParDefaut = $this->NomTypeActeurSelect ;
				$this->CompActeur = $this->TypeActeur->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompActeur->FournisseurDonnees = $this->ApplicationParent->CreeFournTypesActeur() ;
				$this->CompActeur->NomColonneValeur = "nom" ;
				$this->CompActeur->NomColonneLibelle = "titre" ;
				$this->TypeActeurSelect->RemplitFormTache($this->FormPrinc) ;
				$this->FltMembreValid = $this->FormPrinc->InsereFltEditHttpPost("login_validateur", "login_validateur") ;
				$this->FltMembreValid->NePasLierColonne = 1 ;
				$this->FltMembreValid->Libelle = "Valid&eacute; par" ;
				$this->FltDateDernValid = $this->FormPrinc->InsereFltEditHttpPost("date_valid", "date_valid") ;
				$this->FltDateDernValid->NePasLierColonne = 1 ;
				$this->FltDateDernValid->Libelle = "Valid&eacute; le" ;
				$this->FltCtnCmt = $this->FormPrinc->InsereFltEditHttpPost("contenu_comment", "contenu_comment") ;
				$this->FltCtnCmt->Libelle = "Contenu Commentaire" ;
				$this->FltCtnCmt->DeclareComposant("PvCkEditor") ;
				$this->FltCtnCmt->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->FltPjCmt = $this->FormPrinc->InsereFltEditHttpUpload("piece_jointe_comment", "files/docs", "piece_jointe_comment") ;
				$this->FltPjCmt->Libelle = "Pi&egrave;ce jointe Commentaire" ;
				$this->FltStatutValid = $this->FormPrinc->InsereFltEditHttpPost("statut_validation", "statut_validation") ;
				$this->FltStatutValid->Libelle = "Statut" ;
				$this->FltStatutValid->NePasLierColonne = 1 ;
				$this->CompStatutValid = $this->FltStatutValid->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompStatutValid->FournisseurDonnees = $this->ApplicationParent->CreeFournStatutPubl() ;
				$this->CompStatutValid->NomColonneValeur = "id" ;
				$this->CompStatutValid->NomColonneLibelle = "titre" ;
				$this->FormPrinc->DessinateurFiltresEdition = new DessinDetailEtapeValidUnafdt() ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-6">'.$this->FormPrinc->RenduDispositif().'</div>
<div class="col-xs-6">' ;
				if($this->InitiateurConnecte == 1)
				{
					$ctn .= '<h4>Circuit de validation</h4>
<iframe src="?appelleScript=suiviEtapesValid&id='.urlencode($this->IdEtapeInitiale).'" style="width:100%; height:300px; border:0px;"></iframe>';
				}
				// print_r($this->FormPrinc->ElementsEnCours) ;
				$ctn .= '</div>
</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class ScriptSuiviEtapesValidUnafdt extends ScriptBaseUnafdt
		{
			public $NomDocumentWeb = "cadre2" ;
			public $Privileges = array("poster_formulaire", "validation_formulaire") ;
			public $IdEtapeValid = 0 ;
			public $MaxEtapes = 100 ;
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if(! $ok)
				{
					return $ok ;
				}
				$this->DetermineLgnEtapeValid() ;
				return $this->LgnEtapeValidTrouvee() ;
			}
			protected function DetermineLgnEtapeValid()
			{
				$bd = $this->BDPrinc() ;
				$this->IdEtapeValid = _GET_def("id") ;
				$this->LgnEtapeValid = $bd->FetchSqlRow(
					"select t1.*, t2.login_member login_valid
from fluxtaf_etape_valid t1
left join membership_member t2 on t1.id_membre_valid = t2.id
where t1.id=:idEtape and t1.id_membre_creation=:idMembreConnecte and t1.id_etape_parent=0",
					array("idEtape" => $this->IdEtapeValid, "idMembreConnecte" => $this->ZoneParent->IdMembreConnecte())
				) ;
			}
			protected function LgnEtapeValidTrouvee()
			{
				return (is_array($this->LgnEtapeValid) && count($this->LgnEtapeValid) > 0) ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$bd = $this->BDPrinc() ;
				$lgnEtape = $this->LgnEtapeValid ;
				$parcourir = 1 ;
				$totalEtapes = 0 ;
				$idTacheValid = 0 ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				while(count($lgnEtape) > 0)
				{
					$idTacheValid = $lgnEtape["id_tache_valid"] ;
					$lgnEtape = $bd->FetchSqlRow("select t1.*, t3.type_acteur, t3.param1_acteur, t3.param2_acteur, t3.param3_acteur, t3.param4_acteur, t2.login_member login_valid
from fluxtaf_etape_valid t1
left join membership_member t2 on t1.id_membre_valid = t2.id
left join fluxtaf_tache_valid t3 on t1.id_tache_valid = t3.id
where t1.id_etape_parent=:idEtape", array("idEtape" => $lgnEtape["id"])) ;
					if(! is_array($lgnEtape) || count($lgnEtape) == 0)
					{
						break ;
					}
					$txtActeur = 'En cours de validation' ;
					$classeFa = 'fa-clock-o' ;
					if($lgnEtape["statut_validation"] != 0)
					{
						$classeFa = ($lgnEtape["statut_validation"] == 1) ? "fa-check" : "fa-remove" ;
						$txtAction = (($lgnEtape["statut_validation"] == 1) ? "confirm&eacute;" : "rejet&eacute;")." le ".date_time_fr($lgnEtape["date_valid"]) ;
						$txtActeur = $txtAction.' par <b>'.htmlentities($lgnEtape["login_valid"]) ;
					}
					else
					{
						$typeActeur = $this->ApplicationParent->TypesActeur[$lgnEtape["type_acteur"]] ;
						$sqlActeur = $typeActeur->SqlTitreValid() ;
						$paramsActeur = $typeActeur->ParamsTitreValid($lgnEtape) ;
						$lgnActeur = $bd->FetchSqlRow($sqlActeur, $paramsActeur) ;
						$txtActeur = htmlentities($lgnEtape["type_acteur"]).', '.htmlentities($lgnEtape["param1_acteur"]) ;
						if(is_array($lgnActeur) && count($lgnActeur) > 0)
						{
							$txtActeur = htmlentities($lgnActeur["titre"]) ;
						}
					}
					$ctn .= '<div class="row">
<div class="col-xs-12">
<a href="?appelleScript=detailEtapeValid&id='.urlencode($lgnEtape["id"]).'" target="_top"><i class="fa '.$classeFa.' fa-fw"></i> '.htmlentities($lgnEtape["titre"]).'</a> - '.$txtActeur.'</b>
</div>
</div>'.PHP_EOL ;
					$totalEtapes++ ;
					if($totalEtapes > $this->MaxEtapes)
					{
						break ;
					}
				}
				$lgnTacheValid = $this->ObtientLgnTacheValid($idTacheValid) ;
				if(count($lgnTacheValid) > 0)
				{
					$ctn .= '<h4>Validations suivantes</h4>' ;
					while(count($lgnTacheValid) > 0)
					{
						$typeActeur = $this->ApplicationParent->TypesActeur[$lgnTacheValid["type_acteur"]] ;
						$sqlActeur = $typeActeur->SqlTitreValid() ;
						$paramsActeur = $typeActeur->ParamsTitreValid($lgnTacheValid) ;
						$lgnActeur = $bd->FetchSqlRow($sqlActeur, $paramsActeur) ;
						$titreValid = htmlentities($lgnTacheValid["type_acteur"]).', '.htmlentities($lgnTacheValid["param1_acteur"]) ;
						if(is_array($lgnActeur) && count($lgnActeur) > 0)
						{
							$titreValid = htmlentities($lgnActeur["titre"]) ;
						}
						$ctn .= '<div class="row">
	<div class="col-xs-12">
	<i class="fa fa-ellipsis-h fa-fw"></i> '.$titreValid.'</div>
	</div>'.PHP_EOL ;
						$lgnTacheValid = $this->ObtientLgnTacheValid($lgnTacheValid["id"]) ;
					}
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function ObtientLgnTacheValid($idTache)
			{
				$bd = $this->BDPrinc() ;
				$res = $bd->FetchSqlRow('select * from fluxtaf_tache_valid where id_tache_parent=:idTache and id_resultat_tache=1', array("idTache" => $idTache)) ;
				return $res ;
			}
		}
		
		class ScriptListeEtapesValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Validations en attente" ;
			public $Titre = "Validations en attente" ;
			public $Privileges = array("validation_formulaire") ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			protected function ChargeTablPrinc()
			{
				parent::ChargeTablPrinc() ;
				$expr = $this->ApplicationParent->ExprSqlProcessValidsVerifs($this) ;
				$this->TablPrinc->FournisseurDonnees->RequeteSelection = '('.$expr->Texte.')' ;
				$this->TablPrinc->FournisseurDonnees->ParamsSelection = $expr->Parametres ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->TablPrinc->SensColonneTri = "desc" ;
				$this->TablPrinc->AccepterTriColonneInvisible = 1 ;
				$this->DefColTitre = $this->TablPrinc->InsereDefCol("titre", "Titre") ;
				$this->DefColTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->TablPrinc->InsereDefCol("login_membre_creation", "Initi&eacute; par") ;
				$this->TablPrinc->InsereDefColDateTimeFr("date_creation", "Cr&eacute;&eacute; le") ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienTraiter = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=traiteEtapeValid&id=${id}', "<i class='fa fa-check fa-fw' title='Traiter'></i>") ;
			}
		}
		
		class ScriptTraiteEtapeValidUnafdt extends ScriptEditBaseUnafdt
		{
			public $TitreDocument = "Validation formulaire" ;
			public $Titre = "Validation formulaire" ;
			protected $TablEditFormPrinc = "fluxtaf_etape_valid" ;
			public $Privileges = array("validation_formulaire") ;
			protected $FormPrincEditable = 1 ;
			protected $PourAjout = 0 ;
			public $IdEtapeValid = 0 ;
			protected $ReqSelectFormPrinc = "(select t1.*, t3.login_member login_initiateur, t4.login_member login_validateur
from fluxtaf_etape_valid t1
left join fluxtaf_etape_valid t2 on t1.id_etape_initiale = t2.id
left join membership_member t3 on t2.id_membre_creation = t3.id
left join membership_member t4 on t1.id_membre_creation = t4.id
)" ;
			protected $NomClasseCmdExecFormPrinc = "CmdTraiteEtapeValidUnafdt" ;
			protected $UrlRedirectAnnulFormPrinc = "?appelleScript=listeEtapesValid" ;
			public $NomScriptExecSucces = "listeEtapesValid" ;
			protected function DetecteLgnTacheValid()
			{
				$bd = $this->BDPrinc() ;
				$this->IdEtapeValid = _GET_def("id") ;
				$this->LgnTacheValid = $bd->FetchSqlRow("select t2.* from fluxtaf_etape_valid t1
inner join fluxtaf_tache_valid t2 on t1.id_tache_valid = t2.id
where t1.id = :id and t1.statut_validation = 0", array("id" => $this->IdEtapeValid)) ;
				if($this->LgnTacheValidTrouvee())
				{
					$nomTypeActeur = $this->LgnTacheValid["type_acteur"] ;
					$typeActeur = $this->ApplicationParent->TypesActeur[$nomTypeActeur] ;
					$expr = $typeActeur->ExprSqlAccesTache($this, $this->LgnTacheValid["id"]) ;
					$lgn = $bd->FetchSqlRow($expr->Texte, $expr->Parametres) ;
					// print_r($bd) ;
					if(is_array($lgn) && count($lgn) > 0)
					{
						$this->TypeActeurSelect = & $typeActeur ;
						$this->TypeDocSelect = $this->ApplicationParent->TypesDoc[$this->LgnTacheValid["type_doc"]] ;
					}
					else
					{
						$this->LgnTacheValid = null ;
					}
				}
			}
			public function EstAccessible()
			{
				$ok = parent::EstAccessible() ;
				if($ok == false)
				{
					return $ok ;
				}
				$this->DetecteLgnTacheValid() ;
				return $this->LgnTacheValidTrouvee() ;
			}
			protected function LgnTacheValidTrouvee()
			{
				return (is_array($this->LgnTacheValid) && count($this->LgnTacheValid) > 0) ;
			}
			protected function ChargeFormPrinc()
			{
				$this->FltId = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self> and statut_validation = 0") ;
				$this->FltMembreCrea = $this->FormPrinc->InsereFltEditHttpPost("login_initiateur", "login_initiateur") ;
				$this->FltMembreCrea->Libelle = "Initi&eacute; par" ;
				$this->FltMembreCrea->EstEtiquette = 1 ;
				$this->FltDateDernValid = $this->FormPrinc->InsereFltEditHttpPost("date_creation", "date_creation") ;
				$this->FltDateDernValid->Libelle = "Valid&eacute; le" ;
				$this->FltDateDernValid->EstEtiquette = 1 ;
				$this->FltDateDernValid->DefinitFmtLbl(new PvFmtLblDateTimeFr()) ;
				$this->FltMembreValid = $this->FormPrinc->InsereFltEditHttpPost("login_validateur", "login_validateur") ;
				$this->FltMembreValid->Libelle = "Valid&eacute; par" ;
				$this->FltMembreValid->EstEtiquette = 1 ;
				if($this->TypeDocSelect->InscrireTitre == 1)
				{
					$this->FltTitre = $this->FormPrinc->InsereFltEditHttpPost("titre", "titre") ;
					$this->FltTitre->Libelle = "Titre" ;
					$this->FltTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				}
				$this->TypeDocSelect->RemplitFormEtape($this->FormPrinc) ;
				$this->FltCtnCmt = $this->FormPrinc->InsereFltEditHttpPost("contenu_comment", "contenu_comment") ;
				$this->FltCtnCmt->Libelle = "Contenu Commentaire" ;
				$this->FltCtnCmt->DeclareComposant("PvCkEditor") ;
				$this->FltCtnCmt->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->FltPjCmt = $this->FormPrinc->InsereFltEditHttpUpload("piece_jointe_comment", "files/docs", "piece_jointe_comment") ;
				$this->FltPjCmt->Libelle = "Pi&egrave;ce jointe Commentaire" ;
				$this->FltStatutValid = $this->FormPrinc->InsereFltEditHttpPost("statut_validation", "statut_validation") ;
				$this->FltStatutValid->Libelle = "Statut" ;
				$this->CompStatutValid = $this->FltStatutValid->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompStatutValid->FournisseurDonnees = $this->ApplicationParent->CreeFournStatutValid() ;
				$this->CompStatutValid->NomColonneValeur = "id" ;
				$this->CompStatutValid->NomColonneLibelle = "titre" ;
				$this->FltDateValid = $this->FormPrinc->InsereFltEditFixe("date_valid", date("Y-m-d H:i:s"), "date_valid") ;
				$this->FltIdMembreValid = $this->FormPrinc->InsereFltEditFixe("id_membre_valid", $this->ZoneParent->IdMembreConnecte(), "id_membre_valid") ;
				$this->FormPrinc->DessinateurFiltresEdition = new DessinTraiteEtapeValidUnafdt() ;
			}
		}
		
		class ScriptAvisEtapesValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $Titre = "Validations effectu&eacute;es" ;
			public $TitreDocument = "Validations effectu&eacute;es" ;
			protected $ReqSelectTablPrinc = "(select t1.*, t6.titre titre_tache_valid, t3.login_member login_valid, t2.date_creation date_initiee, t5.login_member login_initiateur, t4.id_tache_valid id_tache_initiale
from fluxtaf_etape_valid t1
inner join fluxtaf_etape_valid t2 on t1.id_etape_parent = t2.id
inner join fluxtaf_etape_valid t4 on t1.id_etape_initiale = t4.id
left join membership_member t3 on t1.id_membre_valid = t3.id
inner join fluxtaf_tache_valid t6 on t4.id_tache_valid = t6.id
left join membership_member t5 on t4.id_membre_valid = t5.id)" ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			public $Privileges = array("poster_formulaire", "validation_formulaire") ;
			protected function ChargeTablPrinc()
			{
				parent::ChargeTablPrinc() ;
				$this->FltMembreCrea = $this->TablPrinc->InsereFltSelectFixe("membreCrea", $this->ZoneParent->IdMembreConnecte(), "id_membre_valid=<self> and id_etape_parent <> 0 and statut_validation <> 0") ;
				$this->FltTache = $this->TablPrinc->InsereFltSelectHttpGet("tache", "id_tache_initiale = <self>") ;
				$this->FltTache->Libelle = "T&acirc;che" ;
				$this->CompTache = $this->FltTache->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompTache->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$expr = $this->ApplicationParent->ExprSqlProcessValidsDispos($this) ;
				$this->CompTache->FournisseurDonnees->RequeteSelection = "(".$expr->Texte.")" ;
				$this->CompTache->FournisseurDonnees->ParamsSelection = $expr->Parametres ;
				$this->CompTache->NomColonneLibelle = "titre" ;
				$this->CompTache->NomColonneValeur = "id" ;
				$this->CompTache->InclureElementHorsLigne = 1 ;
				$this->FltTitre = $this->TablPrinc->InsereFltSelectHttpGet("titre", "instr(titre, <self>) > 0") ;
				$this->FltTitre->Libelle = "Titre" ;
				$this->FltLogin = $this->TablPrinc->InsereFltSelectHttpGet("loginMembre", "(instr(login_initiateur, <self>) > 0 or instr(login_valid, <self>) > 0)") ;
				$this->FltLogin->Libelle = "Login" ;
				$this->TablPrinc->AccepterTriColonneInvisible = 1 ;
				$this->TablPrinc->ToujoursAfficher = 1 ;
				$this->TablPrinc->SensColonneTri = "desc" ;
				$this->TablPrinc->InsereDefColCachee("date_valid") ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->TablPrinc->InsereDefCol("titre_tache_valid", "Tache") ;
				$this->DefColTitre = $this->TablPrinc->InsereDefCol("titre", "Formulaire") ;
				$this->DefColTitre->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->TablPrinc->InsereDefColDateTimeFr("date_initiee", "Initi&eacute; le") ;
				$this->TablPrinc->InsereDefCol("login_initiateur", "Initi&eacute; par") ;
				$this->TablPrinc->InsereDefColDateTimeFr("date_valid", "Valid&eacute; le") ;
				$this->TablPrinc->InsereDefCol("login_valid", "Valid&eacute; par") ;
				$this->TablPrinc->InsereDefColChoix("statut_validation", "Statut", "", array(1 => "Confirm&eacute;", 2 => "Rejet&eacute;")) ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienDetails = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, "?appelleScript=detailEtapeValid&id=\${id}", "<i class='fa fa-list fa-fw' title='D&eacute;tails'></i>") ;
			}
			public function RenduSpecifique()
			{
				$ctn = parent::RenduSpecifique() ;
				return $ctn ;
			}
		}
		
		class CmdCreeProcessValidUnafdt extends CmdExecBaseUnafdt
		{
			public $MessageSuccesExecution = "Le formulaire a &eacute;t&eacute; ajout&eacute; avec succ&egrave;s" ;
			protected function ExecuteInstructions()
			{
				$this->ScriptParent->TypeDocSelect->AppliqueCmdExecFormEtape($this) ;
				$bd = $this->ScriptParent->BDPrinc() ;
				$tableEdit = "fluxtaf_etape_valid" ;
				$form = & $this->FormulaireDonneesParent ;
				$lgn = $form->FournisseurDonnees->ExtraitParametresFiltres($form->FiltresEdition) ;
				$ok = $bd->InsertRow($tableEdit, $lgn) ;
				if($ok)
				{
					$lgnProcess = $bd->FetchSqlRow("select * from ".$tableEdit." where id_ctrl = :idCtrl", array("idCtrl" => $lgn["id_ctrl"])) ;
					$idCtrl = uniqid() ;
					$ok = $bd->InsertRow(
						$tableEdit,
						array(
							"id_tache_valid" => $lgnProcess["id_tache_valid"],
							"id_ctrl" => $idCtrl,
							"id_membre_creation" => $this->ZoneParent->IdMembreConnecte(),
							"id_membre_valid" => $this->ZoneParent->IdMembreConnecte(),
							"statut_validation" => 1,
							"date_valid" => date("Y-m-d H:i:s"),
							"titre" => $lgnProcess["titre"],
							"param1_doc" => $lgnProcess["param1_doc"],
							"param2_doc" => $lgnProcess["param2_doc"],
							"param3_doc" => $lgnProcess["param3_doc"],
							"param4_doc" => $lgnProcess["param4_doc"],
							"param5_doc" => $lgnProcess["param5_doc"],
							"param6_doc" => $lgnProcess["param6_doc"],
							"param7_doc" => $lgnProcess["param7_doc"],
							"param8_doc" => $lgnProcess["param8_doc"],
							"param_int1_doc" => $lgnProcess["param_int1_doc"],
							"param_int2_doc" => $lgnProcess["param_int2_doc"],
							"param_int3_doc" => $lgnProcess["param_int3_doc"],
							"param_int4_doc" => $lgnProcess["param_int4_doc"],
							"param_int5_doc" => $lgnProcess["param_int5_doc"],
							"param_int6_doc" => $lgnProcess["param_int6_doc"],
							"param_int7_doc" => $lgnProcess["param_int7_doc"],
							"param_int8_doc" => $lgnProcess["param_int8_doc"],
							"param_double1_doc" => $lgnProcess["param_double1_doc"],
							"param_double2_doc" => $lgnProcess["param_double2_doc"],
							"param_double3_doc" => $lgnProcess["param_double3_doc"],
							"param_double4_doc" => $lgnProcess["param_double4_doc"],
							"param_double5_doc" => $lgnProcess["param_double5_doc"],
							"param_double6_doc" => $lgnProcess["param_double6_doc"],
							"param_double7_doc" => $lgnProcess["param_double7_doc"],
							"param_double8_doc" => $lgnProcess["param_double8_doc"],
							"param_text1_doc" => $lgnProcess["param_text1_doc"],
							"param_text2_doc" => $lgnProcess["param_text2_doc"],
							"param_text3_doc" => $lgnProcess["param_text3_doc"],
							"param_text4_doc" => $lgnProcess["param_text4_doc"],
							"param_text5_doc" => $lgnProcess["param_text5_doc"],
							"param_text6_doc" => $lgnProcess["param_text6_doc"],
							"param_text7_doc" => $lgnProcess["param_text7_doc"],
							"param_text8_doc" => $lgnProcess["param_text8_doc"],
							"id_etape_parent" => $lgnProcess["id"],
							"id_etape_initiale" => $lgnProcess["id"],
						)
					) ;
					if(! $ok)
					{
						$this->RenseigneErreurBD($bd) ;
					}
					else
					{
						$lgnProcess2 = $bd->FetchSqlRow("select t1.*, t2.id_tache_confirm from ".$tableEdit." t1
inner join fluxtaf_tache_valid t2 on t1.id_tache_valid = t2.id
where t1.id_ctrl = :idCtrl", array("idCtrl" => $idCtrl)) ;
						$idCtrl2 = uniqid() ;
						$ok = false ;
						if(is_array($lgnProcess2) && count($lgnProcess2) > 0)
						{
							if($lgnProcess2["id_tache_confirm"] > 0)
							{
								$ok = $bd->InsertRow(
									$tableEdit,
									array(
										"id_tache_valid" => $lgnProcess2["id_tache_confirm"],
										"id_ctrl" => $idCtrl2,
										"id_membre_creation" => $this->ZoneParent->IdMembreConnecte(),
										"statut_validation" => 0,
										"titre" => $lgnProcess2["titre"],
										"param1_doc" => $lgnProcess2["param1_doc"],
										"param2_doc" => $lgnProcess2["param2_doc"],
										"param3_doc" => $lgnProcess2["param3_doc"],
										"param4_doc" => $lgnProcess2["param4_doc"],
										"param5_doc" => $lgnProcess2["param5_doc"],
										"param6_doc" => $lgnProcess2["param6_doc"],
										"param7_doc" => $lgnProcess2["param7_doc"],
										"param8_doc" => $lgnProcess2["param8_doc"],
										"param_int1_doc" => $lgnProcess2["param_int1_doc"],
										"param_int2_doc" => $lgnProcess2["param_int2_doc"],
										"param_int3_doc" => $lgnProcess2["param_int3_doc"],
										"param_int4_doc" => $lgnProcess2["param_int4_doc"],
										"param_int5_doc" => $lgnProcess2["param_int5_doc"],
										"param_int6_doc" => $lgnProcess2["param_int6_doc"],
										"param_int7_doc" => $lgnProcess2["param_int7_doc"],
										"param_int8_doc" => $lgnProcess2["param_int8_doc"],
										"param_double1_doc" => $lgnProcess2["param_double1_doc"],
										"param_double2_doc" => $lgnProcess2["param_double2_doc"],
										"param_double3_doc" => $lgnProcess2["param_double3_doc"],
										"param_double4_doc" => $lgnProcess2["param_double4_doc"],
										"param_double5_doc" => $lgnProcess2["param_double5_doc"],
										"param_double6_doc" => $lgnProcess2["param_double6_doc"],
										"param_double7_doc" => $lgnProcess2["param_double7_doc"],
										"param_double8_doc" => $lgnProcess2["param_double8_doc"],
										"param_text1_doc" => $lgnProcess2["param_text1_doc"],
										"param_text2_doc" => $lgnProcess2["param_text2_doc"],
										"param_text3_doc" => $lgnProcess2["param_text3_doc"],
										"param_text4_doc" => $lgnProcess2["param_text4_doc"],
										"param_text5_doc" => $lgnProcess2["param_text5_doc"],
										"param_text6_doc" => $lgnProcess2["param_text6_doc"],
										"param_text7_doc" => $lgnProcess2["param_text7_doc"],
										"param_text8_doc" => $lgnProcess2["param_text8_doc"],
										"id_etape_parent" => $lgnProcess2["id"],
										"id_etape_initiale" => $lgnProcess["id"],
									)
								) ;
								if($ok)
								{
									$idEtape3 = $bd->FetchSqlValue("select id from fluxtaf_etape_valid where id_ctrl=:idCtrl", array("idCtrl" => $idCtrl2), "id") ;
									$this->ZoneParent->ActionAlerteEtapeValid->Invoque(array("idEtape" => $idEtape3, "idMembre" => $this->ZoneParent->IdMembreConnecte())) ;
								}
							}
							else
							{
								$ok = true ;
							}
						}
						if($ok)
						{
							$this->ConfirmeSucces() ;
						}
						elseif(! is_array($lgnProcess2))
						{
							$this->RenseigneErreurBD($bd) ;
						}
						else
						{
							$this->RenseigneErreur("Cr&eacute;ation du circuit de validation echou&eacute;. Veuillez contacter l'administrateur.") ;
						}
					}
				}
				else
				{
					$this->RenseigneErreurBD($bd) ;
				}
			}
		}
		
		class CmdTraiteEtapeValidUnafdt extends CmdExecBaseUnafdt
		{
			public $InscrireLienAnnuler = 1 ;
			public $UrlLienAnnuler = '?appelleScript=listeEtapesValid' ;
			public $CacherFormulaireFiltresSiSucces = 1 ;
			protected function ExecuteInstructions()
			{
				$this->ScriptParent->TypeDocSelect->AppliqueCmdExecTraiteEtape($this) ;
				$form = & $this->FormulaireDonneesParent ;
				$script = & $this->ScriptParent ;
				$statutValid = $script->FltStatutValid->Lie() ;
				$bd = $script->BDPrinc() ;
				if($statutValid == 1 || $statutValid == 2)
				{
					$lgnForm = $form->FournisseurDonnees->ExtraitParametresFiltres($form->FiltresEdition) ;
					$idEtape = $script->FltId->Lie() ;
 					$ok = $bd->UpdateRow("fluxtaf_etape_valid", $lgnForm, "id = :id and statut_validation = 0", array("id" => $idEtape)) ;
					$idCtrl = uniqid() ;
					if($ok)
					{
						$lgnTache = $bd->FetchSqlRow("select t2.*, t3.est_tache_finale tache_finale_confirm, t4.est_tache_finale tache_finale_rejet
from fluxtaf_etape_valid t1
inner join fluxtaf_tache_valid t2 on t1.id_tache_valid = t2.id
left join fluxtaf_tache_valid t3 on t2.id_tache_confirm = t3.id
left join fluxtaf_tache_valid t4 on t2.id_tache_rejet = t4.id
where t1.id = :id", array("id" => $idEtape)) ;
						$idProchTache = 0 ;
						$prochTacheFinale = 0 ;
						if(is_array($lgnTache) && count($lgnTache) > 0)
						{
							$idProchTache = intval(($statutValid == 1) ? $lgnTache["id_tache_confirm"] : $lgnTache["id_tache_rejet"]) ;
							$prochTacheFinale = intval(($statutValid == 1) ? $lgnTache["tache_finale_confirm"] : $lgnTache["tache_finale_rejet"]) ;
							if($idProchTache == '')
							{
								$prochTacheFinale = 1 ;
							}
						}
						elseif(! is_array($lgnTache))
						{
							$ok = 0 ;
						}
						if($idProchTache > 0)
						{
							$ok = $bd->RunSql("insert into fluxtaf_etape_valid (id_ctrl, id_membre_creation, titre, param1_doc, param2_doc, param3_doc, param4_doc, param5_doc, param6_doc, param7_doc, param8_doc, param_int1_doc, param_int2_doc, param_int3_doc, param_int4_doc, param_int5_doc, param_int6_doc, param_int7_doc, param_int8_doc, param_double1_doc, param_double2_doc, param_double3_doc, param_double4_doc, param_double5_doc, param_double6_doc, param_double7_doc, param_double8_doc, param_text1_doc, param_text2_doc, param_text3_doc, param_text4_doc, param_text5_doc, param_text6_doc, param_text7_doc, param_text8_doc, id_tache_valid, est_etape_finale, id_etape_initiale, id_etape_parent)
select :idCtrl, id_membre_valid, titre, param1_doc, param2_doc, param3_doc, param4_doc, param5_doc, param6_doc, param7_doc, param8_doc, param_int1_doc, param_int2_doc, param_int3_doc, param_int4_doc, param_int5_doc, param_int6_doc, param_int7_doc, param_int8_doc, param_double1_doc, param_double2_doc, param_double3_doc, param_double4_doc, param_double5_doc, param_double6_doc, param_double7_doc, param_double8_doc, param_text1_doc, param_text2_doc, param_text3_doc, param_text4_doc, param_text5_doc, param_text6_doc, param_text7_doc, param_text8_doc, :idTacheValid, :estEtapeFinale, id_etape_initiale, id
from fluxtaf_etape_valid where id=:idEtape", array("idCtrl" => $idCtrl, "idEtape" => $idEtape, "idTacheValid" => $idProchTache, "estEtapeFinale" => $prochTacheFinale)) ;
							$idEtape3 = $bd->FetchSqlValue("select id from fluxtaf_etape_valid where id_ctrl=:idCtrl", array("idCtrl" => $idCtrl), "id") ;
							$this->ZoneParent->ActionAlerteEtapeValid->Invoque(array("idEtape" => $idEtape3, "idMembre" => $this->ZoneParent->IdMembreConnecte())) ;
						}
						elseif($ok)
						{
							$ok = $bd->RunSql("update fluxtaf_etape_valid set est_etape_finale=1 where id=:idEtape", array("idEtape" => $idEtape)) ;
							$this->ZoneParent->ActionAlerteEtapeTerminee->Invoque(array("idEtape" => $idEtape, "idMembre" => $this->ZoneParent->IdMembreConnecte())) ;
						}
					}
					if($ok)
					{
						$this->ConfirmeSucces("Le formulaire ".htmlentities(utf8_decode($lgnForm["titre"]))." a &eacute;t&eacute; ".(($statutValid == 1) ? "confirm&eacute;" : "rejet&eacute;")) ;
					}
					else
					{
						$this->RenseigneErreurBD($bd) ;
					}
				}
				else
				{
					$this->RenseigneErreur("Statut de publication incorrect") ;
				}
			}
		}
		
	}
	
?>