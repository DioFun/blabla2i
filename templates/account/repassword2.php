<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=account.repassword2");
	die("");
}


if (($token === valider("token"))&&($id === valider("id"))): ?>

	
	

 
<div id="corps">

	<img src="URL_de_l'image" alt="Logo">

<h1>Réinitialiser votre mot de passe</h1>





<div id="formLogin">
	<form action="controleur.php" method="GET">
		Nouveau mot de passe : <input type="text" name="newpass" placeholder="Entrez votre nouveau mot de passe"/><br />
		Confirmez le mot de passe : <input type="text" name="newpassconfirm" placeholder="Confirmez votre nouveau mot de passe"/><br />
		<input type="hidden" value=<?= $token ?> name="tokenVal"/>
		<input type="hidden" value=<?= $id ?> name="idVal"/>
		<input type="submit" name="action" value="ChangerMDP" />
	</form>
</div>


</div>

<?php endif; ?>
