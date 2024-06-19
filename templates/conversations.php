<?php
// Ce fichier permet de tester les fonctions développées dans le fichier malibforms.php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) == "conversations.php")
{
	header("Location:../index.php?view=conversations");
	die("");
}

include_once("libs/modele.php"); // listes
include_once("libs/maLibUtils.php");// tprint
include_once("libs/maLibForms.php");// mkTable, mkLiens, mkSelect ...



?>

<h1>Conversations du site</h1>

<h2>Liste des conversations actives</h2>

<?php

$conversations = listerConversations("actives");

// mkTable($conversations, array("id","theme")); 
mkLiens($conversations,"theme","id","infex.php?view=chat","idConv");
// Comment n'afficher que id & thèmes ?
// A remplacer par mkLiens
?>

<h2>Liste des conversations inactives</h2>

<?php
$conversations = listerConversations("inactives");
//mkTable($conversations, array("id","theme")); 
mkLiens($conversations,"theme","id","infex.php?view=chat","idConv");
// A remplacer par mkLiens
?>

<hr />
<h2>Gestion des conversations</h2>

<?php

$conversations = listerConversations(); // toutes
mkTable($conversations); // A remplacer par mkSelect

mkForm("controleur.php");
mkSelect("idConv", $conversations, "id", "theme" );
mkInput("submit","action", "Archiver");
mkInput("submit","action", "Réactiver");
mkInput("submit","action", "Supprimer");
endForm();

?>















