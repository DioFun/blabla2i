<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=login");
	die("");
}

// if (!valider("connecte",'SESSION'))
// {
// 	header("Location:index.php?view=accueil");
// 	die("");
// }
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
    .section0{
        display: block;
    }
    .section1{
        display: none;
    }
</style>
<script> //Les formulaire d'ajout/modif de calendrier et de numéro sont cachés par défaut, on les affiches à la demande de l'utilisateur
    function createForm(which){
        if (which == "cal"){
            form = document.getElementById("formAjoutCal");
            if (form.style.display == "block"){
                form.style.display = "none";
            }
            else{
                form.style.display = "block";
            }
        }
        else if (which == "num"){
            form = document.getElementById("formAjoutNum");
            if (form.style.display == "block"){
                form.style.display = "none";
            }
            else{
                form.style.display = "block";
            }
        }
    }
    function createFormCal(){
        createForm("cal");
    }
    function createFormNum(){
        createForm("num");
    }
</script>

<script> //fonction pour afficher la première ou la deuxième section dans le profile (à propos : 0 & Compte : 1)
function display(which){
    if (which == 0){
        s0 = document.getElementsByClassName("section0");
        for (i = 0; i < s0.length; i++){
            s0[i].style.display = "block";
        }
        s1 = document.getElementsByClassName("section1");
        for (i = 0; i < s1.length; i++){
            s1[i].style.display = "none";
        }
    }
    else if (which == 1){
        s0 = document.getElementsByClassName("section0");
        for (i = 0; i < s0.length; i++){
            s0[i].style.display = "none";
        }
        s1 = document.getElementsByClassName("section1");
        for (i = 0; i < s1.length; i++){
            s1[i].style.display = "block";
        }
    }

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
<!-- Mockup : Ici c'est la barre de navigation qui permet de naviguer entre les différentes sections du profile -->
<div id="nav">
    <button onclick="display(0)">A propos de vous</button>
    <button onclick="display(1)">Compte</button>
</div>

<!-- Mockup : Ici c'est la première section "A propos de vous" qui apparaît avec Prénom + Nom + Mail -->
<div id="infos" class="section0">
    <h1><?=$_SESSION["pseudo"]?></h1>
    <h3><?=$infos["email"]?></h3>
</div>


<!-- Mockup : Ici c'est la deuxième section "Compte" qui apparaît avec la possibilité de modifier ses infos -->
<div id="settingInfos" class="section1">
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

<!-- Mockup : Retour sur la première section qui permet d'afficher les forms pour l'ajout / la modification du calendrier et du numéro de tél de l'utilisateur -->
<div id="verifInfos" class="section0">
    <button onclick="createFormCal()">Ajouter / Modifier un calendrier</button>
    <form id="formAjoutCal" style="display : none;">
        <input type="text" name="calURL" placeholder="URL du calendrier"/>
        <input type="submit" name="action" value="CreationCal" />
    </form>
    <br>
    <button onclick="createFormNum()">Ajouter / Modifier un numéro</button>
    <form id="formAjoutNum" style="display : none;">
        <input type="text" name="num" placeholder="Numéro à ajouter"/>
        <input type="submit" name="action" value="CreationNum" />
    </form>
</div>

<br>

<!-- Mockup : Toujours la première section avec la liste des voitures de l'utilisateur et la possibilité d'en rajouter une-->
<div id="voituresProfile" class="section0">
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