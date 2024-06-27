<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=login");
	die("");
}

if (!valider("connecte",'SESSION') && !valider("isAdmin","SESSION"))
{
	header("Location:index.php?view=accueil");
	die("");
}
include_once("libs/modele.php");
include_once("libs/maLibUtils.php");
include_once("libs/maLibForms.php");

$infos = getUserInfos(valider("idUser","SESSION"));

?>


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