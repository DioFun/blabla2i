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
	<?php
		echo "var userId =".$_SESSION["idUser"]; // pour la mise en page (utilisateur connecté à droite dans le chat)
	?>

	var jMessage = $("<div class = \"Message\">")
												.append("<div class =\"senderName\">")
												.append("<div class = \"messContent\">")
												.append("<div class = \"messDate\">");

		function displayMessages() {
            $.ajax({
                type: "GET",
                url: "controleur.php",
                data: {
                    "action": "getChat",
                    <?php
                    if ($tripId = valider("tripId")) {
                        echo "'tripId' : '$tripId'";
                    }
                    if ($userId = valider("userId")) {
                        echo "'userId' : '$userId'";
                    }
                    ?>},
                success: function (oRep) {
                    var messages = JSON.parse(oRep);
                    console.log(messages);
                    var i;
                    $("#chatCont").html("");
                    for (i = 0; i < messages.length; i++) {
                        var jCloneMessage = jMessage.clone();
                            $.ajax({
                                type: "GET",
                                url: "controleur.php",
                                data: {
                                    "action": "getUserName",
                                    "userId": messages[i].sender_id
                                },
                                succes: function (oRep) {
                                    console.log(oRep);
                                    jCloneMessage.children(".senderName").html(oRep);
                                }
                            });
                        jCloneMessage.children(".messContent").html(messages[i].content);
                        jCloneMessage.children(".messDate").html(messages[i].created_at);

                        if (messages[i].sender_id == userId) jCloneMessage.addClass("loggedInUserMessage"); // pour différencier message de l'utilisateur connecté et les autres
                        $("#chatCont").append(jCloneMessage);
                    }
                }
            })
        }


	function sendMessage(){
		$.ajax({
			type : "POST",
			url : "controleur.php",
			data : {
				"action" : "newMessage",
				"senderId" : userId,
				"content" : $("input [type=text]").val(),
				<?php
					if ($tripId=valider("tripId")) {
						echo "'tripId' : '$tripId'";
					}
					if ($receiverId=valider("receiverId")) {
						echo "'receiverId' : '$receiverId'";
					}
					?>
            },
			success : function(){
				displayMessages();
			}
		})
	}

	$(document).ready(function(){
		displayMessages();
		refreshChat = setInterval(displayMessages, 1000);
	})
</script>

<!--
<div class="message loggedInUserMessage">
	<div class ="senderName">Nom sender</div>
	<div class = "messContent">Contenu</div>
	<div class = "messDate">Date & heure</div>
</div>
-->
<body>
	<div id="chatCont">

	</div>

	<input type="text"/>
	<input type="button" value ="Envoyer" onclick="sendMessage()"/>
</body>