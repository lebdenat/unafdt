<?php
	
	if(! defined('TYPE_ALERTE_UNAFDT'))
	{
		define('TYPE_ALERTE_UNAFDT', 1) ;
		
		class TypeAlerteBaseUnafdt extends ElementCircuitUnafdt
		{
			public function Titre()
			{
				return "Base" ;
			}
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
			}
		}
		
		class TypeAlerteIndefUnafdt extends TypeAlerteBaseUnafdt
		{
			public function Titre()
			{
				return "Indefini" ;
			}
		}
		
		class TypeAlerteSmsBaseUnafdt extends TypeAlerteBaseUnafdt
		{
			public function Titre()
			{
				return "Envoi SMS" ;
			}
			protected function ContenuSmsEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				return clean_special_chars($lgnMembre["login_member"]).", formulaire ".clean_special_chars($lgnEtape["id"])."-".clean_special_chars($lgnEtape["titre"])." vient d'etre soumis." ;
			}
			protected function ContenuSmsEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$libValidee = ($lgnEtape["statut_validation"] == 1) ? "confirme" : "valide" ;
				return clean_special_chars($lgnMembre["login_member"]).", formulaire ".clean_special_chars($lgnEtape["id"])."-".clean_special_chars($lgnEtape["titre"])." vient d'etre ".$libValidee."" ;
			}
		}
		class TypeAlerteUrlSmsUnafdt extends TypeAlerteSmsBaseUnafdt
		{
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				$contenu = $this->ContenuSmsEtapeAValider($lgnMembre, $lgnEtape) ;
				$httpSess = new HttpSession() ;
				$contacts = explode(";", $lgnMembre["contact"]) ;
				foreach($contacts as $i => $numero)
				{
					if(trim($numero) == '')
					{
						continue ;
					}
					$params = array("numeroSource" => SOA_ENVOI_SMS_UNAFDT, "numeroDest" => trim($numero), "contenu" => $contenu) ;
					$result = $httpSess->GetPage(_parse_pattern(URL_ENVOI_SMS_UNAFDT, array_map("urlencode", $params))) ;
				}
			}
			public function NotifieEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$contenu = $this->ContenuSmsEtapeAValider($lgnMembre, $lgnEtape) ;
				$httpSess = new HttpSession() ;
				$contacts = explode(";", $lgnMembre["contact"]) ;
				foreach($contacts as $i => $numero)
				{
					if(trim($numero) == '')
					{
						continue ;
					}
					$params = array("numeroSource" => SOA_ENVOI_SMS_UNAFDT, "numeroDest" => trim($numero), "contenu" => $contenu) ;
					$result = $httpSess->GetPage(_parse_pattern(URL_ENVOI_SMS_UNAFDT, array_map("urlencode", $params))) ;
				}
			}
		}
		class TypeAlerteWgetSmsUnafdt extends TypeAlerteSmsBaseUnafdt
		{
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				$bd = $this->CreeBDPrinc() ;
				$contenu = $this->ContenuSmsEtapeAValider($lgnMembre, $lgnEtape) ;
				$httpSess = new HttpSession() ;
				$contacts = explode(";", $lgnMembre["contact"]) ;
				foreach($contacts as $i => $numero)
				{
					if(trim($numero) == '')
					{
						continue ;
					}
					$params = array("numeroSource" => SOA_ENVOI_SMS_UNAFDT, "numeroDest" => trim($numero), "contenu" => $contenu) ;
					$cmd = 'wget -qO- '.escapeshellarg(_parse_pattern(URL_ENVOI_SMS_UNAFDT, array_map("urlencode", $params))) ;
					exec($cmd, $retour, $statut) ;
					$bd->RunSql("insert into fluxtaf_sms_wget_etape_valid (id_membre_action, id_etape_valid, commande, resultat, statut) values (:idMembre, :idEtape, :cmd, :result, :statut)", array("idMembre" => $lgnMembre["id"], "idEtape" => $lgnEtape["id"], "cmd" => $cmd, "result" => join("\n", $retour), "statut" => $statut)) ;
				}
			}
			public function NotifieEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$bd = $this->CreeBDPrinc() ;
				$contenu = $this->ContenuSmsEtapeValidee($lgnMembre, $lgnEtape) ;
				$contacts = explode(";", $lgnMembre["contact"]) ;
				foreach($contacts as $i => $numero)
				{
					if(trim($numero) == '')
					{
						continue ;
					}
					$params = array("numeroSource" => SOA_ENVOI_SMS_UNAFDT, "numeroDest" => trim($numero), "contenu" => $contenu) ;
					$cmd = 'wget -qO- '.escapeshellarg(_parse_pattern(URL_ENVOI_SMS_UNAFDT, array_map("urlencode", $params))) ;
					exec($cmd, $retour, $statut) ;
					$bd->RunSql("insert into fluxtaf_sms_wget_etape_valid (id_membre_action, id_etape_valid, commande, resultat, statut) values (:idMembre, :idEtape, :cmd, :result, :statut)", array("idMembre" => $lgnMembre["id"], "idEtape" => $lgnEtape["id"], "cmd" => $cmd, "result" => join("\n", $retour), "statut" => $statut)) ;
				}
			}
		}
		
		class TypeAlerteMailBaseUnafdt extends TypeAlerteBaseUnafdt
		{
			public function Titre()
			{
				return "Envoi Mail Base" ;
			}
			protected function SujetMailEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				return html_entity_decode("Formulaire #".htmlentities($lgnEtape["id"])."-".htmlentities($lgnEtape["titre"])." en attente de validation") ;
			}
			protected function CorpsMailEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				return "<p>Bonjour ".htmlentities($lgnMembre["login_member"]).",</p>
