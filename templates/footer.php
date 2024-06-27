<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php");
	die("");
}

?>

<div id="pied">

<?php


// Si l'utilisateur est connecte, on affiche un lien de deconnexion 
if (valider("connecte","SESSION"))
{
	echo "Utilisateur <b>$_SESSION[pseudo]</b> connecté depuis <b>$_SESSION[heureConnexion]</b> &nbsp; "; 
	echo "<a href=\"controleur.php?action=Logout\">Se Déconnecter</a>";
}
?>
</div>

<script>
    // Fonction pour supprimer une notification
    function removeNotif(id){
        $.ajax({
            url: "controleur.php",
            type: "GET",
            data: {action: "DeleteNotif", id: id},
            success: function(){
                $("#notif"+id).hide();
            }
        });


    }
</script>

<script src="jquery-3.7.1.min.js"></script>

<script>

    // Fonction pour supprimer une voiture
    function deleteCar(id){
        $.ajax({
            url: "controleur.php",
            type: "GET",
            data: {action: "SuppressionVoiture", idCar: id},
            success: function(){
                $("#voiture"+id).hide();
            }
        });
    }
</script>

<script>
    // Fonctions pour bannir ou débannir un utilisateur
    // ban
    function banUser(id){
        $.ajax({
            url: "controleur.php",
            type: "GET",
            data: {action: "BanUser", idBanUser: id},
            success: function(){
                $("#listeBannedUsers").append($("#user"+id));
                $("#user"+id).find("button").attr("onclick", "unbanUser("+id+")").text("Débannir");
            }
        });
    }

    // unban
    function unbanUser(id){
        $.ajax({
            url: "controleur.php",
            type: "GET",
            data: {action: "UnBanUser", idUnBanUser: id},
            success: function(){
                $("#listeUsers").append($("#user"+id));
                $("#user"+id).find("button").attr("onclick", "banUser("+id+")").text("Bannir");
            }
        });
    }
</script>

</body>
</html>
