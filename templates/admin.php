<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=login");
	die("");
}

// if (!valider("connecte",'SESSION') && !valider("isAdmin","SESSION"))
// {
// 	header("Location:index.php?view=accueil");
// 	die("");
// }
include_once("libs/modele.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibForms.php");

$infos = getUserInfos(valider("idUser","SESSION"));

?>

<script>
    function changeIconColor(){
        $(".icons:eq(3)").css("fill", "orange");
    }
</script>

<script> // Le formulaire d'édition des infos est caché par défaut, on l'affiche à la demande de l'utilisateur
    cache = true; //pas caché
    function editInfos(){
        console.log("editInfos");
        if (cache){
            document.getElementById("formEditInfos").style.display = "block";
            document.getElementById("settingStaticInfos").style.display = "none";
        }
        else{
            document.getElementById("formEditInfos").style.display = "none";
            document.getElementById("settingStaticInfos").style.display = "block";
        }
        cache = !cache;
    }

</script>

<div id="settingInfos">
    <button onclick="editInfos()">Edit</button>
    <div id="formEditInfos" style="display:none;">
        <form action="controleur.php" method="GET">
            <p>Nom : </p><input type="text" name="nom" placeholder="Nom" value="<?=$infos["lastname"]?>"/><br />
            <p>Pr&eacute;nom : </p><input type="text" name="prenom" placeholder="Prénom" value="<?=$infos["firstname"]?>"/><br />
            <p>Email : </p><input type="text" name="mail" placeholder="E-Mail (en @centrale.centralelille.fr)" value="<?=$infos["email"]?>"/><br />
            <p>Adresse : </p><input type="text" name="adress" placeholder="Adresse" value="<?=$infos["adress"]?>"/><br />
            <input type="submit" name="action" value="ModifyInfos" />
        </form>
    </div>
    <div id="settingStaticInfos" style="display:block;">
        <p>Nom : <?=$infos["lastname"]?></p>
        <p>Prénom : <?=$infos["firstname"]?></p>
        <p>Mail : <?=$infos["email"]?></p>
        <p>Adresse : <?=$infos["adress"]?></p>
    </div>
</div>

<br>

<!-- Première section importante de la page admin, les voitures -->
<div id="voituresProfile">
    <!-- Là c'est le formulaire pour ajouter une voiture -->
    <div id="formCreationVoiture">
        <form action="controleur.php" method="GET">
            Immatriculation de la Voiture : <input type="text" name="registrationCar" placeholder="Entrez l'immatriculation de votre voiture"/><br />

            <input type="submit" name="action" value="CreationVoiture" />
            
        </form>
    </div>
    <!-- Là c'est la liste avec possibilité de supprimer les voitures -->
    <?php showVehicleList(getUserCar($_SESSION["idUser"]));?>
</div>


<!-- Deuxième section importante de la page admin, les utilisateurs -->
<?php
    showUsersList(); //afficher la liste des utilisateurs
?>

<br>

<!-- Troisième section importante de la page admin, les utilisateurs bannis -->
<?php
    showBannedUsersList(); //afficher la liste des utilisateurs bannis
?>