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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
	var cache; // on associe userId et le nom de l'utilisateur pour l'affichage
	<?php
		echo "var userId =".$_SESSION["idUser"]; // pour la mise en page (utilisateur connecté à droite dans le chat)
	?>

	var jMessage = $("<div class = \"Message\">")
												.append("<div class =\"senderName\">")
												.append("<div class = \"messContent\">")
												.append("<div class = \"messDate\">");

	function getMessages(){
		$.ajax({
			type : "GET",
			url : "controleur.php",
			data : {"action" : "getChat"
					<?php
					if ($tripId=valider("tripId")) {
						echo "'tripId' : '$tripId'";
					}
					if ($userId=valider("userId")) {
						echo "'userId' : '$userId'";
					}
					?>},
			success : function(oRep){
				console.log(oRep);
				return JSON.parse(oRep);
			}
		})

		function displayMessages(){
			var messages = getMessages();
			var i;

			for (i = 0; i<messages.legnth; i++){
				var jCloneMessage = jMessage.clone();
				if (!cache.hasOwnProperty(messages[i].sender_id)){
					$.ajax({
						type: "GET",
						url : "controleur.php",
						data : {
							"action" : "getUserName",
							"userId" : messages[i].sender_id
						},
						succes : function(oRep){
							console.log(oRep);
							cache.messages[i].sender_id = oRep;
						}
					})
				}
				jCloneMessage.children(".senderName").html(cache.messages[i].sender_id);
				jCloneMessage.children(".messContent").html(messages[i].content);
				jCloneMessage.children(".messDate").html(messages[i].created_at);

				if (messages[i].sender_id == userId) jCloneMessage.addClass("loggedInUserMessage"); // pour différencier message de l'utilisateur connecté et les autres
				$("#chatCont").append(jCloneMessage);
			}
		}
	}
</script>

<!--
<div class="message loggedInUserMessage">
	<div class ="senderName">Nom sender</div>
	<div class = "messContent">Contenu</div>
	<div class = "messDate">Date & heure</div>
</div>
-->

<div id="chatCont">

</div>