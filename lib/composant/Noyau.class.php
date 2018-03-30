<?php
	
	if(! defined('COMPOSANT_NOYAU_UNAFDT'))
	{
		define('COMPOSANT_NOYAU_UNAFDT', 1) ;
		
		class FormDonneesBaseUnafdt extends PvFormulaireDonneesSbAdmin2
		{
			public $LibelleCommandeExecuter = "Valider" ;
		}
		class TablDonneesBaseUnafdt extends PvTableauDonneesSbAdmin2
		{
		}
		
		class FormulaireDonneesBaseUnafdt extends FormDonneesBaseUnafdt
		{
		}
		class TableauDonneesBaseUnafdt extends TablDonneesBaseUnafdt
		{
		}
		
		class CmdAnnulBaseUnafdt extends PvCommandeAnnulerBase
		{
		}
		class CmdExecBaseUnafdt extends PvCommandeExecuterBase
		{
			protected function RenseigneErreurBD(& $bd)
			{
				$this->RenseigneErreur("Erreur SQL : ".$bd->ConnectionException) ;
			}
		}
		class CmdAjoutElementUnafdt extends PvCommandeAjoutElement
		{
		}
		class CmdModifElementUnafdt extends PvCommandeModifElement
		{
		}
		class CmdSupprElementUnafdt extends PvCommandeSupprElement
		{
			public $CacherFormulaireFiltresSiSucces = 1 ;
		}
		class CmdDesactiveElementUnafdt extends PvCommandeExecuterBase
		{
			public $InscrireLienAnnuler = 1 ;
			public $NomColonneDesactive = "ACTIVE" ;
			public $ValeurDesactive = 0 ;
			public $CacherFormulaireFiltresSiSucces = 1 ;
			protected function CreeFiltresEdition()
			{
				$filtres = array() ;
				$filtres["desactive"] = $this->ScriptParent->CreeFiltreFixe("desactive", $this->ValeurDesactive) ;
				$filtres["desactive"]->NomColonneLiee = $this->NomColonneDesactive ;
				return $filtres ;
			}
			protected function ExecuteInstructions()
			{
				if($this->FormulaireDonneesParent->EstNul($this->FormulaireDonneesParent->FournisseurDonnees))
				{
					$this->ConfirmeSucces("Aucun fournisseur de donn&eacute;es n'a &eacute;t&eacute; renseign&eacute;") ;
					return ;
				}
				$fourn = & $this->FormulaireDonneesParent->FournisseurDonnees ;
				$filtresEdit = $this->CreeFiltresEdition() ;
				$ok = $fourn->ModifElement($this->FormulaireDonneesParent->FiltresLigneSelection, $filtresEdit) ;
				if($ok)
				{
					$this->ConfirmeSucces() ;
				}
				else
				{
					$this->RenseigneErreur("Erreur SQL : ".$fourn->BaseDonnees->ConnectionException) ;
				}
			}
		}
	
		// class CorrectValeurUtfUnafdt extends PvCorrecteurValeurEncodeeUtf8
		// class CorrectValeurUtfUnafdt extends PvCorrecteurValeurSansAccent
		class CorrectValeurUtfUnafdt extends PvCorrecteurValeurFiltreBase
		{
			/*
			public function AppliquePourRendu($valeur, & $filtre)
			{
				return ForceEncoding::ToUTF8($valeur) ;
			}
			public function AppliquePourTraitement($valeur, & $filtre)
			{
				return ForceEncoding::ToUTF8($valeur) ;
			}
			public function AppliquePourColonne($valeur, & $defCol)
			{
 				return utf8_decode($valeur) ;
			}
			*/
		}
		class CorrectValeur2Unafdt extends CorrectValeurUtfUnafdt
		{
		}
	}
	
?>