
<?php
session_start();

include('utils/utils.php');

// verification si l'utilisateur est identifié, sinon on le redirige vers la page de connexion pour  empecher un visiteur d'ajouter des jeux
if(!isset($_SESSION['id_membre'])){
    header("Location:connexion.php");
    exit;
}

$message = "";
$jeux = "";

$prenom = "";
$nom = "";
$email = "";
$date_inscription = "";

try{
    // on se connecte à la bdd ludotheque
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ludotheque;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Récupérer l'id du membre connecté
    $id_membre = (int)$_SESSION['id_membre'];

    // récupération des informations du membre connecté
    $request = $pdo->prepare(
        "SELECT prenom, nom, email, date_inscription
        FROM Membre
        WHERE id_membre = ?;"
    );

    $request->bindParam(1, $id_membre, PDO::PARAM_INT);

    $request->execute();

    $data = $request->fetchAll(PDO::FETCH_ASSOC);

    // vérification si le membre existe
    if(count($data) == 1){

        //récupération & sécurisation des données récupérées
        $prenom = htmlspecialchars($data[0]['prenom'], ENT_QUOTES, 'UTF-8');
        $nom = htmlspecialchars($data[0]['nom'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($data[0]['email'], ENT_QUOTES, 'UTF-8');
        $date_inscription = htmlspecialchars($data[0]['date_inscription'], ENT_QUOTES, 'UTF-8');

        // récupération de jeux créer par le membre
        $request = $pdo->prepare(
            "SELECT j.titre, c.nom_categorie
            FROM jeu j
            INNER JOIN categorie c
            ON j.id_categorie = c.id_categorie
            WHERE j.id_membre = ?
            ORDER BY j.titre ASC;"
        );

        $request->bindParam(1, $id_membre, PDO::PARAM_INT);

        $request->execute();

        $dataJeux = $request->fetchAll(PDO::FETCH_ASSOC);

        // on parcout les jeux
        if(count($dataJeux) > 0){
            foreach($dataJeux as $jeu){
                $titre = htmlspecialchars($jeu['titre'], ENT_QUOTES, 'UTF-8');
                $categorie = htmlspecialchars($jeu['nom_categorie'], ENT_QUOTES, 'UTF-8');

                $jeux = $jeux."<li>{$titre} : {$categorie}</li>";
            }
        }
        else{
            $jeux = "<li>Vous n'avez pas encore créer de jeu.</li>";
        }
    }
    else{
        $message = "Utilisateur introuvable";
    }

}catch(Exception $error){
    die($error->getMessage());
}

?>

<?php include('includes/header.php'); ?>

<main>
    <h2>Mon compte</h2>

    <p><?php echo($message); ?></p>

    <?php if($message == ""){ ?>

        <h3>Mes informations</h3>

        <p>Prénom : <?php echo($prenom); ?></p>
        <p>Nom : <?php echo($nom); ?></p>
        <p>Email : <?php echo($email); ?></p>
        <p>Date d'inscription : <?php echo($date_inscription); ?></p>

        <h3>Mes jeux ajoutés</h3>

        <ul>
            <?php echo($jeux); ?>
        </ul>

    <?php } ?>

</main>

<?php include('includes/footer.php'); ?>
