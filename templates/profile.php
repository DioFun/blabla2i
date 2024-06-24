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

?>

<style>
    .liste{
        width: 90%;
        min-height: 20px;
        border: 2ch solid black;
    }
</style>

<div id="formCreationVoiture">
	<form action="controleur.php" method="GET">
		Immatriculation de la Voiture : <input type="text" name="registrationCar" placeholder="Entrez l'immatriculation de votre voiture"/><br />

		<input type="submit" name="action" value="CreationVoiture" />
		
	</form>
</div>

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