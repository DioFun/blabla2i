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

<!-- Mockup : Ici c'est la première section "A propos de vous" qui apparaît avec Prénom + Nom + Mail -->
<div id="infos">
    <h1><?=$_SESSION["pseudo"]?></h1>
    <h3><?=$infos["email"]?></h3>
</div>


<!-- Mockup : Ici c'est la deuxième section "Compte" qui apparaît avec la possibilité de modifier ses infos -->
<div id="settingInfos" display="none">
    <button onclick="">Edit</button>
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

<!-- Là c'est le formulaire pour ajouter une voiture -->
<div id="formCreationVoiture">
	<form action="controleur.php" method="GET">
		Immatriculation de la Voiture : <input type="text" name="registrationCar" placeholder="Entrez l'immatriculation de votre voiture"/><br />

		<input type="submit" name="action" value="CreationVoiture" />
		
	</form>
</div>
<!-- Là c'est la liste  -->
<div class="liste">
    <h1>
        Mes voitures
    </h1>
    <?php
        $voitures = getUserCar($_SESSION["idUser"]);
        foreach($voitures as $voiture){
            ?>
                <div class="voiture">
                    <img src="../ressources/ec-lille.png" alt="Logo Voiture">
                    <p><?=$voiture["registration"]?></p>
                </div>
            
            <?php
        }
    ?>
</div>