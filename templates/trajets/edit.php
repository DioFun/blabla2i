<?php
include_once 'libs/modele.php';
$trip = getTrip(valider("id"));
?>

<form action="controleur.php?id=<?= $trip['id'] ?>" method="POST">
    <label for="destination">Où allez vous ?</label> <input type="checkbox" id="destination" name="destination" <?= $trip['arrival'] ? "checked" : "" ?>/> <br>
    <label for="departure">D'où partez vous ?</label> <input type="text" id="departure" name="departure" placeholder="Point de rencontre" value="<?= $trip['departure'] ?>"><br>
    <label for="date">Quel jour partez vous ?</label> <input type="date" id="date" name="date" min=date.now() value="<?= $trip['date'] ?>" ><br>
    <label for="time">A quelle heure ?</label> <input type="time" id="time" name="time" <?= $trip['hour'] ?>><br>
    <label for="passengers">Combien de passagers prenez-vous ?</label> <input type="number" min=1 name="passengers" id="passengers" value="<?= $trip['passenger'] ?>"><br>
    <label for="driver">Je conduis</label> <input type="checkbox" name="driver" id="driverd" <?= $trip['creator_id'] == $trip['driver_id'] ? "checked" : "" ?>>
    <input type="hidden" name="action" value="trajets.edit">
    <input type="submit" value="Éditer" />
</form>