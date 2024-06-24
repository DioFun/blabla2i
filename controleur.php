<?php
session_start();

	include_once "libs/maLibUtils.php";
	include_once "libs/maLibSQL.pdo.php";
	include_once "libs/maLibSecurisation.php"; 
	include_once "libs/modele.php"; 

	$qs = "";

	if ($action = valider("action"))
	{
		ob_start ();

		echo "Action = '$action' <br />";

		// Un paramètre action a été soumis, on fait le boulot...
		switch($action)
		{
			
			// Connexion //////////////////////////////////////////////////


			case 'Connexion' :
				// On verifie la presence des champs login et passe
				if ($login = valider("login"))
				if ($pass = valider("pass"))
				{
					// On verifie l'utilisateur, et on crée des variables de session si tout est OK
					// Cf. maLibSecurisation
					if (verifUser($login,$pass)){


						
						createFlash("success", "Connecté !");
						$qs = "?view=accueil";
						

					} else {
						createFlash("error", "Login ou mot de passe incorrect");
						$qs = "?view=login";
					}
				} else createFlash("error", "Login ou mot de passe incorrect");

				// On redirigera vers la page index automatiquement
				
			break;

			/*
			Nom : <input type="text" name="nom" placeholder="Nom"/><br />
			Prénom : <input type="text" name="prenom" placeholder="Prénom"/><br />
			mail : <input type="text" name="mail" placeholder="E-Mail (en @centrale.centralelille.fr)"/><br />
			Password : <input type="password" name="pass" placeholder="Mot de passe"/><br />
			Confirmer Password : <input type="password" name="secondpass" placeholder="Confirmez votre mot de passe"/><br />
			*/
			
			case 'Verify' :

				if (($id = valider("id"))&&($token = valider("token"))){
					
					$requete = recupConfirmationToken($id)[0];
					$currentTimestamp = time();

					// Convertir send_at en timestamp.
					$sendAtTimestamp = strtotime($requete["confirmation_send_at"]);


					if (!(($currentTimestamp - $sendAtTimestamp) < 3600)) {

						createFlash("error", "E-Mail non confirmé : token expiré");	

					} elseif ($token === $requete["confirmation_token"]){
						updateConfirmedMail($id);
						createFlash("success", "E-Mail confirmé");
					} else {
						createFlash("error", "E-Mail non confirmé : token invalide");
					}
					

				}

				$qs = "?view=login";
				

			break;

			case 'ChangerMDPMail' :

				if (($resetMail = valider("resetMail"))&&($id = valider("id"))){

					$resetToken = generateToken();
					putResetToken($id,$resetToken);
					sendResetEmail($resetMail, $resetToken, $id);
				}

				break;


			case 'ChangerMDP' :

				if (($tokenVal = valider("tokenVal"))&&($idVal = valider("idVal"))
					&&($newpassconfirm = valider("newpassconfirm"))&&($newpass = valider("newpass"))){

					$requete = recupResetToken($idVal)[0];
					$currentTimestamp = time();

					// Convertir send_at en timestamp.
					$sendAtTimestamp = strtotime($requete["reset_send_at"]);

					if ($newpassconfirm !== $newpass) {
						
						createFlash("error", "les mots de passes sont différents");

					} elseif (!(($currentTimestamp - $sendAtTimestamp) < 3600)) {

						createFlash("error", "Mot de passe non modifié : token expiré");	

					} elseif ($tokenVal === $requete["reset_token"]) {

						createFlash("success", "Mot de passe modifiés");
						updatePassword($idVal,$newpass);

					} else {

						createFlash("error", "Les tokens de reset ne sont pas les mêmes" );

					}				
				}

					$qs = "?view=login";
				

				break;

			case 'Create' :
				if (($nom = valider("nom"))
				  	&&($prenom = valider("prenom"))
					&&($mail = valider("mail"))
					&&($adress = strtolower(valider("adress")))
					&&($pass = valider("pass"))
					&&($planning = valider("planning"))
					&&($secondpass = valider("secondpass"))){

						

						$qs = verifCreateUser($nom,$prenom,$mail,$adress,$pass,$secondpass,$planning);

					} else {

						$qs = "?view=create&msg=". urlencode("Tous les champs doivent être remplis.");
					}
				
			break;

			case 'Logout' :

				session_destroy();
				$qs = "?view=login&msg=". urlencode("à bientot !");

			break;

			
		}

	}

	// On redirige toujours vers la page index, mais on ne connait pas le répertoire de base
	// On l'extrait donc du chemin du script courant : $_SERVER["PHP_SELF"]
	// Par exemple, si $_SERVER["PHP_SELF"] vaut /chat/data.php, dirname($_SERVER["PHP_SELF"]) contient /chat

	$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";
	
	// On redirige vers la page index avec les bons arguments

	header("Location:" . $urlBase . $qs);
	//qs doit contenir le symbole '?'

	// On écrit seulement après cette entête
	ob_end_flush();
	
?>










