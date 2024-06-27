<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php");
	die("");
}

// On envoie l'entête Content-type correcte avec le bon charset
header('Content-Type: text/html;charset=utf-8');

// Pose qq soucis avec certains serveurs...
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- **** H E A D **** -->
<?php if (valider("connecte","SESSION")): ?>



<head>	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
	<title>TinyMVC</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<!-- **** F I N **** H E A D **** -->

<!-- **** B O D Y **** -->
<body>

<div id="banniere">

<div id="logo">
<img src="ressources/ec-lille.png" />
</div>

<a href="index.php?view=accueil">Accueil</a>
<a href="index.php?view=accueil">Rajouter trajet</a>
<a href="index.php?view=accueil">Rechercher</a>
<a href="index.php?view=accueil">Profil</a>
<a href="index.php?view=conversation">Conversations</a>




<h1 id="stitre"> Centro'd'voitures </h1>

</div>

<?php endif; ?>

<?php var_dump($_SESSION); ?>

<?php if (flashExists()): ?>
    <?php foreach(getAllFlash() as $type => $flashMessages): ?>
        <div class="flash <?= $type ?>">
            <ul>
                <?php foreach ($flashMessages as $flash): ?>
                    <li><?= $flash ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
<?php endif; unflash(); ?>
