
<?php
// on startla ssession
session_start();

include('utils/utils.php');

// on vide les des données enregistrées dans la session
$_SESSION = [];

// et on la détruit ( ça la coupe & nous deconnecte )
session_destroy();

// pour éviter de laisser l'utilisateur par exemple sur une page où il n'est pas censé être hors ligne, on le redirect vers la page d'accueil & on coupe le script avec exit
header("Location:index.php");

exit;
