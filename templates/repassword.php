<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=create");
	die("");
}


?>
 
<div id="corps">

	<img src="URL_de_l'image" alt="Logo">

<h1>Réinitialiser votre mot de passe</h1>



<?=$info?>

<div id="formLogin">
	<form action="controleur.php" method="GET">
		email : <input type="text" name="mail" placeholder="Entrez votre email"/><br />
		<input type="submit" name="action" value="Changer1" />
	</form>
</div>


</div>