<p>&nbsp;</p>
<p>Le formulaire <b>".htmlentities($lgnEtape["id"])."-".htmlentities($lgnEtape["titre"])."</b> vient d'&ecirc;tre soumis.</p>
<p>Veuillez vous connecter pour le valider.</p>
<p>&nbsp;</p>
<p>".SIGN_MAIL_VALID_UNAFDT."</p>" ;
			}
			protected function SujetMailEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$libValidee = ($lgnEtape["statut_validation"] == 1) ? "confirm&eacute;" : "rejet&eacute;" ;
				return html_entity_decode("Formulaire #".htmlentities($lgnEtape["id"])."-".htmlentities($lgnEtape["titre"])." ".$libValidee) ;
			}
			protected function CorpsMailEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$libValidee = ($lgnEtape["statut_validation"] == 1) ? "confirm&eacute;e" : "valid&eacute;" ;
				return  "<p>Bonjour ".htmlentities($lgnMembre["login_member"]).",</p>
<p>&nbsp;</p>
<p>Le formulaire <b>".htmlentities($lgnEtape["id"])."-".htmlentities($lgnEtape["titre"])."</b> vient d'&ecirc;tre ".$libValidee.".</p>
<p>&nbsp;</p>
<p>".SIGN_MAIL_VALID_UNAFDT."</p>" ;
			}
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				return false ;
			}
			public function NotifieEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				return false ;
			}
		}
		
		class TypeAlerteMailerUnafdt extends TypeAlerteMailBaseUnafdt
		{
			protected function & CreeMailer()
			{
				$mail = new PHPMailer(); // create a new object
				$mail->IsSMTP(); // enable SMTP
				$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
				$mail->SMTPAuth = true; // authentication enabled
				$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
				$mail->Host = HOTE_SMTP_UNAFDT ;
				$mail->Port = PORT_SMTP_UNAFDT ; // or 587
				$mail->IsHTML(true);
				$mail->Username = COMPTE_MAIL_VALID_UNAFDT;
				$mail->Password = PWD_MAIL_VALID_UNAFDT ;
				return $mail ;
			}
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				$mail = $this->CreeMailer() ;
				$mail->Subject = $this->SujetMailEtapeAValider($lgnMembre, $lgnEtape) ;
				$mail->Body = $this->CorpsMailEtapeAValider($lgnMembre, $lgnEtape) ;
				$mail->AddAddress($lgnMembre["email"]);
				return $mail->Send() ;
			}
			public function NotifieEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$mail = $this->CreeMailer() ;
				$mail->Subject = $this->SujetMailEtapeValidee($lgnMembre, $lgnEtape) ;
				$mail->Body = $this->CorpsMailEtapeValidee($lgnMembre, $lgnEtape) ;
				$mail->AddAddress($lgnMembre["email"]);
				return $mail->Send() ;
			}
		}
		class TypeAlerteSendMailUnafdt extends TypeAlerteMailBaseUnafdt
		{
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				$sujet = $this->SujetMailEtapeAValider($lgnMembre, $lgnEtape) ;
				$corps = $this->CorpsMailEtapeAValider($lgnMembre, $lgnEtape) ;
				return send_html_mail($lgnMembre["email"], $sujet, $corps, COMPTE_MAIL_VALID_UNAFDT) ;
			}
			public function NotifieEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$sujet = $this->SujetMailEtapeValidee($lgnMembre, $lgnEtape) ;
				$corps = $this->CorpsMailEtapeValidee($lgnMembre, $lgnEtape) ;
				return send_html_mail($lgnMembre["email"], $sujet, $corps, COMPTE_MAIL_VALID_UNAFDT) ;
			}
		}
		class TypeAlerteMailShellUnafdt extends TypeAlerteMailBaseUnafdt
		{
			public function NotifieEtapeAValider(& $lgnMembre, & $lgnEtape)
			{
				$bd = $this->CreeBDPrinc() ;
				$sujet = $this->SujetMailEtapeAValider($lgnMembre, $lgnEtape) ;
				$corps = $this->CorpsMailEtapeAValider($lgnMembre, $lgnEtape) ;
				$cmd = "/usr/local/bin/sendEmail -f ".escapeshellarg(COMPTE_MAIL_VALID_UNAFDT)." -t ".escapeshellarg($lgnMembre["email"])." -s ".escapeshellarg(HOTE_SMTP_UNAFDT)." -u ".escapeshellarg($sujet)." -m ".escapeshellarg('<html>'.$corps.'</html>')." -o message-content-type=html";
				exec($cmd , $retour, $statut);
				$bd->RunSql("insert into fluxtaf_mail_shell_etape_valid (id_membre_action, id_etape_valid, commande, resultat, statut) values (:idMembre, :idEtape, :cmd, :result, :statut)", array("idMembre" => $lgnMembre["id"], "idEtape" => $lgnEtape["id"], "cmd" => $cmd, "result" => join("\n", $retour), "statut" => $statut)) ;
			}
			public function NotifieEtapeValidee(& $lgnMembre, & $lgnEtape)
			{
				$bd = $this->CreeBDPrinc() ;
				$sujet = $this->SujetMailEtapeValidee($lgnMembre, $lgnEtape) ;
				$corps = $this->CorpsMailEtapeValidee($lgnMembre, $lgnEtape) ;
				$cmd = "/usr/local/bin/sendEmail -f ".escapeshellarg(COMPTE_MAIL_VALID_UNAFDT)." -t ".escapeshellarg($lgnMembre["email"])." -s ".escapeshellarg(HOTE_SMTP_UNAFDT)." -u ".escapeshellarg($sujet)." -m ".escapeshellarg('<html>'.$corps.'</html>')." -o message-content-type=html";
				exec($cmd , $retour, $statut);
				$bd->RunSql("insert into fluxtaf_mail_shell_etape_valid (id_membre_action, id_etape_valid, commande, resultat, statut) values (:idMembre, :idEtape, :cmd, :result, :statut)", array("idMembre" => $lgnMembre["id"], "idEtape" => $lgnEtape["id"], "cmd" => $cmd, "result" => join("\n", $retour), "statut" => $statut)) ;
			}
		}
		
	}

?>