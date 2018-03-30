<?php
	
	if(! defined('SCRIPT_NOYAU_UNAFDT'))
	{
		define('SCRIPT_NOYAU_UNAFDT', 1) ;
		
		class ScriptBaseUnafdt extends PvScriptBaseSbAdmin2
		{
			public $NomDocumentWeb = "connecte" ;
			public $Aspect ;
			public function BDPrinc()
			{
				return $this->ApplicationParent->BDPrinc ;
			}
			protected function CreeAspect()
			{
				return new AspectScriptIndefUnafdt() ;
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineAspect() ;
			}
			protected function DetermineAspect()
			{
				$this->Aspect = $this->CreeAspect() ;
				$this->Aspect->DefinitScript($this) ;
				$this->Aspect->ChargeConfig() ;
				$this->Aspect->Prepare() ;
			}
			protected function CreeTablPrinc()
			{
				return new TablDonneesBaseUnafdt() ;
			}
			protected function CreeFormPrinc()
			{
				return new FormDonneesBaseUnafdt() ;
			}
			protected function CreeFournBDPrinc()
			{
				$fourn = new PvFournisseurDonneesSql() ;
				$fourn->BaseDonnees = $this->ApplicationParent->BDPrinc ;
				return $fourn ;
			}
			public function RenduDispositifBrut()
			{
				$ctn = '' ;
				if($this->Aspect->EstValide() == 1)
				{
					$ctn .= $this->Aspect->Rendu() ;
					$ctn .= parent::RenduDispositifBrut() ;
				}
				else
				{
					$ctn .= '<p class="bg-error">La page que vous avez demand&eacute;e n\'existe pas</p>' ;
				}
				return $ctn ;
			}
		}
		
		class ScriptAccueilUnafdt extends PvScriptAccueilSbAdmin2
		{
			public $TitreDocument = "Bienvenue" ;
			public $Titre = "Bienvenue sur le Work Flow de UNAFDT" ;
			public $MsgBienvenue = "Bienvenue sur le Work Flow de UNAFDT" ;
			public function RenduDispositifBrut()
			{
				$ctn = '' ;
				// $ctn .= '<p class="well">'.$this->MsgBienvenue.'</p>'.PHP_EOL ;
				$ctn .= "<div align='center'><img src='images/arr-plan-workflow.gif' /></div>" ;
				return $ctn ;
			}
		}
		
		class ScriptListeBaseUnafdt extends ScriptBaseUnafdt
		{
			protected $TablPrinc ;
			protected $InscrireDefColActsTablPrinc = 0 ;
			protected $InscrireFournBDPrinc = 1 ;
			protected $ReqSelectTablPrinc = "" ;
			protected $DefColActsTablPrinc ;
			protected function InitTablPrinc()
			{
			}
			protected function ChargeTablPrinc()
			{
			}
			protected function ChargeTablPrincAuto()
			{
				if($this->InscrireFournBDPrinc)
				{
					$this->TablPrinc->FournisseurDonnees = $this->CreeFournBDPrinc() ;
					$this->TablPrinc->FournisseurDonnees->RequeteSelection = $this->ReqSelectTablPrinc ;
				}
			}
			protected function InitDefColActsTablPrinc()
			{
				$this->DefColActsTablPrinc = $this->TablPrinc->InsereDefColActions("Actions") ;
			}
			protected function ChargeDefColActsTablPrinc()
			{
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineTablPrinc() ;
			}
			protected function DetermineTablPrinc()
			{
				$this->TablPrinc = $this->CreeTablPrinc() ;
				$this->InitTablPrinc() ;
				$this->TablPrinc->AdopteScript("tablPrinc", $this) ;
				$this->TablPrinc->ChargeConfig() ;
				$this->ChargeTablPrincAuto() ;
				$this->ChargeTablPrinc() ;
				if($this->InscrireDefColActsTablPrinc == 1)
				{
					$this->InitDefColActsTablPrinc() ;
					$this->ChargeDefColActsTablPrinc() ;
				}
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->TablPrinc->RenduDispositif() ;
				// print_r($this->TablPrinc->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}
		class ScriptEditBaseUnafdt extends ScriptBaseUnafdt
		{
			protected $FormPrinc ;
			protected $PourAjout = 1 ;
			protected $NomScriptExecSucces = "" ;
			protected $ParamsScriptExecSucces = "" ;
			protected $FormPrincEditable = 1 ;
			protected $InscrireCmdExecFormPrinc = 1 ;
			protected $NomClasseCmdExecFormPrinc = "PvCommandeAjoutElement" ;
			protected $MsgSuccesCmdExecFormPrinc = "" ;
			protected $InscrireCmdAnnulFormPrinc = 1 ;
			protected $NomClasseCmdAnnulFormPrinc = "PvCommandeExecuterBase" ;
			protected $UrlRedirectAnnulFormPrinc = "" ;
			protected $InscrireFournBDPrinc = 1 ;
			protected $TablEditFormPrinc = "" ;
			protected $ReqSelectFormPrinc = "" ;
			protected function InitFormPrinc()
			{
				$this->FormPrinc->InclureElementEnCours = ($this->PourAjout) ? 0 : 1 ;
				$this->FormPrinc->InclureTotalElements = ($this->PourAjout) ? 0 : 1 ;
				$this->FormPrinc->Editable = $this->FormPrincEditable ;
				$this->FormPrinc->InscrireCommandeAnnuler = $this->InscrireCmdAnnulFormPrinc ;
				$this->FormPrinc->NomClasseCommandeAnnuler = $this->NomClasseCmdAnnulFormPrinc ;
				$this->FormPrinc->InscrireCommandeExecuter = $this->InscrireCmdExecFormPrinc ;
				$this->FormPrinc->NomClasseCommandeExecuter = $this->NomClasseCmdExecFormPrinc ;
				$this->FormPrinc->MsgExecSuccesCommandeExecuter = $this->MsgSuccesCmdExecFormPrinc ;
				$this->FormPrinc->NomScriptExecSuccesCommandeExecuter = $this->NomScriptExecSucces ;
				$this->FormPrinc->ParamsScriptExecSuccesCommandeExecuter = $this->ParamsScriptExecSucces ;
			}
			protected function ChargeFormPrinc()
			{
			}
			protected function ChargeFormPrincAuto()
			{
				if($this->UrlRedirectAnnulFormPrinc != '' && $this->InscrireCmdAnnulFormPrinc)
				{
					$this->FormPrinc->RedirigeAnnulerVersUrl($this->UrlRedirectAnnulFormPrinc) ;
				}
				if($this->InscrireFournBDPrinc)
				{
					$this->FormPrinc->FournisseurDonnees = $this->CreeFournBDPrinc() ;
					$this->FormPrinc->FournisseurDonnees->RequeteSelection = $this->ReqSelectFormPrinc ;
					$this->FormPrinc->FournisseurDonnees->TableEdition = $this->TablEditFormPrinc ;
					// print_r($this->FormPrinc->FournisseurDonnees) ;
				}
			}
			public function DetermineEnvironnement()
			{
				parent::DetermineEnvironnement() ;
				$this->DetermineFormPrinc() ;
			}
			protected function DetermineFormPrinc()
			{
				$this->FormPrinc = $this->CreeFormPrinc() ;
				$this->InitFormPrinc() ;
				$this->FormPrinc->AdopteScript("formPrinc", $this) ;
				$this->FormPrinc->ChargeConfig() ;
				$this->ChargeFormPrincAuto() ;
				$this->ChargeFormPrinc() ;
			}
			public function RenduSpecifique()
			{
				$ctn = '' ;
				$ctn .= $this->FormPrinc->RenduDispositif() ;
				// print_r($this->FormPrinc->FournisseurDonnees->BaseDonnees) ;
				return $ctn ;
			}
		}
		
		class AspectScriptBaseUnafdt
		{
			public $ScriptParent ;
			public function BDPrinc()
			{
				return $this->ScriptParent->BDPrinc() ;
			}
			public function EstValide()
			{
				return 0 ;
			}
			public function DefinitScript(& $script)
			{
				$this->ScriptParent = & $script ;
			}
			public function ChargeConfig()
			{
			}
			public function Prepare()
			{
			}
			public function Rendu()
			{
				return "" ;
			}
		}
		
		class AspectScriptIndefUnafdt extends AspectScriptBaseUnafdt
		{
			public function EstValide()
			{
				return 1 ;
			}
		}
		
		class AspectScriptEditUnafdt extends AspectScriptBaseUnafdt
		{
			public $ReqSelectPrinc = "" ;
			public $LgnPrinc ;
			public $ValeurParamId ;
			public $NomParamId = "" ;
			public $FormatContenu = '' ;
			public function Prepare()
			{
				$this->ValeurParamId = _GET_def($this->NomParamId) ;
				$bd = $this->BDPrinc() ;
				$this->LgnPrinc = $bd->FetchSqlRow("select * from ".$this->ReqSelectPrinc." where id = :id", array("id" => $this->ValeurParamId)) ;
			}
			public function EstValide()
			{
				return (is_array($this->LgnPrinc) > 0 && count($this->LgnPrinc) > 0) ;
			}
			public function Rendu()
			{
				$ctn = '' ;
				if($this->FormatContenu == '')
				{
					return $ctn ;
				}
				$ctn .= _parse_pattern($this->FormatContenu, array_map('htmlentities', $this->LgnPrinc)) ;
				return $ctn ;
			}
		}
	}
	
?>