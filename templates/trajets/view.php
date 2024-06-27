<?php
include_once ('libs/modele.php');
//C'est la propriété php_self qui nous l'indique :
// Quand on vient de index :
// [PHP_SELF] => /chatISIG/index.php
// Quand on vient directement par le répertoire templates
// [PHP_SELF] => /chatISIG/templates/accueil.php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
// Pas de soucis de bufferisation, puisque c'est dans le cas où on appelle directement la page sans son contexte
if (basename($_SERVER["PHP_SELF"]) != "index.php" || !($id = valider("id")))
{
    header("Location:../index.php?view=accueil");
    die("");
};

$tripDetail = getTrip($id);
$passengers = getPassengers($id);
?>

<div id="corps">
    <a href="?view=accueil">retour</a>
    <h1>Détail du trajet</h1>
    <?php if($tripDetail['creator_id'] == valider("idUser", "SESSION")): ?>
        <a href="?view=trajets.edit&id=<?= $id ?>">Éditer</a> <a href="controleur.php?action=trajets.remove&id=<?= $id ?>">Supprimer</a><br>
    <?php endif; ?>
    Point de rencontre:  <?= $tripDetail['departure']; ?> <br>
    Arrivée : <?= $tripDetail['arrival']; ?> <br>
    Jour du trajet : <?= $tripDetail['date']; ?> <br>
    Passagers : <?php if ($tripDetail['creator_id'] != valider("idUser", "SESSION")): ?>
    <?php if (isPassenger($tripDetail['id'])): ?> <a href='controleur.php?action=trajets.remove-passenger&trip_id=<?= $id ?>'>quitter</a>
        <?php else : ?>
            <a href='controleur.php?action=trajets.join&id=<?= $id ?>'>rejoindre</a>
        <?php endif; endif; ?>
    <ul>
        <?php foreach ($passengers as $passenger): ?>
            <li><?= ucfirst($passenger['firstname']) ?> <?= strtoupper($passenger['lastname']) ?> <?php if(valider("idUser", "SESSION") != $passenger['id'] && $tripDetail['creator_id'] == valider("idUser", "SESSION")): ?><a href="controleur.php?action=trajets.remove-passenger&trip_id=<?= $id ?>&user_id=<?= $passenger['id'] ?>">-</a> <?php endif; ?></li>
        <?php endforeach; ?>
    </ul>

</div>
