<?php
	
	if(! defined('ZONE_UNAFDT'))
	{
		if(! defined('COMPOSANT_BASE_UNAFDT'))
		{
			include dirname(__FILE__)."/Composant.class.php" ;
		}
		if(! defined('DOCUMENT_WEB_BASE_UNAFDT'))
		{
			include dirname(__FILE__)."/DocumentWeb.class.php" ;
		}
		if(! defined('ACTION_WEB_UNAFDT'))
		{
			include dirname(__FILE__)."/Action.class.php" ;
		}
		if(! defined('SCRIPT_BASE_UNAFDT'))
		{
			include dirname(__FILE__)."/Script.class.php" ;
		}
		define('ZONE_UNAFDT', 1) ;
		
		class ZonePrincUnafdt extends PvZoneSbAdmin2
		{
			public $CheminFichierRelatif = CHEMIN_FIC_REL_ZONE_PRINC_UNAFDT ;
			// public $EncodageDocument = "windows-1252" ;
			public $NomClasseMembership = "MembershipUnafdt" ;
			public $NomClasseRemplisseurConfigMembership = "RemplCfgMembershipUnafdt" ;
			public $TitreNonConnecte = '<span href="?" class="logo">SODETEK <span class="lite">Inside</span></span>' ;
			public $Titre = '<span class="logo">SODETEK <span class="lite">Inside</span></span>' ;
			public $PrivilegesPassePartout = array("super_admin") ;
			public $PrivilegesEditMembership = array("gestion_membre") ;
			public $CheminCSSBootstrap = "css/bootstrap-theme.min.css" ;
			public $NomClasseScriptConnexion = "ScriptConnexionUnafdt" ;
			public function AjusteCSS()
			{
				$this->InscritContenuCSS(".navbar-top-links *
{
color:white ;
}
.nav .dropdown-menu *
{
color:black ;
}
.sidebar ul li a
{
color:black ;
}
.navbar-default .active, .navbar-default a:hover, .sidebar .in a {
color:black ;
}") ;
			}
			protected function CreeDocWebNonConnecte()
			{
				return new DocWebNonConnecteUnafdt() ;
			}
			protected function CreeDocWebConnecte()
			{
				return new DocWebConnecteUnafdt() ;
			}
			protected function CreeDocWebCadre()
			{
				return new DocWebCadreUnafdt() ;
			}
			protected function ChargeDocumentsWeb()
			{
				parent::ChargeDocumentsWeb() ;
				$this->DocumentsWeb["cadre2"] = new DocWebCadre2Unafdt() ;
			}
			protected function ChargeScripts()
			{
				$this->ActionAlerteEtapeValid = $this->InsereActionPrinc("alerteEtapeValid", new ActionAlerteEtapeValidUnafdt()) ;
				$this->ActionAlerteEtapeTerminee = $this->InsereActionPrinc("alerteEtapeTerminee", new ActionAlerteEtapeTermineeUnafdt()) ;
				$this->InsereScriptParDefaut(new ScriptAccueilUnafdt()) ;
				$this->InsereScript("listeTachesValid", new ScriptListeTachesValidUnafdt()) ;
				$this->InsereScript("ajoutTacheValid", new ScriptAjoutTacheValidUnafdt()) ;
				$this->InsereScript("modifTacheValid", new ScriptModifTacheValidUnafdt()) ;
				$this->InsereScript("supprTacheValid", new ScriptSupprTacheValidUnafdt()) ;
				$this->InsereScript("detailTacheValid", new ScriptDetailTacheValidUnafdt()) ;
				$this->InsereScript("acteurTacheValid", new ScriptActeurTacheValidUnafdt()) ;
				$this->InsereScript("suiviTacheValid", new ScriptSuiviTacheValidUnafdt()) ;
				$this->InsereScript("choixProcessValid", new ScriptChoixProcessValidUnafdt()) ;
				$this->InsereScript("creeProcessValid", new ScriptCreeProcessValidUnafdt()) ;
				$this->InsereScript("suiviProcessValid", new ScriptSuiviProcessValidUnafdt()) ;
				$this->InsereScript("detailEtapeValid", new ScriptDetailEtapeValidUnafdt()) ;
				$this->InsereScript("listeEtapesValid", new ScriptListeEtapesValidUnafdt()) ;
				$this->InsereScript("traiteEtapeValid", new ScriptTraiteEtapeValidUnafdt()) ;
				$this->InsereScript("suiviEtapesValid", new ScriptSuiviEtapesValidUnafdt()) ;
				$this->InsereScript("avisEtapesValid", new ScriptAvisEtapesValidUnafdt()) ;
				$this->InsereScript("statPrdEtapeValid", new ScriptStatLstPrdEtapeValidUnafdt()) ;
				$this->InsereScript("det1PrdEtapeValid", new ScriptDet1PrdEtapeValidUnafdt()) ;
				$this->InsereScript("det2PrdEtapeValid", new ScriptDet2PrdEtapeValidUnafdt()) ;
				$this->InsereScript("det3PrdEtapeValid", new ScriptDet3PrdEtapeValidUnafdt()) ;
				$this->InsereScript("listeDirections", new ScriptListeDirectUnafdt()) ;
				$this->InsereScript("ajoutDirection", new ScriptAjoutDirectUnafdt()) ;
				$this->InsereScript("modifDirection", new ScriptModifDirectUnafdt()) ;
				$this->InsereScript("supprDirection", new ScriptSupprDirectUnafdt()) ;
				$this->InsereScript("listeDepartements", new ScriptListeDepartUnafdt()) ;
				$this->InsereScript("ajoutDepartement", new ScriptAjoutDepartUnafdt()) ;
				$this->InsereScript("modifDepartement", new ScriptModifDepartUnafdt()) ;
				$this->InsereScript("supprDepartement", new ScriptSupprDepartUnafdt()) ;
				$this->InsereScript("listeServices", new ScriptListeServiceUnafdt()) ;
				$this->InsereScript("ajoutService", new ScriptAjoutServiceUnafdt()) ;
				$this->InsereScript("modifService", new ScriptModifServiceUnafdt()) ;
				$this->InsereScript("supprService", new ScriptSupprServiceUnafdt()) ;
				// $this->InsereScript("actionsTacheValid", new ScriptActionsTacheValidUnafdt()) ;
			}
		}
	}
	
?>