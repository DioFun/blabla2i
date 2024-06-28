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

//		echo "Action = '$action' <br />";

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
						createFlash("error", "Login ou mot de passe incorrect ou veillez à valider votre email !");
						$qs = "?view=account.login";
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

				$qs = "?view=account.login";


			break;

			case 'ChangerMDPMail' :

				if (($resetMail = valider("resetMail"))){

					$resetToken = generateToken();
					putResetToken($resetMail,$resetToken);

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

					$qs = "?view=account.login";


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
						createFlash("error", "Tous les champs doivent être remplis.");
						$qs = "?view=account.create";
					}
				
			break;

			case 'Logout' :

				unset($_SESSION['pseudo'], $_SESSION['idUser'], $_SESSION['isAdmin'], $_SESSION['connecte'], $_SESSION['heureConnexion']);
				createFlash("success", "Déconnecté !");
				$qs = "?view=account.login";

			break;

			case 'trajets.create' :
				$destination = valider("destination") ? "ig2i" : "centrale";
				$isDriving = valider("driver");
				if (($departure = valider("departure")) && ($date = valider("date"))
					&& ($time = valider("time")) && ($passengers = valider("passengers"))
                    && ($vehicle = valider("car"))) {
					if (!createTrip($isDriving, $departure, $destination, $date, $time, $passengers, $vehicle)) createFlash("error", "Une erreur est survenue lors de la création !");
					else createFlash("success", "Le trajet a bien été créé !");
					$qs = '?view=accueil';

				} else  {
					$qs = "?view=trajets.create";
					createFlash("error", "Un ou plusieurs paramètres ne sont pas correctements remplis !");
				}
			break;

            case "trajets.remove":
                $id = valider("id");
                if (canEditTrip($id)) {
                    removeTrip($id);
                    createFlash("success", "Le trajet a bien été supprimé !");
                }
                $qs = "?view=accueil";
            break;
            case "trajets.edit":
                if (!canEditTrip(valider('id'))) {
                    $qs = "?view=accueil";
                } else {
                    $id = valider("id");
                    $destination = valider("destination") ? "ig2i" : "centrale";
                    $isDriving = valider("driver");
                    if (($departure = valider("departure")) && ($date = valider("date"))
                        && ($time = valider("time")) && ($passengers = valider("passengers"))
                        && $vehicle = valider("car")) {
                        editTrip($id, $isDriving, $departure, $destination, $date, $time, $passengers, $vehicle);
                        createFlash("success", "Le trajet a bien été modifié !");
                        $qs = "?view=trajets.view&id=$id";

                    } else {
                        $qs = "?view=trajets.edit&id=$id";
                        createFlash("error", "Un ou plusieurs paramètres ne sont pas correctements remplis !");
                    }
                }
            break;
            case "trajets.join":
                if ($id = valider("id")) {
                    if (!joinTrip($id)) createFlash("error", "Impossible de rejoindre ce trajet.");
                    else createFlash('success', "Vous avez rejoins le trajet.");
                    $qs="?view=trajets.view&id=$id";
                }
            break;

            case "trajets.remove-passenger" :
                $id = valider("user_id");
                $id = $id ?: valider("idUser", "SESSION");
                if ($tripId = valider("trip_id")) {
                    if (removePassenger($id, $tripId)) createFlash("success", "Passager supprimé !");
                    else createFlash("error", "Impossible de supprimer ce passager.");
                    $qs = "?view=trajets.view&id=$tripId";
                } else {
                    createFlash("error", "Erreur!");
                    $qs= "?view=trajets.view&id=$tripId";
                }
            break;
			case 'getConversations' :
				$conv["trip"] = getActiveTripConversations($_SESSION['idUser']);
				$conv["user"] = getUserConversations($_SESSION['idUser']);
				$conv["general"] = getGeneralConversation();
				echo json_encode($conv);
				die();
			break;

			case "getChat" :
				if ($tripId=valider("tripId")){
					echo json_encode(getTripMessages($tripId));
					}
				elseif ($userId=valider("userId")){
					echo json_encode(getUserMessages($_SESSION['iduUser'], $userId));
				}
				else{ // chat général
					echo json_encode(getGeneralMessages());
				}
				die();
			break;

			case "getUserName" :
				if ($userId=valider("userId")){
					$rep = getUserName($userId);
					echo $rep["firstname"]." ".$rep["lastname"];
				};
				die();
			break;

			case "getTripName" :
				if ($tripId = valider("tripId")){
					$rep = getTripInfos($tripId);
					echo $rep["date"]." ".$rep["heure"]." ".$rep["departure"];
				}
				die();
			break;

			case "newMessage" :
				if ($tripId = valider("tripId")){
					if (($senderId = valider("senderId"))
						&& ($content = valider("content"))){
							sendTripMessage($senderId, $tripId, $content);
					}
				} elseif ($receiverId=valider("receiverId")){
					if (($senderId = valider("senderId"))
						&& ($content = valider("content"))){
							sendUserMessage($senderId, $receiverId, $content);
					}
				}
				else { // chat général
					if (($senderId = valider("senderId"))
						&& ($content = valider("content"))){
							sendGeneralMessage($senderId, $content);
					}
				}
				die();
			break;

			case "suggestUser" :
				if ($debut = valider("debut")){
					echo json_encode(suggestUser($debut));
				}
				die();

			case 'CreationVoiture' :
				if ($registrationCar = valider("registrationCar")){
					$qs = addCar($registrationCar, valider("idUser", "SESSION"));
				}else{
					$qs = "?view=account.profile";
					createFlash("error", "Problème avec l'immatriculation rentrée !");
				}

			break;

			case 'SuppressionVoiture' :
				if (($idCar = valider("idCar")) && ($idOwner = valider("idUser", "SESSION"))){
					$res = deleteCar($idCar, $idOwner);
					if ($res){
						createFlash("success", "Voiture supprimée !");
					}else{
						createFlash("error", "Problème lors de la suppression de la voiture");
					}
				}else{
					createFlash("error", "Problème lors de la suppression de la voiture");
				}
			break;

			case 'CreationCal' :
				if (($calURL = valider("calURL")) && ($idUser = valider("idUser", "SESSION"))){
					$qs = addCal($calURL, $idUser);
				}else{
					$qs = "?view=account.profile";
					createFlash("error", "Problème avec l'url fourni !");
				}
			break;

			case 'CreationNum' :
				// if ($num = valider("num")){
				// 	$qs = addNum($num, $_SESSION["idUser"]);
				// }else{
				// 	$qs = "?view=profile&msg=". urlencode("Problème avec le numéro entrée");
				// }
				$qs = "?view=accont.profile";
			break;

			case 'ModifyInfos' :
				if (($nom = valider("nom"))
				  	&&($prenom = valider("prenom"))
					&&($mail = valider("mail"))
					&&($adress = valider("adress"))){

						$qs = modifyInfos($nom,$prenom,$mail,$adress,valider("idUser", "SESSION"));

					} else {
						createFlash("error", "Informations invalides !");
						$qs = "?view=account.profile";
					}
			break;

			case 'DeleteNotif' :
				if ($id = valider("id")){
					$res = deleteNotif($id);
					if ($res){
						createFlash("success", "Notification supprimée !");
					}else{
						createFlash("error", "Problème lors de la suppression de la notification");
					}
				}else{
					createFlash("error", "Problème lors de la suppression de la notification");
				}
			break;

			case 'BanUser' :
				if (($idBanUser = valider("idBanUser")) && valider("isAdmin", "SESSION")){
					$res = banUser($idBanUser);
					if ($res){
						createFlash("success", "Utilisateur banni !");
					}else{
						createFlash("error", "Problème lors du bannissement de l'utilisateur");
					}
				}else{
					createFlash("error", "Problème lors du bannissement de l'utilisateur");
				}
			break;

			case 'UnBanUser' :
				if (($idUnBanUser = valider("idUnBanUser")) && valider("isAdmin", "SESSION")){
					$res = unbanUser($idUnBanUser);
					if ($res){
						createFlash("success", "Utilisateur retiré des bans !");
					}else{
						createFlash("error", "Problème lors du bannissement de l'utilisateur");
					}
				}else{
					createFlash("error", "Problème lors du bannissement de l'utilisateur");
				}
			break;

            case "ajax.getAvailableCars":
                header("Content-Type: application/json; charset=UTF-8");
                if ($edited = valider("edit")) $response['cars'] = getAvailableCarsTripBypass(valider("date"), $edited);
                else $response['cars'] = getAvailableCars(valider("date"));
                echo json_encode($response);
                die();

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










