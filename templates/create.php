<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=create");
	die("");
}

$info = "";
if ($msg = valider("msg")) {
	$info = "<h3 style = \"color:red; \">$msg</h3>";
}

?>
 
<div id="corps">

	<img src="URL_de_l'image" alt="Logo">

<h1>Créer un compte</h1>



<?=$info?>

<div id="formLogin">
	<form action="controleur.php" method="GET">
		Nom : <input type="text" name="nom" placeholder="Nom"/><br />
		Prénom : <input type="text" name="prenom" placeholder="Prénom"/><br />
		mail : <input type="text" name="mail" placeholder="E-Mail (en @centrale.centralelille.fr)"/><br />
		Password : <input type="password" name="pass" placeholder="Mot de passe"/><br />
		Confirmer Password : <input type="password" name="secondpass" placeholder="Confirmez votre mot de passe"/><br />
		<input type="submit" name="action" value="Create" />
	</form>
</div>


</div>
