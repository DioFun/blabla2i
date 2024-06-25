<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=login");
	die("");
}

if (!valider("connecte",'SESSION'))
{
	header("Location:index.php?view=accueil");
	die("");
}
include_once("libs/modele.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibForms.php");

$infos = getUserInfos($_SESSION["idUser"]);
?>

<style>
    .liste{
        width: 90%;
        min-height: 20px;
        border: 2ch solid black;
        padding: 10px;
        margin: 10px;
    }
</style>
<script> //Les formulaire d'ajout/modif de calendrier et de numéro sont cachés par défaut, on les affiches à la demande de l'utilisateur
    createForm(which){
        if (which == "cal"){
            document.getElementById("formCreationVoiture").style.display = "block";
        }
        else if (which == "num"){
            document.getElementById("formCreationVoiture").style.display = "block";
        }
    }
    createFormCal(){
        createForm("cal");
    }
    createFormNum(){
        createForm("num");
    }
</script>

<script> //fonction pour afficher la première ou la deuxième section dans le profile (à propos : 0 & Compte : 1)
function display(which){
    if (which == 0){
        document.getElementById("infos").style.display = "block";
        document.getElementById("settingInfos").style.display = "none";
        document.getElementById("verifInfos").style.display = "block";
        document.getElementById("voituresProfile").style.display = "block";
    }
    else if (which == 1){
        document.getElementById("infos").style.display = "none";
        document.getElementById("settingInfos").style.display = "block";
        document.getElementById("verifInfos").style.display = "none";
        document.getElementById("voituresProfile").style.display = "none";
    }

}

</script>

<!-- Mockup : Ici c'est la barre de navigation qui permet de naviguer entre les différentes sections du profile -->
<div id="nav">
    <button onclick="display(0)">A propos de vous</button>
    <button onclick="display(1)">Compte</button>
</div>

<!-- Mockup : Ici c'est la première section "A propos de vous" qui apparaît avec Prénom + Nom + Mail -->
<div id="infos">
    <h1><?=$_SESSION["pseudo"]?></h1>
    <h3><?=$infos["email"]?></h3>
</div>


<!-- Mockup : Ici c'est la deuxième section "Compte" qui apparaît avec la possibilité de modifier ses infos -->
<div id="settingInfos" display="none">
    <button onclick="">Edit</button>
    <form display="none">
        <input type="text" name="nom" placeholder="Nom"/><br />
        <input type="text" name="prenom" placeholder="Prénom"/><br />
        <input type="text" name="mail" placeholder="E-Mail (en @centrale.centralelille.fr)"/><br />
        <input type="text" name="adress" placeholder="Adresse"/><br />
        <input type="submit" name="action" value="Create" />
    </form>
    <p>Prénom : <?=$infos["firstname"]?></p>
    <p>Nom : <?=$infos["lastname"]?></p>
    <p>Mail : <?=$infos["email"]?></p>
    <p>Adresse : <?=$infos["adress"]?></p>
</div>

<!-- Mockup : Retour sur la première section qui permet d'afficher les forms pour l'ajout / la modification du calendrier et du numéro de tél de l'utilisateur -->
<div id="verifInfos">
    <button onclick="createFormCal(this)">Ajouter / Modifier un calendrier</button>
    <form display="none">
        <input type="text" name="calURL" placeholder="URL du calendrier"/>
        <input type="submit" name="action" value="CreationCal" />
    </form>
    <button onclick="createFormNum(this)">Ajouter / Modifier un numéro</button>
    <form display="none">
        <input type="text" name="num" placeholder="Numéro à ajouter"/>
        <input type="submit" name="action" value="CreationNum" />
    </form>
</div>


<!-- Mockup : Toujours la première section avec la liste des voitures de l'utilisateur et la possibilité d'en rajouter une-->
<div id="voituresProfile">
    <!-- Là c'est le formulaire pour ajouter une voiture -->
    <div id="formCreationVoiture">
        <form action="controleur.php" method="GET">
            Immatriculation de la Voiture : <input type="text" name="registrationCar" placeholder="Entrez l'immatriculation de votre voiture"/><br />

            <input type="submit" name="action" value="CreationVoiture" />
            
        </form>
    </div>
    <!-- Là c'est la liste -->
    <?php showVehicleList(getUserCar($_SESSION["idUser"]));?>
</div>