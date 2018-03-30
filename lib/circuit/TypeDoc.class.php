<?php
	
	if(! defined('TYPE_DOC_UNAFDT'))
	{
		define('TYPE_DOC_UNAFDT', 1) ;
		
		class TypeDocBaseUnafdt extends ElementCircuitUnafdt
		{
			public $FltsFormEtape = array() ;
			public $InscrireTitre = 1 ;
			public $NomsFltsFormEtapeCache = array() ;
			public function Titre()
			{
				return "Base" ;
			}
			public function DessineFormEtape(& $dessin, & $script, & $composant, $parametres)
			{
				return '' ;
			}
			public function DessineTraiteEtape(& $dessin, & $script, & $composant, $parametres)
			{
				return '' ;
			}
			public function DessineDetailEtape(& $dessin, & $script, & $composant, $parametres)
			{
				return '' ;
			}
			public function AppliqueCmdExecFormEtape(& $cmd)
			{
			}
			public function AppliqueCmdExecTraiteEtape(& $cmd)
			{
			}
			public function RemplitFormEtape(& $form)
			{
			}
			public function & InsereFltEditHttpPostEtape(& $form, $nomParam='', $nomColLiee='')
			{
				$flt = $form->InsereFltEditHttpPost($nomParam, $nomColLiee) ;
				$this->FltsFormEtape[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltEditHttpUploadEtape(& $form, $nomParam='', $cheminRep='', $nomColLiee='')
			{
				$flt = $form->InsereFltEditHttpUpload($nomParam, $cheminRep, $nomColLiee) ;
				$this->FltsFormEtape[] = & $flt ;
				return $flt ;
			}
			public function & InsereFltCacheHttpPostEtape(& $form, $nomParam='', $nomColLiee='')
			{
				$flt = $form->InsereFltEditHttpPost($nomParam, $nomColLiee) ;
				$this->FltsFormEtape[] = & $flt ;
				$this->NomsFltsFormEtapeCache[] = $nomParam ;
				return $flt ;
			}
			public function & InsereFltCacheHttpUploadEtape(& $form, $nomParam='', $cheminRep='', $nomColLiee='')
			{
				$flt = $form->InsereFltEditHttpUpload($nomParam, $cheminRep, $nomColLiee) ;
				$this->FltsFormEtape[] = & $flt ;
				$this->NomsFltsFormEtapeCache[] = $nomParam ;
				return $flt ;
			}
		}
		
		class DessinEtapeValidBaseUnafdt extends PvDessinFiltresDonneesBootstrap
		{
			public function RenduLibelleFiltre(& $filtre)
			{
				return parent::RenduLibelleFiltre($filtre) ;
			}
			public function RenduFiltre(& $filtre, & $composant)
			{
				return parent::RenduFiltre($filtre, $composant) ;
			}
			public function ExecuteNatif(& $script, & $composant, $parametres)
			{
				return parent::Execute($script, $composant, $parametres) ;
			}
		}
		class DessinFormEtapeValidUnafdt extends DessinEtapeValidBaseUnafdt
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				if(isset($script->TypeDocSelect))
				{
					$ctn .= $script->TypeDocSelect->DessineFormEtape($this, $script, $composant, $parametres) ;
				}
				if($ctn == '')
				{
					$ctn = parent::Execute($script, $composant, $parametres) ;
				}
				return $ctn ;
			}
		}
		class DessinTraiteEtapeValidUnafdt extends DessinEtapeValidBaseUnafdt
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				if(isset($script->TypeDocSelect))
				{
					$ctn .= $script->TypeDocSelect->DessineTraiteEtape($this, $script, $composant, $parametres) ;
				}
				if($ctn == '')
				{
					$ctn = parent::Execute($script, $composant, $parametres) ;
				}
				return $ctn ;
			}
		}
		class DessinDetailEtapeValidUnafdt extends DessinEtapeValidBaseUnafdt
		{
			public function Execute(& $script, & $composant, $parametres)
			{
				$ctn = '' ;
				if(isset($script->TypeDocSelect))
				{
					$ctn .= $script->TypeDocSelect->DessineDetailEtape($this, $script, $composant, $parametres) ;
				}
				if($ctn == '')
				{
					$ctn = parent::Execute($script, $composant, $parametres) ;
				}
				return $ctn ;
			}
		}
		
		class TypeDocIndefUnafdt extends TypeDocBaseUnafdt
		{
			public function Titre()
			{
				return "Indefini" ;
			}
		}
		
		class TypeDocPieceJointeUnafdt extends TypeDocBaseUnafdt
		{
			public $FltParam1 ;
			public $FltParam2 ;
			public $CompParam2 ;
			public $Critr1 ;
			public function Titre()
			{
				return "Document joint" ;
			}
			public function RemplitFormEtape(& $form)
			{
				$this->FltParam1 = $form->InsereFltEditHttpPost('param1_doc', 'param1_doc') ;
				$this->FltParam1->Libelle = "Description" ;
				$this->FltParam1->DeclareComposant("PvCkEditor") ;
				$this->FltParam1->CorrecteurValeur = new CorrectValeurUtfUnafdt() ;
				$this->FltsFormEtape[] = & $this->FltParam1 ;
				$this->FltParam2 = $form->InsereFltEditHttpUpload('param2_doc', 'files/docs', 'param2_doc') ;
				$this->FltParam2->Libelle = "Pi&egrave;ce jointe" ;
				$this->CompParam2 = $this->FltParam2->ObtientComposant() ;
				$this->CompParam2->InclureCheminCoteServeur = 0 ;
				$this->CompParam2->InclureApercu = 1 ;
				$this->FltsFormEtape[] = & $this->FltParam2 ;
				if($form->Editable && $form->InscrireCommandeExecuter == 1)
				{
					$this->Critr1 = $form->CommandeExecuter->InsereCritereNonVide(array("param1_doc", "param2_doc")) ;
				}
			}
		}
		
		class TypeDocEmployeUnafdt extends TypeDocBaseUnafdt
		{
			protected $LgnMembre ;
			protected $LgnInfoMembre ;
			protected function RenduTitre1($titre)
			{
				return '<div class="panel panel-default"><div class="panel-footer">'.$titre.'</div></div>'.PHP_EOL ;
			}
			protected function RenduSectionMembre(& $form)
			{
				$bd = $this->ApplicationParent->CreeBDPrinc() ;
				$membership = & $form->ZoneParent->Membership ;
				if($form->InclureElementEnCours == 1)
				{
					$this->LgnMembre = $bd->FetchSqlRow("select t3.*
from fluxtaf_etape_valid t1
inner join fluxtaf_etape_valid t2 on t1.id_etape_initiale = t2.id
inner join ".$membership->MemberTable." t3 on t2.id_membre_creation = t3.id
where t1.id = :id", array("id" => $form->ScriptParent->FltId->Lie())) ;
				}
				else
				{
					$this->LgnMembre = $bd->FetchSqlRow("select * from ".$membership->MemberTable." where id=:id", array("id" => $form->ZoneParent->IdMembreConnecte())) ;
				}
				if(! is_array($this->LgnMembre) || count($this->LgnMembre) == 0)
				{
					return '<div>-- Membre non trouv&eacute; --</div>' ;
				}
				$this->LgnInfoMembre = $bd->FetchSqlRow("select t1.nom nom_service, t2.nom nom_departement, t3.nom nom_direction, t4.nom nom_poste
from ".$membership->MemberTable." t0
left join fluxtaf_service t1 on t0.id_service=t1.id
left join fluxtaf_departement t2 on t0.id_departement=t2.id
left join fluxtaf_direction t3 on t0.id_direction=t3.id
left join fluxtaf_poste t4 on t0.id_poste=t4.id
where t0.id = :idMembre", array("idMembre" => $this->LgnMembre["id"])) ;
				$ctn = '' ;
				$ctn .= $this->RenduTitre1('EMPLOYE').PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Matricule</b></div>
<div class="col-sm-6 col-xs-6"><b>Direction</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnMembre["matricule"]).'</div>
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInfoMembre["nom_direction"]).'</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Nom &amp; Prenom</b></div>
<div class="col-sm-6 col-xs-6"><b>Departement</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnMembre["last_name"].' '.$this->LgnMembre["first_name"]).'</div>
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInfoMembre["nom_departement"]).'</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Categorie</b></div>
<div class="col-sm-6 col-xs-6"><b>Service</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInfoMembre["nom_poste"]).'</div>
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInfoMembre["nom_service"]).'</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Date embauche</b></div>
<div class="col-sm-6 col-xs-6">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.date_fr($this->LgnMembre["date_embauche"]).'</div>
<div class="col-sm-6 col-xs-6">&nbsp;</div>
</div>
</div>' ;
				return $ctn ;
			}
			protected function RenduSectionEtatTraite(& $dessin, & $form)
			{
				$script = & $form->ScriptParent ;
				$ctn = '' ;
				$ctn .= $this->RenduTitre1('ETAT').PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Cr&eacute;&eacute; par</b></div>
<div class="col-sm-6 col-xs-6">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->FltMembreCrea, $form).'</div>
<div class="col-sm-6 col-xs-6">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Valid&eacute; par</b></div>
<div class="col-sm-6 col-xs-6"><b>Date validation</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->FltMembreValid, $form).'</div>
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->FltDateDernValid, $form).'</div>
</div>
</div>' ;
				return $ctn ;
			}
			protected function RenduSectionEtatDetail(& $dessin, & $form)
			{
				$script = & $form->ScriptParent ;
				$ctn = '' ;
				$ctn .= $this->RenduTitre1('ETAT').PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Cr&eacute;&eacute; par</b></div>
<div class="col-sm-6 col-xs-6"><b>Statut</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->FltMembreCrea, $form).'</div>
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->FltStatutPubl, $form).'</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Assign&eacute; &agrave;</b></div>
<div class="col-sm-6 col-xs-6"><b>Valid&eacute; par</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->TypeActeur, $form).'</div>
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($script->FltMembreValid, $form).'</div>
</div>
</div>' ;
				return $ctn ;
			}
			protected function RenduFiltresEtape(& $dessin, & $form)
			{
				$script = & $form->ScriptParent ;
				$ctn = '' ;
				$ctn .= $this->RenduTitre1('DEMANDE').PHP_EOL ;
				$ctn .= '<div class="container-fluid">'.PHP_EOL ;
				$ctn .= '<div class="row">'.PHP_EOL ;
				$ctn .= '<div class="col-sm-3 col-xs-3">'.$dessin->RenduLibelleFiltre($script->FltTitre).'</div>'.PHP_EOL ;
				$ctn .= '<div class="col-sm-9 col-xs-9">'.$dessin->RenduFiltre($script->FltTitre, $form).'</div>'.PHP_EOL ;
				$ctn .= '</div><br />'.PHP_EOL ;
				foreach($this->FltsFormEtape as $i => $flt)
				{
					if(in_array($flt->NomParametreLie, $this->NomsFltsFormEtapeCache))
					{
						continue ;
					}
					$ctn .= '<div class="row">'.PHP_EOL ;
					$ctn .= '<div class="col-sm-3 col-xs-3">'.$dessin->RenduLibelleFiltre($flt).'</div>'.PHP_EOL ;
					$ctn .= '<div class="col-sm-9 col-xs-9">'.$dessin->RenduFiltre($flt, $form).'</div>'.PHP_EOL ;
					$ctn .= '</div><br />'.PHP_EOL ;
				}
				$ctn .= '</div>' ;
				return $ctn ;
			}
			protected function RenduSectionValidation(&$dessin, & $form)
			{
				$script = & $form->ScriptParent ;
				$ctn = '' ;
				$ctn .= $this->RenduTitre1('VALIDATION').PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-sm-3 col-xs-3"><label>Commentaire</label></div>
<div class="col-sm-9 col-xs-9">'.$dessin->RenduFiltre($script->FltCtnCmt, $form).'</div>
</div>
<br />
<div class="row">
<div class="col-sm-3 col-xs-3"><label>Pi&egrave;ce jointe</label></div>
<div class="col-sm-9 col-xs-9">'.$dessin->RenduFiltre($script->FltPjCmt, $form).'</div>
</div>
<br />
<div class="row">
<div class="col-sm-3 col-xs-3"><label>Statut</label></div>
<div class="col-sm-9 col-xs-9">'.$dessin->RenduFiltre($script->FltStatutValid, $form).'</div>
</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class TypeDocSortieCaisseUnafdt extends TypeDocEmployeUnafdt
		{
			public function Titre()
			{
				return "Sortie de caisse" ;
			}
			public function DessineFormEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduFiltresEtape($dessin, $composant) ;
				return $ctn ;
			}
			public function DessineDetailEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionEtatDetail($dessin, $composant) ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduFiltresEtape($dessin, $composant) ;
				$ctn .= $this->RenduSectionValidation($dessin, $composant) ;
				return $ctn ;
			}
			public function DessineTraiteEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionEtatTraite($dessin, $composant) ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduFiltresEtape($dessin, $composant) ;
				$ctn .= $this->RenduSectionValidation($dessin, $composant) ;
				// $ctn .= $dessin->ExecuteNatif($script, $composant, $parametres).PHP_EOL ;
				return $ctn ;
			}
			public function RemplitFormEtape(& $form)
			{
				$form->Largeur = '100%' ;
				$this->FltParam1 = $this->InsereFltEditHttpPostEtape($form, 'param_int1_doc', 'param_int1_doc') ;
				$this->FltParam1->Libelle = "Montant" ;
				$this->FltParam2 = $this->InsereFltEditHttpPostEtape($form, 'param_int2_doc', 'param_int2_doc') ;
				$this->FltParam2->Libelle = "Mode de paiement" ;
				$this->CompParam2 = $this->FltParam2->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompParam2->FournisseurDonnees = $this->ApplicationParent->CreeFournBDPrinc() ;
				$this->CompParam2->FournisseurDonnees->RequeteSelection = "fluxtaf_moyen_rglt" ;
				$this->CompParam2->NomColonneValeur = "id" ;
				$this->CompParam2->NomColonneLibelle = "nom" ;
				$this->FltParam3 = $this->InsereFltEditHttpPostEtape($form, 'param1_doc', 'param1_doc') ;
				$this->FltParam3->Libelle = "Payer &agrave;" ;
				$this->FltParam4 = $this->InsereFltEditHttpPostEtape($form, 'param2_doc', 'param2_doc') ;
				$this->FltParam4->Libelle = "Objet du r&egrave;glement" ;
				$this->FltParam4->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->FltParam5 = $this->InsereFltEditHttpUploadEtape($form, 'param3_doc', 'files/docs', 'param3_doc') ;
				$this->FltParam5->Libelle = "Doc justificatif" ;
				$this->CompParam5 = $this->FltParam5->ObtientComposant() ;
				$this->CompParam5->InclureCheminCoteServeur = 0 ;
				if($form->Editable && $form->InscrireCommandeExecuter == 1)
				{
					$this->Critr1 = $form->CommandeExecuter->InsereCritereNonVide(array("param_int1_doc", "param_int2_doc", "param1_doc", "param2_doc", "param3_doc")) ;
				}
			}
		}
		
		class TypeDocInterimUnafdt extends TypeDocEmployeUnafdt
		{
			public $InscrireTitre = 1 ;
			protected function InstalleFltsInterimFormEtape(& $form)
			{
				$membership = & $form->ZoneParent->Membership ;
				$this->FltIdInterim = $this->InsereFltCacheHttpPostEtape($form, "param_int1_doc", "param_int1_doc") ;
				$this->FltIdInterim->ValeurParDefaut = 1 ;
				$this->CompIdInterim = $this->FltIdInterim->DeclareComposant("PvZoneSelectHtml") ;
				$this->CompIdInterim->FournisseurDonnees = $this->CreeFournBDPrinc() ;
				$this->CompIdInterim->FournisseurDonnees->RequeteSelection = '(select t0.*, concat(t0.'.$membership->LastNameMemberColumn.', \' \', '.$membership->FirstNameMemberColumn.') nom_complet from '.$membership->MemberTable.' t0)' ;
				// print_r($this->CompIdInterim->FournisseurDonnees->RequeteSelection) ;
				$this->CompIdInterim->NomColonneValeur = $membership->IdMemberColumn ;
				$this->CompIdInterim->NomColonneLibelle = 'nom_complet' ;
				$this->CompIdInterim->AttrsSupplHtml["onchange"] = "ActualiseFormulaire".$form->IDInstanceCalc."()" ;
			}
			public function RenduSectionInterimaire(& $dessin, & $composant)
			{
				$membership = & $composant->ZoneParent->Membership ;
				$idInterim = $this->FltIdInterim->Lie() ;
				$bd = & $this->CreeBDPrinc() ;
				$this->LgnInterim = $bd->FetchSqlRow("select t1.*, t2.nom nom_direction, t3.nom nom_categorie
from ".$membership->MemberTable." t1
left join fluxtaf_direction t2 on t1.id_direction = t2.id
left join fluxtaf_categorie t3 on t1.id_categorie = t3.id
where t1.".$membership->IdMemberColumn." = :idMembre", array("idMembre" => $idInterim)) ;
				$ctn = '' ;
				$ctn .= $this->RenduTitre1('INTERIM').PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Nom &amp; Prenom</b></div>
<div class="col-sm-6 col-xs-6"><b>Direction</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.$dessin->RenduFiltre($this->FltIdInterim, $composant).'</div>
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInterim["nom_direction"]).'</div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6"><b>Matricule</b></div>
<div class="col-sm-6 col-xs-6"><b>Categorie</b></div>
</div>
<div class="row">
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInterim["matricule"]).'</div>
<div class="col-sm-6 col-xs-6">'.htmlentities($this->LgnInterim["nom_categorie"]).'</div>
</div>
</div>' ;
				return $ctn ;
			}
		}
		
		class TypeDocDemandeAbsenceUnafdt extends TypeDocInterimUnafdt
		{
			public function Titre()
			{
				return "Demande d'autorisation d'absence" ;
			}
			public function DessineFormEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduSectionInterimaire($dessin, $composant) ;
				$ctn .= $this->RenduFiltresEtape($dessin, $composant) ;
				return $ctn ;
			}
			public function DessineDetailEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionEtatDetail($dessin, $composant) ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduSectionInterimaire($dessin, $composant) ;
				$ctn .= $this->RenduFiltresEtape($dessin, $composant) ;
				$ctn .= $this->RenduSectionValidation($dessin, $composant) ;
				return $ctn ;
			}
			public function DessineTraiteEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionEtatTraite($dessin, $composant) ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduSectionInterimaire($dessin, $composant) ;
				$ctn .= $this->RenduFiltresEtape($dessin, $composant) ;
				$ctn .= $this->RenduSectionValidation($dessin, $composant) ;
				// $ctn .= $dessin->ExecuteNatif($script, $composant, $parametres).PHP_EOL ;
				return $ctn ;
			}
			public function RemplitFormEtape(& $form)
			{
				$form->Largeur = '100%' ;
				$this->InstalleFltsInterimFormEtape($form) ;
				$this->FltParam1 = $this->InsereFltEditHttpPostEtape($form, 'param_int2_doc', 'param_int2_doc') ;
				$this->FltParam1->Libelle = "Nature absence" ;
				$this->CompParam1 = $this->FltParam1->DeclareComposant("PvZoneBoiteOptionsRadioHtml") ;
				$this->CompParam1->FournisseurDonnees = $this->ApplicationParent->CreeFournBDPrinc() ;
				$this->CompParam1->FournisseurDonnees->RequeteSelection = "fluxtaf_nature_absence" ;
				$this->CompParam1->NomColonneValeur = "id" ;
				$this->CompParam1->NomColonneLibelle = "nom" ;
				$this->FltParam3 = $this->InsereFltEditHttpPostEtape($form, 'param_date1_doc', 'param_date1_doc') ;
				$this->FltParam3->Libelle = "Date debut" ;
				$this->FltParam3->DeclareComposant("PvCalendarDateInput") ;
				$this->FltParam4 = $this->InsereFltEditHttpPostEtape($form, 'param_date2_doc', 'param_date2_doc') ;
				$this->FltParam4->Libelle = "Date fin" ;
				$this->FltParam4->DeclareComposant("PvCalendarDateInput") ;
				$this->FltParam5 = $this->InsereFltEditHttpPostEtape($form, 'param1_doc', 'param1_doc') ;
				$this->FltParam5->Libelle = "Motif" ;
				$this->FltParam5->DeclareComposant("PvZoneMultiligneHtml") ;
				$this->FltParam6 = $this->InsereFltEditHttpUploadEtape($form, 'param2_doc', 'files/docs', 'param2_doc') ;
				$this->FltParam6->Libelle = "Doc justificatif" ;
				$this->CompParam6 = $this->FltParam5->ObtientComposant() ;
				$this->CompParam6->InclureCheminCoteServeur = 0 ;
				if($form->Editable && $form->InscrireCommandeExecuter == 1)
				{
					$this->Critr1 = $form->CommandeExecuter->InsereCritereNonVide(array("param_int1_doc", "param_int2_doc", "param_date1_doc", "param_date2_doc", "param1_doc", "param2_doc")) ;
				}
			}
		}
		class TypeDocDemandeCongeUnafdt extends TypeDocInterimUnafdt
		{
			public function Titre()
			{
				return html_entity_decode("Demande de cong&eacute;") ;
			}
			public function AppliqueCmdExecFormEtape(& $cmd)
			{
				$cmd->ScriptParent->FltTitre->Valeur = "Congé du ".date_fr($this->FltParam3->Lie())." au ".date_fr($this->FltParam4->Lie()) ;
			}
			public function DessineFormEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduSectionInterimaire($dessin, $composant) ;
				$ctn .= $this->RenduFiltresPrinc($dessin, $composant) ;
				return $ctn ;
			}
			public function DessineDetailEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionEtatDetail($dessin, $composant) ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduSectionInterimaire($dessin, $composant) ;
				$ctn .= $this->RenduFiltresPrinc($dessin, $composant) ;
				$ctn .= $this->RenduSectionValidation($dessin, $composant) ;
				return $ctn ;
			}
			public function DessineTraiteEtape(& $dessin, & $script, & $composant, $parametres)
			{
				$ctn = '' ;
				$ctn .= $this->RenduSectionEtatTraite($dessin, $composant) ;
				$ctn .= $this->RenduSectionMembre($composant) ;
				$ctn .= $this->RenduSectionInterimaire($dessin, $composant) ;
				$ctn .= $this->RenduFiltresPrinc($dessin, $composant) ;
				$ctn .= $this->RenduSectionValidation($dessin, $composant) ;
				return $ctn ;
			}
			protected function RenduFiltresPrinc(& $dessin, & $composant)
			{
				$ctn = '' ;
				$ctn .= $this->RenduTitre1("MOTIF DE LA DEMANDE") ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-3">'.$dessin->RenduLibelleFiltre($this->FltParam1).'</div>
<div class="col-xs-9">'.$dessin->RenduFiltre($this->FltParam1, $composant).'</div>
</div>
</div>
<br />'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-3">'.$dessin->RenduLibelleFiltre($this->FltParam3).'</div>
<div class="col-xs-3">'.$dessin->RenduFiltre($this->FltParam3, $composant).'</div>
<div class="col-xs-3">'.$dessin->RenduLibelleFiltre($this->FltParam4).'</div>
<div class="col-xs-3">'.$dessin->RenduFiltre($this->FltParam4, $composant).'</div>
</div>
</div>
<br />'.PHP_EOL ;
				$ctn .= $this->RenduTitre1("ADRESSE PENDANT LES CONGES") ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-3">'.$dessin->RenduLibelleFiltre($this->FltParam5).'</div>
<div class="col-xs-9">'.$dessin->RenduFiltre($this->FltParam5, $composant).'</div>
</div>
</div>
<br />'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-3">'.$dessin->RenduLibelleFiltre($this->FltParam6).'</div>
<div class="col-xs-9">'.$dessin->RenduFiltre($this->FltParam6, $composant).'</div>
</div>
</div>
<br />'.PHP_EOL ;
				$ctn .= '<div class="container-fluid">
<div class="row">
<div class="col-xs-3">'.$dessin->RenduLibelleFiltre($this->FltParam7).'</div>
<div class="col-xs-9">'.$dessin->RenduFiltre($this->FltParam7, $composant).'</div>
</div>
</div>'.PHP_EOL ;
				return $ctn ;
			}
			public function RemplitFormEtape(& $form)
			{
				$form->Largeur = '100%' ;
				$this->InstalleFltsInterimFormEtape($form) ;
				$this->FltParam1 = $this->InsereFltEditHttpPostEtape($form, 'param_int2_doc', 'param_int2_doc') ;
				$this->FltParam1->Libelle = "Nature cong&eacute;" ;
				$this->CompParam1 = $this->FltParam1->DeclareComposant("PvZoneBoiteOptionsRadioHtml") ;
				$this->CompParam1->FournisseurDonnees = $this->ApplicationParent->CreeFournBDPrinc() ;
				$this->CompParam1->FournisseurDonnees->RequeteSelection = "fluxtaf_nature_absence" ;
				$this->CompParam1->NomColonneValeur = "id" ;
				$this->CompParam1->NomColonneLibelle = "nom" ;
				$this->FltParam3 = $this->InsereFltEditHttpPostEtape($form, 'param_date1_doc', 'param_date1_doc') ;
				$this->FltParam3->Libelle = "Date debut" ;
				$this->FltParam3->DeclareComposant("PvCalendarDateInput") ;
				$this->FltParam4 = $this->InsereFltEditHttpPostEtape($form, 'param_date2_doc', 'param_date2_doc') ;
				$this->FltParam4->Libelle = "Date fin" ;
				$this->FltParam4->DeclareComposant("PvCalendarDateInput") ;
				$this->FltParam5 = $this->InsereFltEditHttpPostEtape($form, 'param1_doc', 'param1_doc') ;
				$this->FltParam5->Libelle = "Domicile habituel" ;
				$this->FltParam6 = $this->InsereFltEditHttpPostEtape($form, 'param2_doc', 'param2_doc') ;
				$this->FltParam6->Libelle = "Contact t&eacute;l&eacute;phonique" ;
				$this->FltParam7 = $this->InsereFltEditHttpPostEtape($form, 'param3_doc', 'param3_doc') ;
				$this->FltParam7->Libelle = "Personne &agrave; contacter" ;
				if($form->Editable && $form->InscrireCommandeExecuter == 1)
				{
					$this->Critr1 = $form->CommandeExecuter->InsereCritereNonVide(array("param_int1_doc", "param_int2_doc", "param_date1_doc", "param_date2_doc", "param1_doc", "param2_doc", "param3_doc")) ;
				}
			}
		}
		
	}

?>