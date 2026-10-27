
<?php
session_start();

include('utils/utils.php');

$jeux = "";

try{
    // on se connecte à la bdd ludotheque
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ludotheque;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // récupérer les jeux, leur catégorie et le membre
    $request = $pdo->prepare(
        "SELECT
            j.titre,
            j.joueurs_min,
            j.joueurs_max,
            j.duree,
            c.nom_categorie,
            m.prenom
        FROM jeu j
        INNER JOIN categorie c
        ON j.id_categorie = c.id_categorie
        INNER JOIN membre m
        ON j.id_membre = m.id_membre
        ORDER BY j.titre ASC;"
    );

    $request->execute();

    // on recup la réponse
    $data = $request->fetchAll(PDO::FETCH_ASSOC);

    //on vérifie si les jeux existent
    if(count($data) > 0){
        foreach($data as $jeu){

            // on réutilise les fonctions de utils.php 
            $joueurs = formatJoueurs(
                $jeu['joueurs_min'],
                $jeu['joueurs_max']
            );

            $categorieDuree = categorieDuree($jeu['duree']);

            // sécuriser les données affichées pour éviter les failles XSS ( injection ) , htmlspecialchars() nous permet d'afficher le texte en texte brut et donc éviter des injection de ce style. 
            $titre = htmlspecialchars($jeu['titre'], ENT_QUOTES, 'UTF-8');
            $categorie = htmlspecialchars($jeu['nom_categorie'], ENT_QUOTES, 'UTF-8');
            $prenom = htmlspecialchars($jeu['prenom'], ENT_QUOTES, 'UTF-8');

            // recuperation de la durée du jeu
            $duree = (int)$jeu['duree'];

            //  on construcit avec la concaténation 
            $jeux = $jeux."
                <li>
                    <h3>{$titre}</h3>
                    <p>Catégorie : {$categorie}</p>
                    <p>Nombre de joueurs : {$joueurs}</p>
                    <p>Durée : {$duree} minutes - {$categorieDuree}</p>
                    <p>Ajouté par : {$prenom}</p>
                </li>
            ";
        }
    }
    else{
        $jeux = "<li>aucun jeu dans le catalogue</li>";
    }

}catch(Exception $error){
    die($error->getMessage());
}

?>

<?php include('includes/header.php'); ?>

<main>
    <h2>Accueil</h2>
    <p>Bienvenue sur le catalogue de la ludothèque associative Le Dé Fêlé.</p>
    
    <h2>Catalogue des jeux</h2>
    <ul>
        <?php echo($jeux); ?>
    </ul>
</main>

<?php include('includes/footer.php'); ?>
