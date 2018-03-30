<?php
	
	if(! defined('SCRIPT_STATS_UNAFDT'))
	{
		define('SCRIPT_STATS_UNAFDT', 1) ;
		
		class ScriptStatLstPrdEtapeValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Stats - statut circuits de validation" ;
			public $Titre = "Stats - statut circuits de validation" ;
			protected $ReqSelectTablPrinc = "(select t1.id, t1.titre, case when t2.total is null then 0 else t2.total end total_en_cours, case when t3.total is null then 0 else t3.total end total_confirme, case when t4.total is null then 0 else t4.total end total_rejete
from fluxtaf_tache_valid t1
left join (
select t21.id_tache_valid, count(0) total
from fluxtaf_etape_valid t21
inner join fluxtaf_etape_valid t22 on t21.id = t22.id_etape_initiale
where t22.statut_validation = 0
and (:dateDebut <= t22.date_creation) and (:dateFin >= date(t22.date_creation))
group by t21.id_tache_valid
) t2 on t1.id = t2.id_tache_valid
left join (
select t31.id_tache_valid, count(0) total
from fluxtaf_etape_valid t31
inner join fluxtaf_etape_valid t32 on t31.id = t32.id_etape_initiale
where t32.statut_validation = 1 and t32.est_etape_finale = 1
and (:dateDebut <= t32.date_valid) and (:dateFin >= date(t32.date_valid))
group by t31.id_tache_valid
) t3 on t1.id = t3.id_tache_valid
left join (
select t41.id_tache_valid, count(0) total
from fluxtaf_etape_valid t41
inner join fluxtaf_etape_valid t42 on t41.id = t42.id_etape_initiale
where t42.statut_validation = 2 and t42.est_etape_finale = 1
and (:dateDebut <= t42.date_valid) and (:dateFin >= date(t42.date_valid))
group by t41.id_tache_valid
) t4 on t1.id = t4.id_tache_valid
where t1.id_tache_parent = 0)" ;
			protected function ChargeTablPrinc()
			{
				$this->FltDateDebut = $this->TablPrinc->InsereFltSelectHttpGet("dateDebut", "") ;
				$this->FltDateDebut->ValeurParDefaut = date("Y-m-d", date("U") - 31 * 86400) ;
				$this->FltDateDebut->Libelle = "Du" ;
				$this->FltDateDebut->DeclareComposant("PvCalendarDateInput") ;
				$this->FltDateFin = $this->TablPrinc->InsereFltSelectHttpGet("dateFin", "") ;
				$this->FltDateFin->ValeurParDefaut = date("Y-m-d") ;
				$this->FltDateFin->Libelle = "au" ;
				$this->FltDateFin->DeclareComposant("PvCalendarDateInput") ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("id") ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("total_en_cours") ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("total_confirme") ;
				$this->DefColId = $this->TablPrinc->InsereDefColCachee("total_rejete") ;
				$this->DefColTitre = $this->TablPrinc->InsereDefCol("titre", "Circuit de validation") ;
				$this->DefColEnCours = $this->TablPrinc->InsereDefColHtml('<a href="javascript:BoiteDlgUrl.ouvre(&quot;Circuits de validation en cours&quot;, &quot;?appelleScript=det1PrdEtapeValid&amp;dateDebut='.urlencode($this->FltDateDebut->Lie()).'&amp;dateFin='.urlencode($this->FltDateFin->Lie()).'&amp;idTacheValid=${id}&quot;, 750, 450) ;">${total_en_cours}</a>', 'En cours') ;
				$this->DefColEnCours->AlignEntete = "center" ;
				$this->DefColEnCours->AlignElement = "center" ;
				$this->DefColConfirm = $this->TablPrinc->InsereDefColHtml('<a href="javascript:BoiteDlgUrl.ouvre(&quot;Circuits de validation confirm&eacute;s&quot;, &quot;?appelleScript=det2PrdEtapeValid&amp;dateDebut='.urlencode($this->FltDateDebut->Lie()).'&amp;dateFin='.urlencode($this->FltDateFin->Lie()).'&amp;idTacheValid=${id}&quot;, 750, 450) ;">${total_confirme}</a>', 'Confirm&eacute;s') ;
				$this->DefColConfirm->AlignEntete = "center" ;
				$this->DefColConfirm->AlignElement = "center" ;
				$this->DefColAnnul = $this->TablPrinc->InsereDefColHtml('<a href="javascript:BoiteDlgUrl.ouvre(&quot;Circuits de validation rej&eacute;t&eacute;s&quot;, &quot;?appelleScript=det3PrdEtapeValid&amp;dateDebut='.urlencode($this->FltDateDebut->Lie()).'&amp;dateFin='.urlencode($this->FltDateFin->Lie()).'&amp;idTacheValid=${id}&quot;, 750, 450) ;">${total_rejete}</a>', 'Rejet&eacute;s') ;
				$this->DefColAnnul->AlignEntete = "center" ;
				$this->DefColAnnul->AlignElement = "center" ;
				$this->TablPrinc->FournisseurDonnees->ParamsSelection = array(
					"dateDebut" => $this->FltDateDebut->Lie(),
					"dateFin" => $this->FltDateFin->Lie(),
				) ;
			}
		}
		
		class ScriptDet1PrdEtapeValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Stats - Circuits de validation en cours" ;
			public $Titre = "Stats - Circuits de validation en cours" ;
			protected $ReqSelectTablPrinc = "(select t21.*, t22.date_creation date_demarrage_tri, t22.date_creation date_demarrage, t3.login_member login_membre_creation
