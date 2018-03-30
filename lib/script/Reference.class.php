<?php

	if(! defined('SCRIPT_REFERENCE_UNAFDT'))
	{
		define('SCRIPT_REFERENCE_UNAFDT', 1) ;
		
		class ScriptListeDirectUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Liste des directions" ;
			public $Titre = "Liste des directions" ;
			protected $ReqSelectTablPrinc = "fluxtaf_direction" ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			protected function ChargeTablPrinc()
			{
				$this->TablPrinc->ToujoursAfficher = 1 ;
				$this->FltNom = $this->TablPrinc->InsereFltSelectHttpGet("nom", "instr(nom, <self>) > 0") ;
				$this->FltNom->Libelle = "Nom" ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColNom = $this->TablPrinc->InsereDefCol("nom", "Nom") ;
				$this->CmdAjout = $this->TablPrinc->InsereCmdRedirectUrl("cmdAjout", "?appelleScript=ajoutDirection", '<i class="fa fa-plus"></i> Ajouter') ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienModif = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=modifDirection&id=${id}', '<i class="fa fa-pencil" title="Modifier"></i>') ;
				$this->LienSuppr = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=supprDirection&id=${id}', '<i class="fa fa-minus" title="Supprimer"></i>') ;
			}
		}
		
		class ScriptEditDirectUnafdt extends ScriptEditBaseUnafdt
		{
			protected $ReqSelectFormPrinc = "fluxtaf_direction" ;
			protected $TablEditFormPrinc = "fluxtaf_direction" ;
			protected $UrlRedirectAnnulFormPrinc = "?appelleScript=listeDirections" ;
			protected function ChargeFormPrinc()
			{
				$this->FltId = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				$this->FltNom = $this->FormPrinc->InsereFltEditHttpPost("nom", "nom") ;
				$this->FltNom->Libelle = "Nom" ;
			}
		}
		class ScriptAjoutDirectUnafdt extends ScriptEditDirectUnafdt
		{
			public $PourAjout = 1 ;
			public $TitreDocument = "Ajouter une direction" ;
			public $Titre = "Ajouter une direction" ;
			protected $NomClasseCmdExecFormPrinc = "CmdAjoutElementUnafdt" ;
		}
		class ScriptModifDirectUnafdt extends ScriptEditDirectUnafdt
		{
			public $PourAjout = 0 ;
			public $TitreDocument = "Modifier la direction" ;
			public $Titre = "Modifier la direction" ;
			protected $NomClasseCmdExecFormPrinc = "CmdModifElementUnafdt" ;
		}
		class ScriptSupprDirectUnafdt extends ScriptEditDirectUnafdt
		{
			public $PourAjout = 0 ;
			public $FormPrincEditable = 0 ;
			public $TitreDocument = "Supprimer la direction" ;
			public $Titre = "Supprimer la direction" ;
			protected $NomClasseCmdExecFormPrinc = "CmdSupprElementUnafdt" ;
		}
		
		class ScriptListeDepartUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Liste des departements" ;
			public $Titre = "Liste des departements" ;
			protected $ReqSelectTablPrinc = "(select t1.*, t2.nom nom_direction from fluxtaf_departement t1 left join fluxtaf_direction t2 on t1.id_direction = t2.id)" ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			protected function ChargeTablPrinc()
			{
				$this->TablPrinc->ToujoursAfficher = 1 ;
				$this->FltNom = $this->TablPrinc->InsereFltSelectHttpGet("nom", "instr(nom, <self>) > 0") ;
				$this->FltNom->Libelle = "Nom" ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColNom = $this->TablPrinc->InsereDefCol("nom", "Nom") ;
				$this->DefColDepart = $this->TablPrinc->InsereDefCol("nom_direction", "Direction") ;
				$this->CmdAjout = $this->TablPrinc->InsereCmdRedirectUrl("cmdAjout", "?appelleScript=ajoutDepartement", '<i class="fa fa-plus"></i> Ajouter') ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienModif = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=modifDepartement&id=${id}', '<i class="fa fa-pencil" title="Modifier"></i>') ;
				$this->LienSuppr = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=supprDepartement&id=${id}', '<i class="fa fa-minus" title="Supprimer"></i>') ;
			}
		}
		
		class ScriptEditDepartUnafdt extends ScriptEditBaseUnafdt
		{
			protected $ReqSelectFormPrinc = "fluxtaf_departement" ;
			protected $TablEditFormPrinc = "fluxtaf_departement" ;
			protected $UrlRedirectAnnulFormPrinc = "?appelleScript=listeDepartements" ;
			protected function ChargeFormPrinc()
			{
				$this->FltId = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				$this->FltNom = $this->FormPrinc->InsereFltEditHttpPost("nom", "nom") ;
				$this->FltNom->Libelle = "Nom" ;
				$this->FltDirect = $this->FormPrinc->InsereFltEditHttpPost("id_direction", "id_direction") ;
				$this->FltDirect->Libelle = "Direction" ;
				$this->CompDirect = $this->FltDirect->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompDirect->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompDirect->FournisseurDonnees->RequeteSelection = "fluxtaf_direction" ;
				$this->CompDirect->NomColonneValeur = "id" ;
				$this->CompDirect->NomColonneLibelle = "nom" ;
			}
		}
		class ScriptAjoutDepartUnafdt extends ScriptEditDepartUnafdt
		{
			public $PourAjout = 1 ;
			public $TitreDocument = "Ajouter un departement" ;
			public $Titre = "Ajouter un departement" ;
			protected $NomClasseCmdExecFormPrinc = "CmdAjoutElementUnafdt" ;
		}
		class ScriptModifDepartUnafdt extends ScriptEditDepartUnafdt
		{
			public $PourAjout = 0 ;
			public $TitreDocument = "Modifier le departement" ;
			public $Titre = "Modifier le departement" ;
			protected $NomClasseCmdExecFormPrinc = "CmdModifElementUnafdt" ;
		}
		class ScriptSupprDepartUnafdt extends ScriptEditDepartUnafdt
		{
			public $PourAjout = 0 ;
			public $FormPrincEditable = 0 ;
			public $TitreDocument = "Supprimer le departement" ;
			public $Titre = "Supprimer le departement" ;
			protected $NomClasseCmdExecFormPrinc = "CmdSupprElementUnafdt" ;
		}
		
		
		class ScriptListeServiceUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Liste des services" ;
			public $Titre = "Liste des services" ;
			protected $ReqSelectTablPrinc = "(select t1.*, t2.nom nom_departement, t2.id_direction, t3.nom nom_direction
