<?php
	
	if(! defined("DOCUMENT_WEB_UNAFDT"))
	{
		define("DOCUMENT_WEB_UNAFDT", 1) ;
		
		class DocWebNonConnecteUnafdt extends PvDocWebNonConnecteSbAdmin2
		{
			public function PrepareRendu(& $zone)
			{
				parent::PrepareRendu($zone) ;
				$zone->InscritLienCSS("css/admin.css") ;
				$zone->AjusteCSS() ;
			}
			public function RenduEntete(& $zone)
			{
				$scriptRendu = & $zone->ScriptPourRendu ;
				$ctn = $this->RenduEnteteHtmlSimple($zone).PHP_EOL ;
				$ctn .= '<div class="container">
<div class="row">
<div class="col-md-4 col-md-offset-4">
<div class="login-panel panel panel-default">'.PHP_EOL ;
				$ctn .= '<p>'.$zone->TitreNonConnecte.'</p>'.PHP_EOL ;
				$ctn .= '<div class="panel-heading">
<h3 class="panel-title">'.$scriptRendu->Titre.'</h3>
</div>'.PHP_EOL ;
				$ctn .= '<div class="panel-body">'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduPied(& $zone)
			{
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>
</div>
</div>
</div>'.PHP_EOL ;
				$ctn = $this->RenduPiedHtmlSimple($zone).PHP_EOL ;
				return $ctn ;
			}
		}
		
		class SideBarUnafdt extends PvSidebarSbAdmin2
		{
			protected function ChargeSousMenusSpec()
			{
				if($this->InclureMenuAccueil == 1)
				{
					$this->SousMenuAccueil = $this->MenuRacine->InscritSousMenuUrl($this->LibelleMenuAccueil, "?") ;
					$this->DefClasseFa($this->SousMenuAccueil, "fa-dashboard") ;
				}
				if($this->InclureMenuMembership == 1 && $this->ZoneParent->PossedePrivileges($this->ZoneParent->PrivilegesEditMembership))
				{
					$this->SousMenuMembership = $this->MenuRacine->InscritSousMenuFige("membership", $this->LibelleMenuMembership) ;
					$this->DefClasseFa($this->SousMenuMembership, "fa-lock") ;
					// Ajouts
					$this->SousMenuListeDirect = $this->SousMenuMembership->InscritSousMenuScript("listeDirections") ;
					$this->SousMenuListeDirect->Titre = "Directions" ;
					$this->DefClasseFa($this->SousMenuListeDirect, "fa-sitemap") ;
					$this->SousMenuAjoutDirect = $this->SousMenuMembership->InscritSousMenuScript("ajoutDirection") ;
					$this->SousMenuAjoutDirect->Titre = "Ajout direction" ;
					$this->DefClasseFa($this->SousMenuAjoutDirect, "fa-plus") ;
					$this->SousMenuListeDepart = $this->SousMenuMembership->InscritSousMenuScript("listeDepartements") ;
					$this->SousMenuListeDepart->Titre = "Departements" ;
					$this->DefClasseFa($this->SousMenuListeDepart, "fa-sitemap") ;
					$this->SousMenuAjoutDepart = $this->SousMenuMembership->InscritSousMenuScript("ajoutDepartement") ;
					$this->SousMenuAjoutDepart->Titre = "Ajout departement" ;
					$this->DefClasseFa($this->SousMenuAjoutDepart, "fa-plus") ;
					$this->SousMenuListeService = $this->SousMenuMembership->InscritSousMenuScript("listeServices") ;
					$this->SousMenuListeService->Titre = "Services" ;
					$this->DefClasseFa($this->SousMenuListeService, "fa-suitcase") ;
					$this->SousMenuAjoutService = $this->SousMenuMembership->InscritSousMenuScript("ajoutService") ;
					$this->SousMenuAjoutService->Titre = "Ajout service" ;
					$this->DefClasseFa($this->SousMenuAjoutService, "fa-plus") ;
					// Fin
					$this->SousMenuListeMembres = $this->SousMenuMembership->InscritSousMenuScript($this->ZoneParent->NomScriptListeMembres) ;
					$this->SousMenuListeMembres->Titre = $this->LibelleMenuListeMembres ;
					$this->DefClasseFa($this->SousMenuListeMembres, "fa-users") ;
					$this->SousMenuAjoutMembre = $this->SousMenuMembership->InscritSousMenuScript($this->ZoneParent->NomScriptAjoutMembre) ;
					$this->SousMenuAjoutMembre->Titre = $this->LibelleMenuAjoutMembre ;
					$this->DefClasseFa($this->SousMenuAjoutMembre, "fa-user-plus") ;
					$this->SousMenuListeProfils = $this->SousMenuMembership->InscritSousMenuScript($this->ZoneParent->NomScriptListeProfils) ;
					$this->SousMenuListeProfils->Titre = $this->LibelleMenuListeProfils ;
					$this->DefClasseFa($this->SousMenuListeProfils, "fa-list") ;
					$this->SousMenuAjoutProfil = $this->SousMenuMembership->InscritSousMenuScript($this->ZoneParent->NomScriptAjoutProfil) ;
					$this->DefClasseFa($this->SousMenuAjoutProfil, "fa-plus") ;
					$this->SousMenuAjoutProfil->Titre = $this->LibelleMenuAjoutProfil ;
					$this->SousMenuListeRoles = $this->SousMenuMembership->InscritSousMenuScript($this->ZoneParent->NomScriptListeRoles) ;
					$this->DefClasseFa($this->SousMenuListeRoles, "fa-unlock") ;
					$this->SousMenuListeRoles->Titre = $this->LibelleMenuListeRoles ;
					$this->SousMenuAjoutRole = $this->SousMenuMembership->InscritSousMenuScript($this->ZoneParent->NomScriptAjoutRole) ;
					$this->DefClasseFa($this->SousMenuAjoutRole, "fa-plus") ;
					$this->SousMenuAjoutRole->Titre = $this->LibelleMenuAjoutRole ;
				}
			}
			protected function RenduMenuRacine(& $menu)
			{
				$ctn = '' ;
				$ctn .= '<div class="'.$this->ClasseCSSRacine.' sidebar" role="navigation">
<div class="sidebar-nav navbar-collapse">
<ul class="nav" id="side-menu">'.PHP_EOL ;
				if($this->InclureBarreRecherche == 1 && $this->NomScriptRecherche != "")
				{
					$ctn .= '<li class="sidebar-search">
<form action="?" method="get">
<input type="hidden" name="'.htmlspecialchars($this->ZoneParent->NomParamScriptAppele).'" value="'.htmlspecialchars($this->NomScriptRecherche).'" />
<div class="input-group custom-search-form">
<input type="text" class="form-control" placeholder="'.htmlspecialchars($this->LibelleRecherche).'" name="'.htmlspecialchars($this->NomParamTermeRech).'" />
<span class="input-group-btn">
<button class="btn btn-default" type="submit">
<i class="fa fa-search"></i>
</button>
</span>
</div>
</li>'.PHP_EOL ;
				}
				$sousMenus = $menu->SousMenusAffichables() ;
				foreach($sousMenus as $i => $sousMenu)
				{
					$ctn .= '<li>'.PHP_EOL ;
					$ctn .= $this->RenduMenuNv1($sousMenu).PHP_EOL ;
					$ctn .= '</li>'.PHP_EOL ;
				}
				$ctn .= '</ul>' ;
				$ctn .= '</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class DocWebConnecteUnafdt extends PvDocWebConnecteSbAdmin2
		{
			public $SousMenuTachsValid ;
			public $SousMenuProcsValid ;
			protected function DetermineComposants(& $zone)
			{
				$this->Navbar1 = new PvNavbarStaticTopSbAdmin2() ;
				$this->Navbar1->AdopteScript("navbar1", $zone->ScriptPourRendu) ;
				$this->Navbar1->ChargeConfig() ;
				$this->Navbar2 = new PvNavbarTopLinksSbAdmin2() ;
				$this->Navbar2->AdopteScript("navbar2", $zone->ScriptPourRendu) ;
				$this->Navbar2->ChargeConfig() ;
				$this->Sidebar1 = new SideBarUnafdt() ;
				$this->Sidebar1->AdopteScript("sidebar1", $zone->ScriptPourRendu) ;
				$this->Sidebar1->ChargeConfig() ;
				$this->Sidebar1->InclureIconesMenuNv2 = 1 ;
				$this->Sidebar1->ClasseCSSRacine = 'navbar-inverse' ;
				$this->SousMenuValids = $this->Sidebar1->MenuRacine->InscritSousMenuUrl("Validations des demandes", "?appelleScript=listeTachesValid") ;
				$this->Sidebar1->DefClasseFa($this->SousMenuValids, "fa-anchor") ;
				if($zone->PossedePrivilege("gestion_circuit_valid"))
				{
					$this->SousMenuTachsValid = $this->SousMenuValids->InscritSousMenuUrl("Circuits de validation", "?appelleScript=listeTachesValid") ;
					$this->Sidebar1->DefClasseFa($this->SousMenuTachsValid, "fa-wrench") ;
				}
				if($zone->PossedePrivilege("poster_formulaire"))
				{
					$this->SousMenuProcsValid = $this->SousMenuValids->InscritSousMenuUrl("Soumettre un formulaire", "?appelleScript=choixProcessValid") ;
					$this->Sidebar1->DefClasseFa($this->SousMenuProcsValid, "fa-plug") ;
				}
				if($zone->PossedePrivileges(array("poster_formulaire", "consultation_formulaire")))
				{
					$this->SousMenuSuivi = $this->SousMenuValids->InscritSousMenuUrl("Suivi des demandes", "?appelleScript=suiviProcessValid") ;
					$this->Sidebar1->DefClasseFa($this->SousMenuSuivi, "fa-question") ;
				}
				if($zone->PossedePrivilege("validation_formulaire"))
				{
					$this->SousMenuValidAttente = $this->SousMenuValids->InscritSousMenuUrl("Validations en attente", "?appelleScript=listeEtapesValid") ;
					$this->Sidebar1->DefClasseFa($this->SousMenuValidAttente, "fa-check-square-o") ;
				}
				if($zone->PossedePrivileges(array("validation_formulaire")))
				{
					$titreMenu = ($zone->PossedePrivilege("poster_formulaire")) ? "Formulaires valid&eacute;s" : "Validations effectu&eacute;es" ;
					$this->SousMenuValidsEffect = $this->SousMenuValids->InscritSousMenuUrl($titreMenu, "?appelleScript=avisEtapesValid") ;
					$this->Sidebar1->DefClasseFa($this->SousMenuValidsEffect, "fa-check-square") ;
				}
				if($zone->PossedePrivileges(array("validation_formulaire", "stats")))
				{
					$this->SousMenuStats = $this->SousMenuValids->InscritSousMenuUrl("Statistiques validation", "?appelleScript=statPrdEtapeValid") ;
					$this->Sidebar1->DefClasseFa($this->SousMenuStats, "fa-bar-chart") ;
				}
			}
			public function PrepareRendu(& $zone)
			{
				parent::PrepareRendu($zone) ;
				$zone->InscritLienCSS("css/admin.css") ;
				$zone->AjusteCSS() ;
			}
			public function RenduEntete(& $zone)
			{
				$scriptRendu = & $zone->ScriptPourRendu ;
				$ctn = $this->RenduEnteteHtmlSimple($zone).PHP_EOL ;
				$ctn .= '<div id="wrapper">'.PHP_EOL ;
				$ctn .= '<nav class="navbar '.$this->ClasseCSSNavbar.' navbar-static-top" role="navigation" style="margin-bottom: 0">'.PHP_EOL ;
				$ctn .= $this->Navbar1->RenduDispositif() ;
				$ctn .= $this->Navbar2->RenduDispositif() ;
				$ctn .= $this->Sidebar1->RenduDispositif() ;
				$ctn .= '</nav>'.PHP_EOL ;
				$ctn .= '<div id="page-wrapper">
<div class="row">
<div class="col-lg-12">
<h1 class="page-header">'.$scriptRendu->Titre.'</h1>
</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class DocWebCadreUnafdt extends PvDocWebCadreSbAdmin2
		{
			public function PrepareRendu(& $zone)
			{
				parent::PrepareRendu($zone) ;
				$zone->AjusteCSS() ;
			}
		}
		
		class DocWebCadre2Unafdt extends DocWebCadreUnafdt
		{
			public function RenduEntete(& $zone)
			{
				$ctn = parent::RenduEntete($zone) ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-xs-12">'.PHP_EOL ;
				$ctn .= '<br>'.PHP_EOL ;
				return $ctn ;
			}
			public function RenduPied(& $zone)
			{
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn .= '</div>'.PHP_EOL ;
				$ctn = parent::RenduPied($zone).PHP_EOL ;
				return $ctn ;
			}
		}
	}
	
?>