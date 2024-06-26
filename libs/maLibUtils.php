
<?php

// V1.0 du 18 mai 2018

/**
 * @file maLibUtils.php
 * Ce fichier définit des fonctions d'accès ou d'affichage pour les tableaux superglobaux
 */

/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramètre dans un des tableaux GET, POST, COOKIES, SESSION
 * Renvoie false si le paramètre est vide ou absent
 * @note l'utilisation de empty est critique : 0 est empty !!
 * Lorsque l'on teste, il faut tester avec un ===
 * @param string $nom
 * @param string $type
 * @return string|boolean
 */
function valider($nom,$type="REQUEST")
{	
	switch($type)
	{
		case 'REQUEST': 
		if(isset($_REQUEST[$nom]) && !($_REQUEST[$nom] == "")) 	
			return proteger($_REQUEST[$nom]); 	
		break;
		case 'GET': 	
		if(isset($_GET[$nom]) && !($_GET[$nom] == "")) 			
			return proteger($_GET[$nom]); 
		break;
		case 'POST': 	
		if(isset($_POST[$nom]) && !($_POST[$nom] == "")) 	
			return proteger($_POST[$nom]); 		
		break;
		case 'COOKIE': 	
		if(isset($_COOKIE[$nom]) && !($_COOKIE[$nom] == "")) 	
			return proteger($_COOKIE[$nom]);	
		break;
		case 'SESSION': 
		if(isset($_SESSION[$nom]) && !($_SESSION[$nom] == "")) 	
			return $_SESSION[$nom]; 		
		break;
		case 'SERVER': 
		if(isset($_SERVER[$nom]) && !($_SERVER[$nom] == "")) 	
			return $_SERVER[$nom]; 		
		break;
	}
	return false; // Si pb pour récupérer la valeur 
}


/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramètre dans un des tableaux GET, POST, COOKIE, SESSION
 * Prend un argument définissant la valeur renvoyée en cas d'absence de l'argument dans le tableau considéré

 * @param string $nom
 * @param string $defaut
 * @param string $type
 * @return string
*/
function getValue($nom,$defaut=false,$type="REQUEST")
{
	// NB : cette commande affecte la variable resultat une ou deux fois
	if (($resultat = valider($nom,$type)) === false)
		$resultat = $defaut;

	return $resultat;
}

/**
*
* Evite les injections SQL en protegeant les apostrophes par des '\'
* Attention : SQL server utilise des doubles apostrophes au lieu de \'
* ATTENTION : LA PROTECTION N'EST EFFECTIVE QUE SI ON ENCADRE TOUS LES ARGUMENTS PAR DES APOSTROPHES
* Y COMPRIS LES ARGUMENTS ENTIERS !!
* @param string $str
*/
function proteger($str)
{
	// attention au cas des select multiples !
	// On pourrait passer le tableau par référence et éviter la création d'un tableau auxiliaire
	if (is_array($str))
	{
		$nextTab = array();
		foreach($str as $cle => $val)
		{
			$nextTab[$cle] = addslashes($val);
		}
		return $nextTab;
	}
	else 	
		return addslashes ($str);
	//return str_replace("'","''",$str); 	//utile pour les serveurs de bdd Crosoft
}


function flashExists()
{
	return !empty($_SESSION['flash']);
}

function createFlash($type, $msg)
{
	if (!isset($_SESSION)) session_start();
	if (!isset($_SESSION['flash'])) $_SESSION['flash'] = array();
	if (!isset($_SESSION['flash'][$type])) $_SESSION['flash'][$type] = array();
	array_push($_SESSION['flash'][$type], $msg);
	return true;
}

function unflash()
{
	$_SESSION["flash"] = array();
}

function getAllFlash()
{
	if (!isset($_SESSION["flash"])) return array();
	return $_SESSION["flash"];
}

function tprint($tab)
{
	echo "<pre>\n";
	print_r($tab);
	echo "</pre>\n";	
}


