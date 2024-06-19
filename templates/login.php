<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=login");
	die("");
}

$info = "";
if ($msg = valider("msg")) {
	$info = "<h3 style = \"color:red; \">$msg</h3>";
}

?>

<div id="corps">

<h1>Connexion</h1>

<?=$info?>

<div id="formLogin">
	<form action="controleur.php" method="GET">
		Login : <input type="text" name="login" placeholder="Entrez votre e-mail"/><br />
		Passe : <input type="password" name="passe" placeholder="Entrez votre mot de passe"/><br />
		<input type="submit" name="action" value="Connexion" />
		
	</form>
</div>


</div>
