<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=account.create");
	die("");
}

?>
 
<div id="corps">

	<img src="../../ressources/logo.png" alt="Logo">

<h1>Créer un compte</h1>



<div id="formLogin">
	<form action="controleur.php" method="GET">
		Nom : <input type="text" name="nom" placeholder="Nom"/><br />
		Prénom : <input type="text" name="prenom" placeholder="Prénom"/><br />
		mail : <input type="text" name="mail" placeholder="E-Mail (en @centrale.centralelille.fr)"/><br />
		Adresse : <input type="text" name="adress" placeholder="Adresse"/><br />
		Lien Ical planning : <input type="text" name="planning" placeholder="Lien Ical (trouvable sur l'ENT) de votre planning"/><br />
		Mot de passe : <input type="password" name="pass" placeholder="Mot de passe"/><br />
		Confirmer le mot de passe : <input type="password" name="secondpass" placeholder="Confirmez votre mot de passe"/><br />
		<input type="submit" name="action" value="Create" />
	</form>
</div>


</div>
