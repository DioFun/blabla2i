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

<!-- Le style est à adapter mais l'idée y est-->
<style>
#topBarConv{
    position: relative;
    left: 0;
    right: 0;
    top:0;
    padding: 5px;
    border-bottom: 1px solid black;
    text-align: center;
}

#archiveButton{
    position: absolute;
    bottom : 5px;
    right : 5px;
}

#titleConv{
    text-align: center;
    display: inline;
}

.conversation{
    padding-top: 15px;
    padding-bottom: 15px;
    margin-top: 0;
    margin-bottom: 0;
}

.conversation:hover{
    background-color: aqua;
}

.convName, .convDate, .convMessage{
    display: inline;
}

.convDate{
    float: right;
}

.convPp{
    float: left;
}

#loadMoreButton{
    text-align: center;
    background-color: blueviolet;
    padding: 10px;
    cursor: pointer;
}

#loadMoreButton:hover{
    background-color: blue;
}
</style>

<script src="jquery-3.7.1.min.js"></script>
<script>
	var i;
	var jConversation = $("<div class = \"conversation\">")
						.append("<img/>")
						.append("<div class = \"convName\">")
						.append("<div class = \"convMessage\">")
						.append("<div class = \"convDate\">");

    function refreshMessages(){
        $("#convCont").html("");
		var convArray;
        $.ajax({
            type : "GET",
            url : "controleur.php",
            data : {"action" : "getConversations"},
            success : function(oRep){
                var convJSON = JSON.parse(oRep);
                convArray = (convJSON.trip).concat(convJSON.user).concat(convJSON.general);
                convArray.sort(convDateSort);
                console.log(convArray);
                var i;
                for (i = 0; i<4; i++){
                    console.log("test boucle");
                    var jCloneConv = jConversation.clone();
                    if (convArray[i].hasOwnProperty("tripId")){
                        jCloneConv.children(".convname").html(convArray[i].t.date + " " + convArray[i].t.heure + " "+ convArray[i].t.departure);
                        jCloneConv.click(function () {
                            window.location.replace("index.php?view=chat&tripId=" + convArray[i].tripId);
                        });
                    } else if (convArray[i].hasOwnProperty("userId")) {
                        jCloneConv.children(".convname").html(convArray[i].firstname + " " + convArray[i].lastname);
                        jCloneConv.click(function () {
                            window.location.replace("index.php?view=chat&userId=" + convArray[i].userId);
                        });
                    } else {
                        jCloneConv.children(".convname").html("Général");
                        jCloneConv.click(function () {
                            window.location.replace("index.php?view=chat");
                        });
                    }
                    jCloneConv.children(".convMessage").html(convArray[i].firstname + " " + convArray[i].lastname + " : " + convArray[i].content);
                    jCloneConv.children(".convDate").html(convArray[i].created_at);

                    $("#convCont").append(jCloneConv);
                }
            }
        });
        /*
        var i;
		for (i = 0; i<4; i++){
            console.log("test boucle");
			var jCloneConv = jConversation.clone();
			if (convArray[i].hasOwnProperty("tripId")){
				jCloneConv.children(".convname").html(convArray[i].t.date + " " + convArray[i].t.heure + " "+ convArray[i].t.departure);
				jCloneConv.click(window.location.replace("index.php?view=chat&tripId="+convArray[i].tripId));
			} else if (convArray[i].hasOwnProperty("userId")) {
				jCloneConv.children(".convname").html(convArray[i].firstname + " " + convArray[i].lastname);
				jCloneConv.click(window.location.replace("index.php?view=chat&userId="+convArray[i].userId));
			} else {
				jCloneConv.children(".convname").html("Général");
				jCloneConv.click(window.location.replace("index.php?view=chat"));
			}
			jCloneConv.children(".convMessage").html(convArray[i].firstname + " " + convArray[i].lastname + " : " + convArray[i].content);
			jCloneConv.children(".convDate").html(convArray[i].created_at);

			$("#convCont").append(jCloneConv);
		}

         */
    }

    function getConversations(){
		$.ajax({
			type : "GET",
			url : "controleur.php",
            data : {"action" : "getConversations"},
			success : function(oRep){
				var convJSON = JSON.parse(oRep);
				convArray = (convJSON.trip).concat(convJSON.user).concat(convJSON.general);
				convArray.sort(convDateSort);
			}
		})
    }

	function convDateSort(a, b){
		if (a.created_at < b.created_at) return -1;
		if (a.created_at > b.created_at) return 1;
		return 0;
	}

    $(document).ready(function(){
        console.log("Ok");
        $("#newMessageForm").hide();
        refreshMessages();
        refreshConv = setInterval(refreshMessages, 10000);
        $("#newMessageTo").click(function(){
            $("#newMessageForm").show();
        });
        $("#newMessageForm input[type='button']").click(function(){
            $.ajax({
                url : "controleur.php",
                type : "POST",
                data : {
                    "action" : "newMessage",
                    "senderId" :   <?php 
                                        echo $_SESSION["idUser"];
                                    ?>,
                    "receiverId" : $("#receiver").data("userId"),
                    "content" : $("#content").val()
                }, 
                success : function(oRep){
                    console.log(oRep);
                    refreshMessages();
                }
            })
        });

        $("#receiver").keyup(function(){
            $("#suggest").html("");
            var userInput = $("#receiver").val();
            if (userInput == "") $("#suggest").hide();
            else {
                $.ajax({
                    url : "controleur.php",
                    type : "GET",
                    data : {
                        "action" : "suggestUser",
                        "debut" : userInput
                    },
                    success : function(Orep){
                        console.log(Orep);
                        var repJSON = JSON.parse(Orep);
                        var i;
                        for (i = 0; i<repJSON.length; i++){
                            var option = $("<div>").html(repJSON[i].firstname + " " + repJSON[i].lastname)
                                                    .data("userId", repJSON[i].id)
                                                    //.click(remplir(repJSON[i].firstname + " " + repJSON[i].lastname, repJSON[i].id));
                                                    .click(function(){
                                                        $("#receiver").val($(this).html())
                                                                    .data($(this).data());
                                                    })
                            $("#suggest").append(option);
                            $("#suggest").show();
                        }
                    }
                })
            }
        })
    })
</script>

<body>
    <div id="topBarConv">
        <div id="titleConv">Conversations</div>
        <div id = "newMessageTo">Nouveau message</div>
    </div>

<!-- On place les conversations dans des divs de classe .conversation-->

<!--
<div class = "conversation">
	<img/>
	<div class = "convName">Nom de la conversation</div>
    <div class = "lastSender">Auteur du dernier message ("moi" ou nom de qqn d'autre)</div>
    <div class = "convMessage">Dernier message en date</div>
	<div class = "convDate">Date ou heure du dernier message</div>
</div>
-->

    <div id="newMessageForm">
        <input type="text" placeholder="Destinataire" id="receiver"/>
        <div id = "suggest"></div>
        <input type="text" placeholder="Message" id="content"/>
        <input type="button" value = "Envoyer"/>
    </div>

    <div id="convCont">
        <div class = "conversation">
            <img class = "convPp"/>
            <div class = "convName">Nom de la conversation</div>
            <div class = "convMessage">Dernier message en date</div>
            <div class = "convDate">Date ou heure du dernier message</div>
        </div>
    </div>


<!-- Load more button-->

    <div id="loadMoreButton">
        <div>Charger plus de messages</div>
    </div>

</body>