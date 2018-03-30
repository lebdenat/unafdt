<?php
	
	if(! defined('SCRIPT_MEMBERSHIP_UNAFDT'))
	{
		define('SCRIPT_MEMBERSHIP_UNAFDT', 1) ;
		
		class RemplCfgMembershipUnafdt extends PvRemplisseurConfigMembershipSimple
		{
			public $FltIdDirect ;
			public $FltIdDepart ;
			public $FltIdService ;
			public $FltIdCateg ;
			public $FltIdPoste ;
			public $FltMatr ;
			public function & CreeSelectBox(& $flt, $sql, $nomColValeur="", $nomColLibelle="")
			{
				$comp = $flt->DeclareComposant("PvZoneSelectHtml") ;
				$comp->FournisseurDonnees = $flt->ScriptParent->ApplicationParent->CreeFournBDPrinc() ;
				$comp->FournisseurDonnees->RequeteSelection = $sql ;
				$comp->NomColonneValeur = $nomColValeur ;
				$comp->NomColonneLibelle = $nomColLibelle ;
				return $comp ;
			}
			public function RemplitFiltresEditionFormMembre(& $form)
			{
				parent::RemplitFiltresEditionFormMembre($form) ;
				$bd = $form->ApplicationParent->CreeBDPrinc() ;
				if($form->InclureElementEnCours == 0)
				{
					$this->ValeurParamIdDepart = _POST_def("id_departement", 1) ;
					$this->ValeurParamIdService = _POST_def("id_service", 1) ;
					$this->ValeurParamIdDirect = _POST_def("id_direction", 1) ;
				}
				else
				{
					$this->LgnMembreSelect = $bd->FetchSqlRow("select * from membership_member where id=:id", array("id" => _GET_def("idMembre"))) ;
					$this->ValeurParamIdDepart = _POST_def("id_departement", $this->LgnMembreSelect["id_departement"]) ;
					$this->ValeurParamIdService = _POST_def("id_service", $this->LgnMembreSelect["id_service"]) ;
					$this->ValeurParamIdDirect = _POST_def("id_direction", $this->LgnMembreSelect["id_direction"]) ;
				}
				$form->DessinateurFiltresEdition = new DessinFltsMembreUnafdt() ;
				$this->FltIdDirect = $form->InsereFltEditHttpPost("id_direction", "id_direction") ;
				$this->FltIdDirect->Libelle = "Direction" ;
				$this->CompIdDirect = $this->CreeSelectBox($this->FltIdDirect, "(select * from fluxtaf_direction where id > 0)", "id", "nom") ;
				$this->CompIdDirect->AttrsSupplHtml["onchange"] = 'ActualiseFormulaire'.$form->IDInstanceCalc.'()' ;
				$this->FltIdDepart = $form->InsereFltEditHttpPost("id_departement", "id_departement") ;
				$this->FltIdDepart->Libelle = "D&eacute;partement" ;
				$this->CompIdDepart = $this->CreeSelectBox($this->FltIdDepart, "(select * from fluxtaf_departement where id_direction = :idDirection)", "id", "nom") ;
				$this->CompIdDepart->FournisseurDonnees->ParamsSelection["idDirection"] = $this->ValeurParamIdDirect ;
				$this->CompIdDepart->AttrsSupplHtml["onchange"] = 'ActualiseFormulaire'.$form->IDInstanceCalc.'()' ;
				$this->FltIdService = $form->InsereFltEditHttpPost("id_service", "id_service") ;
				$this->FltIdService->Libelle = "Service" ;
				$this->CompIdService = $this->CreeSelectBox($this->FltIdService, "(select * from fluxtaf_service where id_departement = :idDepartement)", "id", "nom") ;
				$this->CompIdService->FournisseurDonnees->ParamsSelection["idDepartement"] = $this->ValeurParamIdDepart ;
				$this->FltIdCategorie = $form->InsereFltEditHttpPost("id_categorie", "id_categorie") ;
				$this->FltIdCategorie->Libelle = "Categorie" ;
				$this->CompIdCategorie = $this->CreeSelectBox($this->FltIdCategorie, "fluxtaf_categorie", "id", "nom") ;
				$this->FltIdPoste = $form->InsereFltEditHttpPost("id_poste", "id_poste") ;
				$this->FltIdPoste->Libelle = "Poste" ;
				$this->CompIdPoste = $this->CreeSelectBox($this->FltIdPoste, "fluxtaf_poste", "id", "nom") ;
				$this->CompIdPoste->AttrsSupplHtml["onchange"] = "ActualiseFormulaire".$form->IDInstanceCalc."()" ;
				$this->FltMatr = $form->InsereFltEditHttpPost("matricule", "matricule") ;
				$this->FltMatr->Libelle = "Matricule" ;
				$this->FltDateEmbauch = $form->InsereFltEditHttpPost("date_embauche", "date_embauche") ;
				$this->FltDateEmbauch->Libelle = "Date embauche" ;
				$this->FltDateEmbauch->DeclareComposant("PvCalendarDateInput") ;
				$this->ValeurParamPosteSelect = _POST_def("id_poste", 1) ;
				$this->LgnPosteSelect = $bd->FetchSqlRow("select * from fluxtaf_poste where id=:id", array("id" => $this->ValeurParamPosteSelect)) ;
				if(! is_array($this->LgnPosteSelect) || count($this->LgnPosteSelect) == 0)
				{
					$form->Visible = false ;
					return ;
				}
				switch($this->LgnPosteSelect["id_type_affectation"])
				{
					case 2 : {
						$this->FltIdService->EstEtiquette = 1 ;
						$this->FltIdService->ValeurParDefaut = 0 ;
						$this->FltIdService->NePasLierParametre = 1 ;
						$this->CompIdService->FournisseurDonnees->RequeteSelection = "fluxtaf_service" ;
					}
					break ;
					case 3 : {
						$this->FltIdService->EstEtiquette = 1 ;
						$this->FltIdService->ValeurParDefaut = 0 ;
						$this->CompIdService->FournisseurDonnees->RequeteSelection = "fluxtaf_service" ;
						$this->FltIdService->NePasLierParametre = 1 ;
						$this->FltIdDepart->EstEtiquette = 1 ;
						$this->FltIdDepart->ValeurParDefaut = 0 ;
						$this->FltIdDepart->NePasLierParametre = 1 ;
						$this->CompIdDepart->FournisseurDonnees->RequeteSelection = "fluxtaf_departement" ;
					}
					break ;
				}
			}
		}
		class DessinFltsMembreUnafdt extends PvDessinFiltresDonneesBootstrap
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$remplCfg = & $script->ZoneParent->RemplisseurConfigMembership ;
				$i = 0 ;
				$fltLogin = & $composant->FiltreLoginMembre ;
				$i++ ;
				$fltMotPasse = null ;
				if($form->InclureElementEnCours == 0)
				{
					$fltMotPasse = & $composant->FiltreMotPasseMembre ;
					$i++ ;
				}
				$fltNom = & $composant->FiltreNomMembre ;
				$i++ ;
				$fltPrenom = & $composant->FiltrePrenomMembre ;
				$i++ ;
				$fltEmail = & $composant->FiltreEmailMembre ;
				$i++ ;
				$fltAdresse = & $composant->FiltreAdresseMembre ;
				$i++ ;
				$fltContact = & $composant->FiltreContactMembre ;
				$i++ ;
				$fltActiver = & $composant->FiltreActiverMembre ;
				$i++ ;
				$fltProfil = & $composant->FiltreProfilMembre ;
				$i++ ;
				$ctn = '' ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($remplCfg->FltMatr).'
'.$this->RenduFiltre($remplCfg->FltMatr, $composant).PHP_EOL ;
				$ctn .= '</div>
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($remplCfg->FltIdDirect).'
'.$this->RenduFiltre($remplCfg->FltIdDirect, $composant).PHP_EOL ;
				$ctn .= '</div>
</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltNom).'
'.$this->RenduFiltre($fltNom, $composant).PHP_EOL ;
				$ctn .= '</div>
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($remplCfg->FltIdDepart).'
'.$this->RenduFiltre($remplCfg->FltIdDepart, $composant).PHP_EOL ;
				$ctn .= '</div>