from fluxtaf_service t1 left join fluxtaf_departement t2 on t1.id_departement = t2.id
left join fluxtaf_direction t3 on t2.id_direction = t3.id)" ;
			protected $InscrireDefColActsTablPrinc = 1 ;
			protected function ChargeTablPrinc()
			{
				$this->TablPrinc->ToujoursAfficher = 1 ;
				$this->FltNom = $this->TablPrinc->InsereFltSelectHttpGet("nom", "instr(nom, <self>) > 0") ;
				$this->FltNom->Libelle = "Nom" ;
				$this->FltIdDepart = $this->TablPrinc->InsereFltSelectHttpGet("id_departement", "id_departement=<self>") ;
				$this->FltIdDepart->Libelle = "Departement" ;
				$this->ValeurIdDirect = _GET_def("id_direction") ;
				$this->CompIdDepart = $this->FltIdDepart->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompIdDepart->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompIdDepart->FournisseurDonnees->RequeteSelection = "(select * from fluxtaf_departement where id_direction=:idDirection)" ;
				$this->CompIdDepart->FournisseurDonnees->ParamsSelection["idDirection"] = $this->ValeurIdDirect ;
				$this->CompIdDepart->NomColonneValeur = "id" ;
				$this->CompIdDepart->NomColonneLibelle = "nom" ;
				$this->CompIdDepart->InclureElementHorsLigne = 1 ;
				$this->FltIdDirect = $this->TablPrinc->InsereFltSelectHttpGet("id_direction", "id_direction=<self>") ;
				$this->FltIdDirect->Libelle = "Direction" ;
				$this->CompIdDirect = $this->FltIdDirect->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompIdDirect->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompIdDirect->FournisseurDonnees->RequeteSelection = "fluxtaf_direction" ;
				$this->CompIdDirect->NomColonneValeur = "id" ;
				$this->CompIdDirect->NomColonneLibelle = "nom" ;
				$this->CompIdDirect->AttrsSupplHtml["onchange"] = 'ActualiseFormulaire'.$this->TablPrinc->IDInstanceCalc.'()' ;
				$this->CompIdDirect->InclureElementHorsLigne = 1 ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColIdDirect = $this->TablPrinc->InsereDefColCachee("id_direction") ;
				$this->DefColIdDepart = $this->TablPrinc->InsereDefColCachee("id_departement") ;
				$this->DefColNom = $this->TablPrinc->InsereDefCol("nom", "Nom") ;
				$this->DefColDepart = $this->TablPrinc->InsereDefCol("nom_departement", "Departement") ;
				$this->DefColDirect = $this->TablPrinc->InsereDefCol("nom_direction", "Direction") ;
				$this->CmdAjout = $this->TablPrinc->InsereCmdRedirectUrl("cmdAjout", "?appelleScript=ajoutService", '<i class="fa fa-plus"></i> Ajouter') ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
				$this->LienModif = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=modifService&id=${id}', '<i class="fa fa-pencil" title="Modifier"></i>') ;
				$this->LienModif = $this->TablPrinc->InsereLienAction($this->DefColActsTablPrinc, '?appelleScript=supprService&id=${id}', '<i class="fa fa-minus" title="Supprimer"></i>') ;
			}
		}
		
		class ScriptEditServiceUnafdt extends ScriptEditBaseUnafdt
		{
			protected $ReqSelectFormPrinc = "fluxtaf_service" ;
			protected $TablEditFormPrinc = "fluxtaf_service" ;
			protected $UrlRedirectAnnulFormPrinc = "?appelleScript=listeServices" ;
			protected function ChargeFormPrinc()
			{
				$this->FltId = $this->FormPrinc->InsereFltLgSelectHttpGet("id", "id = <self>") ;
				$this->FltNom = $this->FormPrinc->InsereFltEditHttpPost("nom", "nom") ;
				$this->FltNom->Libelle = "Nom" ;
				$this->FltDepart = $this->FormPrinc->InsereFltEditHttpPost("id_departement", "id_departement") ;
				$this->FltDepart->Libelle = "Departement" ;
				$this->CompDepart = $this->FltDepart->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompDepart->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompDepart->FournisseurDonnees->RequeteSelection = "(select t1.*, t2.nom nom_direction, concat(t1.nom, ' / ', t2.nom) chemin from fluxtaf_departement t1 left join fluxtaf_direction t2 on t1.id_direction = t2.id)" ;
				$this->CompDepart->NomColonneValeur = "id" ;
				$this->CompDepart->NomColonneLibelle = "chemin" ;
				$this->CompDepart->RechercheParDebut = 0 ;
				if($this->PourAjout == 1)
				{
					$this->FltDepart->ValeurParDefaut = 1 ;
				}
				else
				{
					// $this->CompDepart->
				}
			}
		}
		class ScriptAjoutServiceUnafdt extends ScriptEditServiceUnafdt
		{
			public $PourAjout = 1 ;
			public $TitreDocument = "Ajouter un service" ;
			public $Titre = "Ajouter un service" ;
			protected $NomClasseCmdExecFormPrinc = "CmdAjoutElementUnafdt" ;
		}
		class ScriptModifServiceUnafdt extends ScriptEditServiceUnafdt
		{
			public $PourAjout = 0 ;
			public $TitreDocument = "Modifier le service" ;
			public $Titre = "Modifier le service" ;
			protected $NomClasseCmdExecFormPrinc = "CmdModifElementUnafdt" ;
		}
		class ScriptSupprServiceUnafdt extends ScriptEditServiceUnafdt
		{
			public $PourAjout = 0 ;
			public $FormPrincEditable = 0 ;
			public $TitreDocument = "Supprimer le service" ;
			public $Titre = "Supprimer le service" ;
			protected $NomClasseCmdExecFormPrinc = "CmdSupprElementUnafdt" ;
		}
	}

?>