from fluxtaf_etape_valid t21
inner join fluxtaf_etape_valid t22 on t21.id = t22.id_etape_initiale
left join membership_member t3 on t21.id_membre_creation = t3.id
where t22.statut_validation = 0
and (:dateDebut <= t22.date_creation) and (:dateFin >= date(t22.date_creation))
and t21.id_tache_valid = :idTacheValid)" ;
			public $NomDocumentWeb = "cadre" ;
			protected function ChargeTablPrinc()
			{
				$this->TablPrinc->FournisseurDonnees->ParamsSelection = array("dateDebut" => _GET_def("dateDebut"), "dateFin" => _GET_def("dateFin"), "idTacheValid" => _GET_def("idTacheValid")) ;
				$this->TablPrinc->SensColonneTri = "desc" ;
				$this->TablPrinc->AccepterTriColonneInvisible = 1 ;
				$this->TablPrinc->ParamsGetSoumetFormulaire = array("dateDebut", "dateFin", "idTacheValid") ;
				$this->TablPrinc->TriPossible = 0 ;
				$this->TablPrinc->InsereDefColCachee("date_demarrage_tri") ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->TablPrinc->InsereDefCol("titre", "Formulaire") ;
				$this->TablPrinc->InsereDefCol("login_membre_creation", "Initi&eacute; par") ;
				$this->TablPrinc->InsereDefColDatetimeFr("date_creation", "Initi&eacute; le") ;
				$this->TablPrinc->InsereDefColDatetimeFr("date_demarrage", "Cr&eacute;&eacute; le") ;
			}
		}
		class ScriptDet2PrdEtapeValidUnafdt extends ScriptListeBaseUnafdt
		{
			public $TitreDocument = "Stats - Circuits de validation valid&eacute;s" ;
			public $Titre = "Stats - Circuits de validation valid&eacute;s" ;
			protected $ReqSelectTablPrinc = "(select t31.*, t32.date_valid date_demarrage_tri, t32.date_valid date_demarrage, t3.login_member login_membre_creation, t4.login_member login_membre_valid
from fluxtaf_etape_valid t31
inner join fluxtaf_etape_valid t32 on t31.id = t32.id_etape_initiale
left join membership_member t3 on t31.id_membre_creation = t3.id
left join membership_member t4 on t32.id_membre_valid = t4.id
where t32.statut_validation = :statutValid and t32.est_etape_finale = 1
and (:dateDebut <= t32.date_valid) and (:dateFin >= date(t32.date_valid))
and t31.id_tache_valid = :idTacheValid)" ;
			public $NomDocumentWeb = "cadre" ;
			public $StatutValidation = 1 ;
			protected function ChargeTablPrinc()
			{
				$this->TablPrinc->FournisseurDonnees->ParamsSelection = array("dateDebut" => _GET_def("dateDebut"), "dateFin" => _GET_def("dateFin"), "idTacheValid" => _GET_def("idTacheValid"), "statutValid" => $this->StatutValidation) ;
				$this->TablPrinc->SensColonneTri = "desc" ;
				$this->TablPrinc->AccepterTriColonneInvisible = 1 ;
				$this->TablPrinc->ParamsGetSoumetFormulaire = array("dateDebut", "dateFin", "idTacheValid") ;
				$this->TablPrinc->TriPossible = 0 ;
				$this->TablPrinc->InsereDefColCachee("date_demarrage_tri") ;
				$this->TablPrinc->InsereDefColCachee("id") ;
				$this->TablPrinc->InsereDefCol("titre", "Formulaire") ;
				$this->TablPrinc->InsereDefCol("login_membre_creation", "Initi&eacute; par") ;
				$this->TablPrinc->InsereDefColDatetimeFr("date_creation", "Initi&eacute; le") ;
				$this->TablPrinc->InsereDefCol("login_membre_valid", ($this->StatutValidation == 1) ? "Confirm&eacute; par" : "Rejet&eacute; par") ;
				$this->TablPrinc->InsereDefColDatetimeFr("date_demarrage", ($this->StatutValidation == 1) ? "Confirm&eacute; le" : "Rejet&eacute; le") ;
			}
		}
		class ScriptDet3PrdEtapeValidUnafdt extends ScriptDet2PrdEtapeValidUnafdt
		{
			public $TitreDocument = "Stats - Circuits de validation rejet&eacute;s" ;
			public $Titre = "Stats - Circuits de validation rejet&eacute;s" ;
			public $StatutValidation = 2 ;
		}
	}
	
?>