</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltPrenom).'
'.$this->RenduFiltre($fltPrenom, $composant).PHP_EOL ;
				$ctn .= '</div>
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($remplCfg->FltIdService).'
'.$this->RenduFiltre($remplCfg->FltIdService, $composant).PHP_EOL ;
				$ctn .= '</div>
</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($remplCfg->FltDateEmbauch).'
'.$this->RenduFiltre($remplCfg->FltDateEmbauch, $composant).PHP_EOL ;
				$ctn .= '</div>
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltLogin).'
'.$this->RenduFiltre($fltLogin, $composant).PHP_EOL ;
				$ctn .= '</div>
</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltEmail).'
'.$this->RenduFiltre($fltEmail, $composant).PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				if($composant->InclureElementEnCours == 0)
				{
					$ctn .= '<div class="col-xs-6">
'.$this->RenduLibelleFiltre($fltMotPasse).'
'.$this->RenduFiltre($fltMotPasse, $composant).'
</div>'.PHP_EOL ;
				}
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltProfil).'
'.$this->RenduFiltre($fltProfil, $composant).PHP_EOL ;
				$ctn .= '</div>
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($remplCfg->FltIdPoste).'
'.$this->RenduFiltre($remplCfg->FltIdPoste, $composant).PHP_EOL ;
				$ctn .= '</div>
</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltAdresse).'
'.$this->RenduFiltre($fltAdresse, $composant).PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '<div class="row">
<div class="col-xs-6">'.PHP_EOL ;
				$ctn .= $this->RenduLibelleFiltre($fltActiver).'
'.$this->RenduFiltre($fltActiver, $composant).PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>' ;
				$ctn .= '<br />' ;
				return $ctn ;
			}
		}
		
		class ScriptConnexionUnafdt extends PvScriptConnexionSbAdmin2
		{
			public function RenduDispositif()
			{
				$ctn = '' ;
				$ctn .= parent::RenduDispositif() ;
				return $ctn ;
			}
		}
	}
	
?>