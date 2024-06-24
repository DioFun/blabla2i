<?php
include_once ('libs/modele.php');
//C'est la propriété php_self qui nous l'indique : 
// Quand on vient de index : 
// [PHP_SELF] => /chatISIG/index.php 
// Quand on vient directement par le répertoire templates
// [PHP_SELF] => /chatISIG/templates/accueil.php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
// Pas de soucis de bufferisation, puisque c'est dans le cas où on appelle directement la page sans son contexte
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=accueil");
	die("");
}

?>

<div id="corps">

<h1>Accueil</h1>
    <a href="?view=trajets.create"><button class="primary">Créer un trajet</button></a>
    <h2>Mes trajets</h2>
    <div class="my-trips">
        <?php if ($trips = getUserTrips(valider("idUser", "SESSION"))): ?>
        <?php foreach ($trips as $trip): ?>
          <div class="trip"><?= $trip['departure'] ?>, <?= $trip['arrival'] ?>, <?= $trip['email'] ?>, <a href="?view=trajets.view&id=<?= $trip['id'] ?>">view</a></div>
        <?php endforeach; ?>
        <?php else: ?>
            Vous n'avez aucun trajet de prévu !
        <?php endif; ?>
    </div>

    <h2>Trajets disponibles</h2>
    <div class="available-trips">
        <?php if ($trips = getAvailableTrips(valider("idUser", "SESSION"))): ?>

        <?php else: ?>
            Il n'y a pas de tajets disponibles !
        <?php endif; ?>
    </div>

</div>
