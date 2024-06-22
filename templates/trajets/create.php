<form action="controleur.php" method="POST">
    <label for="destination">Où allez vous ?</label> <input type="checkbox" id="destination" name="destination"/> <br>
    <label for="departure">D'où partez vous ?</label> <input type="text" id="departure" name="departure" placeholder="Point de rencontre"><br>
    <label for="date">Quel jour partez vous ?</label> <input type="date" id="date" name="date" min=date.now()><br>
    <label for="time">A quelle heure ?</label> <input type="time" id="time" name="time"><br>
    <label for="passengers">Combien de passagers prenez-vous ?</label> <input type="number" min=1 name="passengers" id="passengers"><br>
    <label for="driver">Je conduis</label> <input type="checkbox" name="driver" id="driverd">
    <input type="hidden" name="action" value="trajets.create">
    <input type="submit" value="Créer" />
</form>