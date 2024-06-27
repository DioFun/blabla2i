<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=account.login");
	die("");
}

if (valider("connecte",'SESSION'))
{
	header("Location:index.php?view=accueil");
	die("");
}



?>
 
<script>
    function changeIconColor(){
        $(".icons:eq(3)").css("fill", "orange");
    }
</script>

<div id="corps">

	<img src="../../ressources/logo.png" alt="Logo">

<h1>Connexion</h1>





<div id="formLogin">
	<form action="controleur.php" method="GET">
		Login : <input type="text" name="login" placeholder="Entrez votre e-mail"/><br />
		Password : <input type="password" name="pass" placeholder="Entrez votre mot de passe"/><br />

		<a href="index.php?view=account.create">Créer un compte</a>
		<a href="index.php?view=account.repassword">Mot de passe oublié ?</a>

		<input type="submit" name="action" value="Connexion" />
		
	</form>
</div>


</div>
