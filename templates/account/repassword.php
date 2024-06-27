<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=account.repassword");
	die("");
}


?>
 
<div id="corps">

	<img src="../../ressources/logo.png" alt="Logo">

<h1>EMail de réinitialisatiion</h1>


<div id="formLogin">
	<form action="controleur.php" method="GET">
		email : <input type="text" name="resetMail" placeholder="Entrez votre email"/><br />
		<input type="submit" name="action" value="ChangerMDPMail" />
	</form>
</div>


</div>
