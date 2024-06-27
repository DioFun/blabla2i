<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=chat&" . $_SERVER["QUERY_STRING"]);
	// Il faut renvoyer le reste de la chaine de requete... 
	// A SUIVRE : ne marche que pour requetes GET
	// Un appel à http://localhost/chatISIG/templates/chat.php?idConv=2
	// renvoie vers http://localhost/chatISIG/index.php?view=chat&idConv=2
	
	die("");
}

include_once("libs/modele.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibForms.php");
?>















