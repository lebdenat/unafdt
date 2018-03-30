<?php
	
	if(! defined('SCRIPT_TACHE_VALID_UNAFDT'))
	{
		define('SCRIPT_TACHE_VALID_UNAFDT', 1) ;
		
		class ScriptListeTachesValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Liste des t&acirc;ches de validation" ;
			public $Titre = "Liste des t&acirc;ches de validation" ;
			protected $ReqSelectTablPrinc = "fluxtaf_tache_valid" ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			public $Privileges = array("gestion_circuit_valid") ;
			protected function ChargeTablPrinc()
			{
				parent::ChargeTablPrinc() ;
				$this->CmdAjout = $this->TablPrinc->InsereCmdRedirectUrl("ajout", "?appelleScript=ajoutTacheValid", "Cr&eacute;er") ;
				$this->TablPrinc->InsereFltSelectFixe("est_tache_initiale", 1, "est_tache_initiale = <self>") ;
				$this->TablPrinc->InsereFltSelectFixe("est_proprio", $this->ZoneParent->IdMembreConnecte(), "id_membre_creation = <self>") ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->TablPrinc->InsereDefColDateTimeFr("date_creation", "Date cr&eacute;ation") ;
				$this->TablPrinc->InsereDefCol("titre", "Titre") ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienModif = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, "?appelleScript=modifTacheValid&id=\${id}", '<i class="fa fa-pencil fa-fw" title="Modifier"></i>') ;
				$this->LienDetails = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, "?appelleScript=detailTacheValid&id=\${id}", '<i class="fa fa-list fa-fw" title="D&eacute;tails"></i>') ;
				$this->LienSuppr = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, "?appelleScript=supprTacheValid&id=\${id}", '<i class="fa fa-remove fa-fw" title="Supprimer"></i>') ;
			}
		}
		
		class ScriptEditTacheValidUnafdt extends ScriptEditBaseUnafdt
		{
			protected $ReqSelectFormPrinc = "fluxtaf_tache_valid" ;
			protected $TablEditFormPrinc = "fluxtaf_tache_valid" ;
			public $Privileges = array("gestion_circuit_valid") ;
			public $UrlRedirectAnnulFormPrinc = "?appelleScript=listeTachesValid" ;
			protected $NomTypeDocSelect ;
			public $TypeDocSelect ;
			protected function InitFormPrinc()
			{
				if($this->PourAjout == 0)
				{
					$this->UrlRedirectAnnulFormPrinc = "?appelleScript=detailTacheValid&id=".urlencode(_GET_def("id")) ;
				}
				parent::InitFormPrinc() ;
			}
			protected function ChargeFltsSelectPrinc()
			{
				$this->FltId = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
			}
			protected function ChargeFltsEditPrinc()
			{
				$this->FormPrinc->Largeur = '450px' ;
				$this->FltIdCtrl = $this->FormPrinc->InsereFltEditFixe("id_ctrl", uniqid(), "id_ctrl") ;
				$this->FltTitre = $this->FormPrinc->InsereFltEditHttpPost("titre", "titre") ;
				$this->FltTitre->Libelle = "Titre" ;
				$this->FltIndic = $this->FormPrinc->InsereFltEditHttpPost("indicatif", "indicatif") ;
				$this->FltIndic->Libelle = "Indicatif" ;
				$this->FltIndic->DeclareComposant("PvZoneMultiligneHtml") ;
				if($this->PourAjout == 1)
				{
					$this->FltEstEtapeInitiale = $this->FormPrinc->InsereFltEditFixe("est_tache_initiale", '1', "est_tache_initiale") ;
					$this->FltIdMembre = $this->FormPrinc->InsereFltEditFixe("id_membre_creation", $this->ZoneParent->IdMembreConnecte(), "id_membre_creation") ;
				}
				$this->FltTypeDoc = $this->FormPrinc->InsereFltEditHttpPost("type_doc", "type_doc") ;
				$this->FltTypeDoc->Libelle = "Document support" ;
				$this->CompTypeDoc = $this->FltTypeDoc->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompTypeDoc->FournisseurDonnees = $this->ApplicationParent->CreeFournTypesDoc() ;
				$this->CompTypeDoc->NomColonneValeur = "nom" ;
				$this->FltDocEditable = $this->FormPrinc->InsereFltEditHttpPost("doc_editable", "doc_editable") ;
				$this->FltDocEditable->Libelle = "Document Modifiable" ;
				$this->FltDocEditable->DeclareComposant("PvZoneSelectBoolHtml") ;
				$this->FltTacheFinale = $this->FormPrinc->InsereFltEditHttpPost("est_tache_finale", "est_tache_finale") ;
				$this->FltTacheFinale->DeclareComposant("PvZoneSelectBoolHtml") ;
				if($this->PourAjout == 1)
				{
					$this->FltDocEditable->EstEtiquette = 1 ;
					$this->FltDocEditable->ValeurParDefaut = 1 ;
				}
				$this->FltTacheFinale->ValeurParDefaut = "0" ;
				$this->FltTacheFinale->NePasLierColonne = 1 ;
				$this->FltTacheFinale->EstEtiquette = 1 ;
				$this->FltTacheFinale->Libelle = "Terminer le circuit" ;
				$this->CompTypeDoc->NomColonneLibelle = "titre" ;
				if($this->InscrireCmdExecFormPrinc == 1 && $this->FormPrincEditable == 1)
				{
					$this->FormPrinc->CommandeExecuter->InsereCritereNonVide(array("titre", "type_doc")) ;
				}
			}
			protected function ChargeFormPrinc()
			{
				parent::ChargeFormPrinc() ;
				$this->ChargeFltsSelectPrinc() ;
				$this->ChargeFltsEditPrinc() ;
			}
		}
		
		class CmdAjoutTacheValidUnafdt extends CmdAjoutElementUnafdt
		{
			protected function ExecuteInstructions()
			{
				parent::ExecuteInstructions() ;
				if($this->StatutExecution == 1)
				{
					$bd = $this->ScriptParent->BDPrinc() ;
					$fournDonnees = & $this->FormulaireDonneesParent->FournisseurDonnees ;
					$id = $bd->FetchSqlValue("select id from ".$bd->EscapeTableName($fournDonnees->TableEdition)." where id_ctrl=:idCtrl", array("idCtrl" => $this->ScriptParent->FltIdCtrl->Lie()), "id") ;
					if($id > 0)
					{
						$lienContinuer = $this->InsereLien("?appelleScript=detailTacheValid&id=".urlencode($id), '<i class="fa fa-info fw"></i> D&eacute;tails') ;
						$lienContinuer->ClassesCSS[] = "btn btn-primary" ;
					}
				}
			}
		}
		
		class ScriptAjoutTacheValidUnafdt extends ScriptEditTacheValidUnafdt
		{
			public $TitreDocument = "Ajouter une t&acirc;che de validation" ;
			public $Titre = "Ajouter une t&acirc;che de validation" ;
			public $MsgSuccesCmdExecFormPrinc = "La t&acirc;che de validation a &eacute;t&eacute; ajout&eacute;e avec succ&egrave;s" ;
			protected $PourAjout = 1 ;
			protected $FormPrincEditable = 1 ;
			protected $NomClasseCmdExecFormPrinc = "CmdAjoutTacheValidUnafdt" ;
		}
		class ScriptModifTacheValidUnafdt extends ScriptEditTacheValidUnafdt
		{
			public $MsgSuccesCmdExecFormPrinc = "La t&acirc;che de validation a &eacute;t&eacute; modif&eacute;e avec succ&egrave;s" ;
			public $TitreDocument = "Modifier la t&acirc;che de validation" ;
			public $Titre = "Modifier la t&acirc;che de validation" ;
			protected $PourAjout = 0 ;
			protected $FormPrincEditable = 1 ;
			protected $NomClasseCmdExecFormPrinc = "CmdModifElementUnafdt" ;
		}
		class ScriptSupprTacheValidUnafdt extends ScriptEditTacheValidUnafdt
		{
			public $MsgSuccesCmdExecFormPrinc = "La t&acirc;che de validation a &eacute;t&eacute; supprim&eacute;e avec succ&egrave;s" ;
			public $TitreDocument = "Supprimer la t&acirc;che de validation" ;
			public $Titre = "Supprimer la t&acirc;che de validation" ;
			protected $PourAjout = 0 ;
			protected $FormPrincEditable = 0 ;
			protected $NomClasseCmdExecFormPrinc = "CmdSupprElementUnafdt" ;
		}
		
		class ScriptDetailTacheValidUnafdt extends ScriptEditTacheValidUnafdt
		{
			protected $PourAjout = 0 ;
			protected $FormPrincEditable = 0 ;
			protected $InscrireCmdAnnulFormPrinc = 0 ;
			protected $InscrireCmdExecFormPrinc = 0 ;
			public $TitreDocument = "D&eacute;tails t&acirc;che de validation" ;
			public $Titre = "D&eacute;tails t&acirc;che de validation" ;
			protected function RenduCSSSpecifique()
			{
				$ctn = '' ;
				$ctn .= '<style type="text/css">
.cadre-tache-valid {
	width:98% ;
	height:300px ;
	border:0px ;
	background:transparent ;
}
</style>' ;
				return $ctn ;
			}
			protected function RenduCadresTacheValid()
			{
				$ctn = '' ;
				$id = $this->FltId->Lie() ;
				$ctn .= '<ul class="nav nav-tabs" role="tablist">
<li class="active" onclick="rafraichitCadreTacheValid(\'acteurTacheValid\') ;"><a href="#definit-acteur" role="tab" data-toggle="tab" title="Personnes qui peuvent acc&eacute;der valider la t&acirc;che">Acc&egrave;s</a></li>
<li onclick="rafraichitCadreTacheValid(\'suiviTacheValid\') ;"><a href="#suivi" role="tab" data-toggle="tab" title="T&acirc;ches de contr&ocirc;le">Suivi</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="definit-acteur">
<iframe src="?appelleScript=acteurTacheValid&id='.urlencode($id).'" id="acteurTacheValid" class="cadre-tache-valid"></iframe>
</div>
<div class="tab-pane" id="suivi">
<iframe src="?appelleScript=suiviTacheValid&id='.urlencode($id).'" id="suiviTacheValid" class="cadre-tache-valid"></iframe>
</div>
</div>
<script type="text/javascript">
function rafraichitCadreTacheValid(id)
{
	jQuery("#" + id).attr("src", jQuery("#" + id).attr("src")) ;
}
</script>' ;
				return $ctn ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$idTache = _GET_def("id") ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= '<p>
<a class="btn btn-default" href="?appelleScript=modifTacheValid&id='.urlencode($idTache).'"><i class="fa fa-pencil fa-fw"></i> Modifier</a>
<a class="btn btn-danger" href="?appelleScript=supprTacheValid&id='.urlencode($idTache).'"><i class="fa fa-remove fa-fw"></i> Supprimer</a>
</p>' ;
				$ctn .= parent::RenduSpecifique().PHP_EOL ;
				$ctn .= '</div>
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduCSSSpecifique().PHP_EOL ;
				$ctn .= $this->RenduCadresTacheValid() ;
				$ctn .= '</div>
</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class ScriptCadreTacheValidUnafdt extends ScriptEditTacheValidUnafdt
		{
			protected $InscrireCmdAnnulFormPrinc = 0 ;
			public $NomDocumentWeb = "cadre2" ;
			public function DetermineEnvironnement()
			{
				$this->DetermineTypesSpec() ;
				parent::DetermineEnvironnement() ;
			}
			protected function DetermineTypesSpec()
			{
			}
		}
		class ScriptActeurTacheValidUnafdt extends ScriptCadreTacheValidUnafdt
		{
			protected $NomTypeActeurSelect ;
			protected $TypeActeurSelect ;
			protected $PourAjout = 0 ;
			protected $FormPrincEditable = 1 ;
			protected $NomClasseCmdExecFormPrinc = "CmdModifElementUnafdt" ;
			protected $NomTypeActeurDefaut ;
			protected function DetermineTypesSpec()
			{
				$nomTypeActeur = _GET_def("nomTypeActeur") ;
				if($nomTypeActeur == '')
				{
					$bd = $this->BDPrinc() ;
					$nomTypeActeur = $bd->FetchSqlValue("select type_acteur from ".$bd->EscapeTableName($this->TablEditFormPrinc)." where id=:id", array("id" => _GET_def("id")), "type_acteur") ;
				}
				$nomsTypeActeur = array_keys($this->ApplicationParent->TypesActeur) ;
				$this->NomTypeActeurDefaut = $nomsTypeActeur[0] ;
				if($nomTypeActeur == "" || ! in_array($nomTypeActeur, $nomsTypeActeur))
				{
					$nomTypeActeur = $this->NomTypeActeurDefaut ;
				}
				$this->NomTypeActeurSelect = $nomTypeActeur ;
				$this->TypeActeurSelect = & $this->ApplicationParent->TypesActeur[$nomTypeActeur] ;
			}
			protected function ChargeFltsEditPrinc()
			{
				$this->FltTypeActeur = $this->FormPrinc->InsereFltEditHttpPost("type_acteur", "type_acteur") ;
				$this->FltTypeActeur->Libelle = "Type des acteurs" ;
				$this->FltTypeActeur->ValeurParDefaut = $this->NomTypeActeurSelect ;
				if($this->NomTypeActeurDefaut != $this->NomTypeActeurSelect)
				{
					$this->FltTypeActeur->NePasLireColonne = 1 ;
				}
				$this->CompActeur = $this->FltTypeActeur->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompActeur->FournisseurDonnees = $this->ApplicationParent->CreeFournTypesActeur() ;
				$this->CompActeur->NomColonneValeur = "nom" ;
				$this->CompActeur->NomColonneLibelle = "titre" ;
				$this->CompActeur->AttrsSupplHtml["onchange"] = 'window.location = "?appelleScript='.$this->NomElementZone.'&id='.urlencode($this->FltId->Lie()).'&nomTypeActeur=" + encodeURIComponent(this.value)' ;
				$this->TypeActeurSelect->RemplitFormTache($this->FormPrinc) ;
			}
		}
		class ScriptSuiviTacheValidUnafdt extends ScriptBaseUnafdt
		{
			protected $LgnTacheValid = null ;
			protected $ValeurRequeteSoumise = "" ;
			public $ValeurIdTacheValid = "" ;
			public $NomDocumentWeb = "cadre2" ;
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineLgnTacheValid() ;
				if($this->LgnTacheValidTrouvee())
				{
					$ok = $this->DetecteRequeteSoumise() ;
					if($ok == true)
					{
						$this->ExecuteRequeteSoumise() ;
					}
				}
			}
			protected function DetecteRequeteSoumise()
			{
				$this->ValeurRequeteSoumise = _GET_def("soumetRequete") ;
				return ($this->ValeurRequeteSoumise != '') ;
			}
			protected function ExecuteRequeteSoumise()
			{
				if($this->LgnTacheValid["est_tache_finale"] == 0)
				{
					switch($this->ValeurRequeteSoumise)
					{
						case "cree_tache_confirm" : {
							$this->CreeTacheConfirm() ;
						}
						break ;
						case "finalise_tache_confirm" : {
							$this->FinaliseTacheConfirm() ;
						}
						break ;
						case "cree_tache_rejet" : {
							$this->CreeTacheRejet() ;
						}
						break ;
						case "finalise_tache_rejet" : {
							$this->FinaliseTacheRejet() ;
						}
						break ;
						default : {
							$this->ValeurRequeteSoumise = "" ;
						}
						break ;
					}
				}
				if($this->ValeurRequeteSoumise != '')
				{
					$this->DetermineLgnTacheValid() ;
				}
			}
			protected function CreeLgnConfirm($estTacheFinale=0)
			{
				return array(
					"id_ctrl" => uniqid(),
					"titre" => $this->LgnTacheValid["titre"]." - Confirmation",
					"id_tache_parent" => $this->LgnTacheValid["id"],
					"id_resultat_tache" => 1,
					"id_membre_creation" => $this->ZoneParent->IdMembreConnecte(),
					"type_doc" => $this->LgnTacheValid["type_doc"],
					"type_acteur" => "autres",
					"est_tache_finale" => $estTacheFinale,
					"no_etape" => $this->LgnTacheValid["no_etape"] + 1,
				) ;
			}
			protected function MajLocalisationConfirm($lgn2, $idCtrl)
			{
				$bd = $this->BDPrinc() ;
				$idResult = $bd->FetchSqlValue("select id from fluxtaf_tache_valid where id_ctrl=:idCtrl", array("idCtrl" => $idCtrl), "id") ;
				$ok = $bd->UpdateRow("fluxtaf_tache_valid", array("id_tache_confirm" => $idResult), "id = :idTache", array("idTache" => $this->LgnTacheValid["id"])) ;
			}
			protected function CreeTacheConfirm()
			{
				$bd = $this->BDPrinc() ;
				$lgn2 = $this->CreeLgnConfirm(0) ;
				$ok = $bd->InsertRow("fluxtaf_tache_valid", $lgn2) ;
				if($ok)
				{
					$this->MajLocalisationConfirm($lgn2, $lgn2["id_ctrl"]) ;
				}
			}
			protected function FinaliseTacheConfirm()
			{
				$bd = $this->BDPrinc() ;
				$lgn2 = $this->CreeLgnConfirm(1) ;
				$ok = $bd->InsertRow("fluxtaf_tache_valid", $lgn2) ;
				if($ok)
				{
					$this->MajLocalisationConfirm($lgn2, $lgn2["id_ctrl"]) ;
				}
			}
			protected function CreeLgnRejet($estTacheFinale=0)
			{
				return array(
					"id_ctrl" => uniqid(),
					"titre" => $this->LgnTacheValid["titre"]." - Rejet",
					"id_tache_parent" => $this->LgnTacheValid["id"],
					"id_resultat_tache" => 2,
					"id_membre_creation" => $this->ZoneParent->IdMembreConnecte(),
					"type_doc" => $this->LgnTacheValid["type_doc"],
					"type_acteur" => "autres",
					"est_tache_finale" => $estTacheFinale,
					"no_etape" => $this->LgnTacheValid["no_etape"] + 1,
				) ;
			}
			protected function MajLocalisationRejet($lgn2, $idCtrl)
			{
				$bd = $this->BDPrinc() ;
				$idResult = $bd->FetchSqlValue("select id from fluxtaf_tache_valid where id_ctrl=:idCtrl", array("idCtrl" => $idCtrl), "id") ;
				$ok = $bd->UpdateRow("fluxtaf_tache_valid", array("id_tache_rejet" => $idResult), "id = :idTache", array("idTache" => $this->LgnTacheValid["id"])) ;
			}
			protected function CreeTacheRejet()
			{
				$bd = $this->BDPrinc() ;
				$lgn2 = $this->CreeLgnRejet(1) ;
				$ok = $bd->InsertRow("fluxtaf_tache_valid", $lgn2) ;
				if($ok)
				{
					$this->MajLocalisationRejet($lgn2, $lgn2["id_ctrl"]) ;
				}
			}
			protected function FinaliseTacheRejet()
			{
				$bd = $this->BDPrinc() ;
				$lgn2 = $this->CreeLgnRejet(1) ;
				$ok = $bd->InsertRow("fluxtaf_tache_valid", $lgn2) ;
				if($ok)
				{
					$this->MajLocalisationRejet($lgn2, $lgn2["id_ctrl"]) ;
				}
			}
			protected function DetermineLgnTacheValid()
			{
				$bd = $this->BDPrinc() ;
				$this->ValeurIdTacheValid = _GET_def("id") ;
				$this->LgnTacheValid = $bd->FetchSqlRow("select t1.*, t2.titre titre_tache_confirm, t3.titre titre_tache_rejet, t4.titre titre_tache_parent
from fluxtaf_tache_valid t1
left join fluxtaf_tache_valid t2 on t1.id_tache_confirm = t2.id
left join fluxtaf_tache_valid t3 on t1.id_tache_rejet = t3.id
left join fluxtaf_tache_valid t4 on t1.id_tache_parent = t4.id
where t1.id=:idTache and t1.id_membre_creation=:idMembre", array(
	"idTache" => $this->ValeurIdTacheValid,
	"idMembre" => $this->ZoneParent->IdMembreConnecte(),
)
) ;
			}
			public function LgnTacheValidTrouvee()
			{
				return (is_array($this->LgnTacheValid) && count($this->LgnTacheValid) > 0) ;
			}
			public function PossedeRequeteSoumise()
			{
				return $this->ValeurRequeteSoumise != "" ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				if($this->LgnTacheValidTrouvee())
				{
					// Tache initiale
					if($this->LgnTacheValid["est_tache_initiale"] == 0)
					{
						$ctn .= '<div class="container-fluid">'.PHP_EOL ;
						$ctn .= '<div class="row">'.PHP_EOL ;
						$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
						$ctn .= '<b>T&acirc;che source : </b>' ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '<div class="row">'.PHP_EOL ;
						$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
						$ctn .= htmlentities($this->LgnTacheValid["titre_tache_parent"]).PHP_EOL ;
						$ctn .= ' <a href="?appelleScript=detailTacheValid&id='.urlencode($this->LgnTacheValid["id_tache_parent"]).'" class="btn btn-primary" target="_top"><i class="fa fa-info fa-fw"></i> D&eacute;tails</a>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '<br/>' ;
					}
					if($this->LgnTacheValid["est_tache_finale"] == 0)
					{
						$ctn .= '<div class="container-fluid">'.PHP_EOL ;
						$ctn .= '<div class="row">'.PHP_EOL ;
						$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
						$ctn .= '<b>T&acirc;che apr&egrave;s confirmation : </b>' ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '<div class="row">'.PHP_EOL ;
						$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
						if($this->LgnTacheValid["id_tache_confirm"] > 0)
						{
							$ctn .= htmlentities($this->LgnTacheValid["titre_tache_confirm"]).PHP_EOL ;
							$ctn .= ' <a href="?appelleScript=detailTacheValid&id='.urlencode($this->LgnTacheValid["id_tache_confirm"]).'" class="btn btn-primary" target="_top"><i class="fa fa-info fa-fw"></i> D&eacute;tails</a>'.PHP_EOL ;
						}
						else
						{
							$ctn .= '(Aucune) ' ;
							$ctn .= '<a href="'.$this->ObtientUrlParam(array("soumetRequete" => "cree_tache_confirm", "id" => $this->LgnTacheValid["id"])).'" class="btn btn-primary">Cr&eacute;er</a> <a href="'.$this->ObtientUrlParam(array("soumetRequete" => "finalise_tache_confirm", "id" => $this->LgnTacheValid["id"])).'" class="btn btn-success">Finaliser</a>'.PHP_EOL ;
						}
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>'.PHP_EOL ;
						$ctn .= '</div>' ;
						/*
						if($this->LgnTacheValid["est_tache_initiale"] == 0)
						{
							// Rejet
							$ctn .= '<br/>' ;
							$ctn .= '<div class="container-fluid">'.PHP_EOL ;
							$ctn .= '<div class="row">'.PHP_EOL ;
							$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
							$ctn .= '<b>T&acirc;che apr&egrave;s rejet : </b>' ;
							$ctn .= '</div>'.PHP_EOL ;
							$ctn .= '</div>'.PHP_EOL ;
							$ctn .= '<div class="row">'.PHP_EOL ;
							$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
							if($this->LgnTacheValid["id_tache_rejet"] > 0)
							{
								$ctn .= htmlentities($this->LgnTacheValid["titre_tache_rejet"]).PHP_EOL ;
								$ctn .= ' <a href="?appelleScript=detailTacheValid&id='.urlencode($this->LgnTacheValid["id_tache_rejet"]).'" class="btn btn-primary" target="_top"><i class="fa fa-info fa-fw"></i> D&eacute;tails</a>'.PHP_EOL ;
							}
							else
							{
								$ctn .= '(Aucune) ' ;
								$ctn .= '<a href="'.$this->ObtientUrlParam(array("soumetRequete" => "cree_tache_rejet", "id" => $this->LgnTacheValid["id"])).'" class="btn btn-primary">Cr&eacute;er</a> <a href="'.$this->ObtientUrlParam(array("soumetRequete" => "finalise_tache_rejet", "id" => $this->LgnTacheValid["id"])).'" class="btn btn-success">Finaliser</a>'.PHP_EOL ;
							}
							$ctn .= '</div>'.PHP_EOL ;
							$ctn .= '</div>'.PHP_EOL ;
							$ctn .= '</div>' ;
						}
						*/
					}
				}
				return $ctn ;
			}
		}
		class ScriptActionsTacheValidUnafdt extends ScriptCadreTacheValidUnafdt
		{
			protected function ChargeFltsEditPrinc()
			{
			}
		}
	}
	
?>