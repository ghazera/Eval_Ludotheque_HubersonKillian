
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Dé Fêlé - Ludothèque</title>
    <link rel="stylesheet" href="src/css/style.css">
</head>

<body>
    <header>
        <h1>Le Dé Fêlé</h1>

        <nav>
            <?php
            // verification si une session est en cours (on regarde si quelque chose est stocké dans la var id_membre si c'est le cas , notre session n'est pas vide et donc l'utilisateur est identifié)
            if(isset($_SESSION['id_membre'])){

                // on affiche son prénom
                echo("<p>Bonjour ".htmlspecialchars($_SESSION['prenom'], ENT_QUOTES, 'UTF-8')."</p>");

                // menu utilisateur authentifié
                echo('<a href="index.php">Accueil</a>');
                echo('<a href="ajout-jeu.php">Ajouter un jeu</a>');
                echo('<a href="deconnexion.php">Se déconnecter</a>');

            }
            else{
                // menu visiteur
                echo('<a href="index.php">Accueil</a>');
                echo('<a href="inscription.php">Inscription</a>');
                echo('<a href="connexion.php">Connexion</a>');
            }
            ?>
        </nav>
    </header>