function rediriger($url,$qs="")
{
	// if ($qs != "")	 $qs = urlencode($qs);	
	// Il faut respecter l'encodage des caractères dans les chaînes de requêtes
	// NB : Pose des problèmes en cas de valeurs multiples
	// TODO: Passer un tabAsso en paramètres

	if ($qs != "") $qs = "?$qs";
 
	header("Location:$url$qs"); // envoi par la méthode GET
	die(""); // interrompt l'interprétation du code 

	// TODO: on pourrait passer en parametre le message servant au die...
}

// TODO: intégrer les redirections vers la page index dans une fonction :

/*
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php");
	die("");
}
*/

/**
 * Fonction pour créer une notif dans la bdd et sur la page (on récupère l'id sur le moment et il nous sert d'id de notif en html aussi)
 * @param string $msg Le message de la notification
 * @return void
 */
function showNotif($msg){
	$id = createNotif(valider("idUser", "SESSION"));
	?>
	<div id="notif<?= $id ?>" class="notif">
		<p id-notif="<?=$msg?>"><?= $msg ?></p>
		<button onclick="removeNotif(<?=$id?>)">X</button>
	</div>
	<?php
}

/**
 * Ajoute une nouvelle voiture dans la base de données pour un utilisateur donné
 * @param string $registration La plaque d'immatriculation de la voiture
 * @param int $idUser L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur
 */
function addCar($registration, $idUser) {

	$SQL = "SELECT 1 FROM vehicles WHERE registration = '$registration');";
	if (!empty(parcoursRs(SQLSelect($SQL)))) {

		$qs = "?view=create&msg=". urlencode("Adresse mail déjà utilisée");

	}else{

	$SQL = "INSERT INTO vehicles (registration, owner_id) VALUES ('$registration', '$idUser')";
	SQLInsert($SQL);
	$qs = "?view=login&msg=". urlencode("Utilisateur crée avec succès !");
	}

	return $qs;
}

/**
 * Modifie les informations d'un utilisateur -> présent dans la page profile.php
 * @param string $nom Le nom de l'utilisateur
 * @param string $prenom Le prénom de l'utilisateur
 * @param string $mail L'adresse mail de l'utilisateur
 * @param string $adress L'adresse de l'utilisateur
 * @param int $idUser L'identifiant de l'utilisateur
 * @return string Le message à afficher à l'utilisateur
 */
function modifyInfos($nom, $prenom, $mail, $adress, $idUser) {
	$SQL = "UPDATE users SET lastname = '$nom', firstname = '$prenom', email = '$mail', adress = '$adress' WHERE id = '$idUser'";
	$modif = SQLUpdate($SQL);
	log($modif === 0);
	if ($modif === 0) {
		return "?view=profile&msg=". urlencode("Informations modifiées avec succès !");
	}else{
		return "?view=profile&msg=". urlencode("Erreur lors de la modification des informations.");
	}
}

/**
 * Fonction pour montrer la liste des véhicules entrée en paramètre
 * à utiliser avec getUserCar ou getTripCar, par exemple.
 * @param array $voitures La liste des voitures à afficher
 * @return void
 */
function showVehicleList($voitures){
	echo "<div id='listeVoitures' class='liste'>";
	echo "<h1>Mes voitures</h1>";
	if (count($voitures) == 0){
		echo "<p>Vous n'avez pas encore enregistré de voiture</p>";
	}else{
		foreach($voitures as $voiture){
			echo "<div class='voiture'>";
			echo "<img src='../ressources/ec-lille.png' alt='Logo Voiture' />";
			echo "<p>".$voiture["registration"]."</p>";
			echo "</div>";
		}
	}
	echo "</div>";
	return;
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
	function removeNotif(id){
		$.ajax({
			url: "controleur.php",
			type: "GET",
			data: {action: "DeleteNotif", id: id},
			success: function(){
				$("#notif"+id).hide();
			}
		});

		
	}
</script>