
var METHODE_APPEL_AJAX_GET = "GET" ;
var METHODE_APPEL_AJAX_POST = "POST" ;

function AppelAjax() {
	this.Asynchrone = true ;
	this.NePasTraiterUrlVide = true ;
	this.SurChangeEtatAppel = null ;
	this.CreeReponse = function (xmlHttp) {
		var rep = new ReponseAjax() ;
		rep.AnalyseXmlHttp(xmlHttp) ;
		return rep ;
	}
	this.CreeRequeteGET = function () {
		req = new RequeteAjax() ;
		req.AppelParent = this
		req.Methode = METHODE_APPEL_AJAX_GET ;
		return req ;
	}
	this.CreeRequetePOST = function () {
		req = new RequeteAjax() ;
		req.AppelParent = this
		req.Methode = METHODE_APPEL_AJAX_POST ;
		return req ;
	}
	this.ObtientXmlHttp = function () {
		var xmlHttp ;
		try
		{
			//Firefox, Opera 8.0+, Safari
			xmlHttp = new XMLHttpRequest();
		}
		catch(e)
		{
			//Internet Explorer
			try
			{
				xmlHttp = new ActiveXObject("Msxml2.XMLHTTP.6.0");
			}
			catch(e)
			{
				try
				{
					new ActiveXObject("Msxml2.XMLHTTP.3.0");
				}
				catch(e)
				{
					try
					{
						xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch(e)
					{
						alert("Initialisation de la requete AJAX echouée !!! Veuillez mettre a jour votre navigateur")
						return false;
					}
				}
			}
		}
		return xmlHttp;
	}
	this.PrepareXmlHttp = function (xmlHttp, requete) {
		var appelAjax = this ;
		xmlHttp.onreadystatechange = function() {
			// alert (xmlHttp.responseText) ;
			var reponse = appelAjax.CreeReponse(xmlHttp) ;
			if(requete.SurChangeEtatAppel != null)
			{
				requete.SurChangeEtatAppel(reponse, xmlHttp) ;
			}
			if(reponse.EstTermine() && ! requete.DelaiExpirationAtteint)
			{
				requete.ArreteExecution() ;
				reponse.ChargeEntetes(xmlHttp) ;
				if(requete.SurTermine != null)
				{
					requete.SurTermine(reponse, xmlHttp) ;
				}
				if(requete.SurEchoue != null && reponse.EstEchoue())
				{
					requete.SurEchoue(reponse, xmlHttp) ;
				}
				if(requete.SurSucces != null && reponse.EstSucces())
				{
					requete.SurSucces(reponse, xmlHttp) ;
				}
			}
		}
	}
	this.Execute = function (requete) {
		if(this.NePasTraiterUrlVide)
		{
			var url = requete.ObtientUrl() ;
			if(url == "" || url == undefined)
			{
				return ;
			}
		}
		if(requete.Methode == METHODE_APPEL_AJAX_GET)
		{
			this.ExecuteParGET(requete) ;
		}
		else
		{
			this.ExecuteParPOST(requete) ;
		}
	}
	this.ExecuteParGET = function(requete) {
		var xmlHttp = this.ObtientXmlHttp() ;
		if(xmlHttp == null)
		{
			return ;
		}
		var contenu = null ;
		this.PrepareXmlHttp(xmlHttp, requete) ;
		try
		{
			xmlHttp.open("GET", requete.ObtientUrl(), this.Asynchrone) ;
			requete.DemarreExecution(xmlHttp) ;
			contenu = xmlHttp.send() ;
		}
		catch(ex)
		{
			alert("Exception : " + ex.message) ;
		}
		return contenu ;
	}
	this.ExecuteParPOST = function(requete) {
		var xmlHttp = this.ObtientXmlHttp() ;
		if(xmlHttp == null)
		{
			return ;
		}
		var contenu = null ;
		this.PrepareXmlHttp(xmlHttp, requete) ;
		try
		{
			var donnees = requete.ObtientChaineReqHttpParams() ;
			xmlHttp.open("POST", requete.ObtientUrl(), this.Asynchrone) ;
			xmlHttp.setRequestHeader("Content-type", requete.TypeContenuEntete);
			xmlHttp.setRequestHeader("Content-length", donnees.length);
			xmlHttp.setRequestHeader("Connection", requete.NatureConnexionEntete);
			requete.DemarreExecution(xmlHttp) ;
			contenu = xmlHttp.send(donnees) ;
		}
		catch(ex)
		{
			alert("Exception : " + ex.message) ;
		}
		return contenu ;
	}
	this.XmlHttp = this.ObtientXmlHttp() ;
}

function ParametreAjax(nom, valeur) {
	this.Nom = nom ;
	this.Valeur = valeur ;
	this.CommeChaineRequeteHttp = function () {
		var ctn = encodeURIComponent(this.Nom) + "=" ;
		if(typeof this.Valeur == 'string')
		{
			ctn += encodeURIComponent(this.Valeur) ;
		}
		else
		{
			if(typeof this.Valeur == 'object' && typeof this.Valeur.push != 'undefined')
			{
				for(var i=0; i<this.Valeur.length; i++)
				{
					if(i > 0)
						ctn += '&' ;
					ctn += encodeURIComponent(this.Valeur[i]) ;
				}
			}
		}
		return ctn ;
	}
}

function RequeteAjax() {
	this.AppelParent = null ;
	this.Url = "" ;
	this.SurChangeEtatAppel = null ;
	this.SurTermine = null ;
	this.SurEchoue = null ;
	this.SurSucces = null ;
	this.SurDelaiExpirationAtteint = null ;
	this.Methode = METHODE_APPEL_AJAX_GET ;
	this.TypeContenuEntete = "application/x-www-form-urlencoded" ;
	this.ContenuBrutContenuCorps = "" ;
	this.UtiliserContenuBrutContenuCorps = false ;
	this.NatureConnexionEntete = "keep-alive" ;
	this.Parametres = new Array() ;
	this.DelaiExpiration = 0.3 ;
	this.DelaiExpirationAtteint = false ;
	this.IdExpiration = 0 ;
	this.TempsDebutExecution = 0 ;
	this.TempsFinExecution = 0 ;
	this.DelaiExecution = 0 ;
	this.DemarreExecution = function(xmlHttp) {
		// this.TempsDebutExecution = new Date().UTC() ;
		this.DelaiExpirationAtteint = false ;
		var requeteEnCours = this ;
		if(this.DelaiExpiration > 0)
		{
			this.IdExpiration = setTimeout(function () {
				requeteEnCours.TermineExecution(xmlHttp) ;
			}, this.DelaiExpiration * 1000) ;
		}
	}
	this.ArreteExecution = function() {
		clearTimeout(this.IdExpiration) ;
		var maintenant = new Date() ;
		// this.TempsFinExecution = Date.UTC() ;
		this.DelaiExecution = this.TempsFinExecution - this.TempsDebutExecution ;
	}
	this.TermineExecution = function(xmlHttp) {
		this.DelaiExpirationAtteint = true ;
		this.ArreteExecution() ;
		if(this.SurDelaiExpirationAtteint != null)
		{
			this.SurDelaiExpirationAtteint(xmlHttp) ;
		}
	}
	this.AjouteParametres = function (objet, valeur) {
		if(typeof objet == 'object')
		{
			for(var n in objet)
			{
				this.Parametres.push(new ParametreAjax(n, objet[n])) ;
			}
		}
		else
		{
			if(typeof objet == 'string')
			{
				this.Parametres.push(new ParametreAjax(objet, valeur)) ;
			}
		}
	}
	this.ObtientUrl = function () {
		var partiesUrl = this.Url.split('?') ;
		var scriptDemande = partiesUrl[0] ;
		var arguments = "" ;
		for(var i=1; i<partiesUrl.length; i++)
		{
			arguments += partiesUrl[i] ;
		}
		var url = scriptDemande ;
		var parametres = this.ObtientChaineReqHttpParams() ;
		if(this.Methode == METHODE_APPEL_AJAX_GET)
		{
			if(arguments != "" || parametres != "")
			{
				if(parametres != "" && arguments != "")
					arguments += "&" ;
				arguments += parametres ;
			}
		}
		if(arguments != "")
			url += "?" + arguments ;
		// alert(url) ;
		return url ;
	}
	this.ObtientChaineReqHttpParams = function() {
		var chaineReq = "" ;
		if(this.UtiliserContenuBrutContenuCorps)
		{
			chaineReq += this.ContenuBrutContenuCorps ;
		}
		else
		{
			for(var i=0; i<this.Parametres.length; i++)
			{
				if(i > 0)
				{
					chaineReq += "&" ;
				}
				chaineReq += this.Parametres[i].CommeChaineRequeteHttp() ;
			}
		}
		return chaineReq ;
	}
}

function ReponseAjax() {
	this.TypeContenuEntete = "" ;
	this.ContenuCorps = "" ;
	this.SurTermine = null ;
	this.EtatChangement = 0 ;
	this.CodeStatutHttp = 0 ;
	this.NomServeurEntete = "" ;
	this.FourniParEntete = "" ;
	this.DateEntete = "" ;
	this.AnalyseXmlHttp = function (xmlHttp) {
		this.EtatChangement = xmlHttp.readyState ;
		this.CodeStatutHttp = xmlHttp.status ;
		this.ContenuCorps = xmlHttp.responseText ;
		// alert(xmlHttp.responseText) ;
	}
	this.ChargeEntetes = function (xmlHttp) {
		this.TypeContenuEntete = xmlHttp.getResponseHeader("Content-Type") ;
		this.NomServeurEntete = xmlHttp.getResponseHeader("Server") ;
		this.DateEntete = xmlHttp.getResponseHeader("Date") ;
		this.FourniParEntete = xmlHttp.getResponseHeader("X-Powered-By") ;
	}
	this.EstTermine = function() {
		return this.EtatChangement == 4 ;
	}
	this.EstEchoue = function() {
		return (this.EstTermine() && this.CodeStatutHttp != 200) ;
	}
	this.EstSucces = function() {
		return (this.EstTermine() && this.CodeStatutHttp == 200) ;
	}
}

function BlocAjax(id, methode) {
	var blocAjax = this ;
	this.IdBlocHtml = id ;
	this.AppelAjax = new AppelAjax() ;
	this.TexteMsgSurChargement = "Chargement en cours..." ;
	this.AlignMsgSurChargement = "center" ;
	this.TexteMsgSurExpirationAtteint = "La page a mis trop de temps pour repondre !!!" ;
	this.AlignMsgSurExpirationAtteint = "center" ;
	this.IntegreScriptsJs = true ;
	this.RequeteAjax = this.AppelAjax.CreeRequeteGET() ;
	this.RequeteAjax.Methode = (methode.toUpperCase() != METHODE_APPEL_AJAX_GET) ? METHODE_APPEL_AJAX_POST : METHODE_APPEL_AJAX_GET ;
	this.AssigneContenuHtml = function (contenu) {
		var blocHtml = this.ObtientNoeudHtml() ;		
		if(blocHtml != null)
		{
			blocHtml.innerHTML = contenu ;
			if(this.IntegreScriptsJs)
			{
				EvalScriptsDansBloc(blocHtml) ;
			}
		}
	}
	this.RequeteAjax.SurSucces = function (reponse, xmlHttp) {
		blocAjax.AssigneContenuHtml(reponse.ContenuCorps) ;
	}
	this.RequeteAjax.SurDelaiExpirationAtteint = function (reponse, xmlHttp) {
		blocAjax.AssigneContenuHtml(blocAjax.TexteMsgSurExpirationAtteint) ;
	}
	this.RequeteAjax.SurErreur = function (reponse, xmlHttp) {
		blocAjax.AssigneContenuHtml(reponse.ContenuCorps) ;
	}
	this.ObtientNoeudHtml = function () {
		var blocHtml = null ;
		if(this.IdBlocHtml != null)
		{
			blocHtml = document.getElementById(this.IdBlocHtml) ;
		}
		if(blocHtml == null)
		{
			alert("Le bloc HTML(id=" + this.IdBlocHtml + ") n'existe pas !!!") ;
			return null ;
		}
		return blocHtml ;
	}
	this.Remplit = function () {
		var msgChargement = '<div class="msgChargement" align="' + this.AlignMsgSurChargement + '">' + this.TexteMsgSurChargement + '</div>' ;
		this.AssigneContenuHtml(msgChargement) ;
		this.AppelAjax.Execute(this.RequeteAjax) ;
	}
	this.DefinitUrl = function (url) {
		this.RequeteAjax.Url = url ;
	}
	this.AjouteParametre = function (nom, valeur) {
		this.RequeteAjax.AjouteParametres(nom, valeur) ;
	}
	this.DefinitParametres = function (objet, valeur) {
		this.RequeteAjax.AjouteParametres(objet, valeur) ;
	}
}

function EvalScriptsDansBloc(blocHtml)
{
	var scripts = blocHtml.getElementsByTagName("script") ;
	var scriptDoc = document.createElement("script") ;
	scriptDoc.type = "text/javascript" ;
	for(var i=0; i<scripts.length; i++)
	{
		if(scripts[i].text != "")
		{
			var content = scripts[i].text ;
			scriptDoc.appendChild(document.createTextNode(content)) ;
			// scripts[i].parentNode.removeChild(scripts[i]) ;
		}
	}
	// alert(scriptDoc.innerHTML) ;
	document.body.appendChild(scriptDoc) ;
}
