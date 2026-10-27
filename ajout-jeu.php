
<?php
session_start();

include('utils/utils.php');

// verification si l'utilisateur est identifié, sinon on le redirige vers la page de connexion pour  empecher un visiteur d'ajouter des jeux
if(!isset($_SESSION['id_membre'])){
    header("Location:connexion.php");
    exit;
}

$message = "";
$options = "";

try{
    // on se connecte à la bdd ludotheque
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ludotheque;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // récupération des ctaégories
    $request = $pdo->prepare(
        "SELECT id_categorie, nom_categorie
        FROM Categorie
        ORDER BY nom_categorie ASC;"
    );

    $request->execute();

    $categories = $request->fetchAll(PDO::FETCH_ASSOC);

    foreach($categories as $categorie){

        $id = (int)$categorie['id_categorie'];

        $nom = htmlspecialchars(
            $categorie['nom_categorie'],
            ENT_QUOTES,
            'UTF-8'
        );

        $options = $options."<option value='{$id}'>{$nom}</option>";
    }

    // ici je verif que je recois bien le formulaire lorsqu'on appuie sur submit 
    if(isset($_POST['submit'])){

        // récupération des données & les normaliser on utilise trim pour retirer les espaces & autres caracteres en début & fin de chaine
        $titre = trim($_POST['titre'] ?? "");
        $joueurs_min = trim($_POST['joueurs_min'] ?? "");
        $joueurs_max = trim($_POST['joueurs_max'] ?? "");
        $duree = trim($_POST['duree'] ?? "");
        $id_categorie = trim($_POST['id_categorie'] ?? "");

        // on verif si tout les champs sont bien remplis sinon on renvoie un message
        if(
            $titre == "" ||
            $joueurs_min === "" ||
            $joueurs_max === "" ||
            $duree === "" ||
            $id_categorie === ""
        ){
            $message = "Veuillez remplir tous les champs";
        }

        // check si les nombres sont bien des nombres entier avec u validate_int car les int ne prennenet pas de ,
        elseif(
            filter_var($joueurs_min, FILTER_VALIDATE_INT) === false ||
            filter_var($joueurs_max, FILTER_VALIDATE_INT) === false ||
            filter_var($duree, FILTER_VALIDATE_INT) === false ||
            filter_var($id_categorie, FILTER_VALIDATE_INT) === false
        ){
            $message = "Veuillez saisir UNIQUEMENT des nombres entiers";
        }

        // on regarde si le nombre de joueur minimum est bien 1
        elseif($joueurs_min < 1){
            $message = "Le jeu doit avoir au moins 1 joueur";
        }

        // on verif le nombre maximum de joueurs
        elseif($joueurs_max < $joueurs_min){
            $message = "Le maximum doit être supérieur ou égal au minimum";
        }

        // vérification de la durée de la partie
        elseif($duree <= 0){
            $message = "La durée doit être supérieure à 0";
        }

        // on vérifie que l'identifiant est positif
        elseif($id_categorie <= 0){
            $message = "La catégorie est incorrecte";
        }

        else{
            // vérification si le titre existe ou non 
            $request = $pdo->prepare(
                "SELECT id_jeu FROM Jeu WHERE titre = ?;"
            );

            $request->bindParam(1, $titre, PDO::PARAM_STR);

            $request->execute();

            $data = $request->fetchAll(PDO::FETCH_ASSOC);

            if(count($data) > 0){
                $message = "Un jeu existe déjà avec ce même titre";
            }
            else{
                // on convertis les valeurs en nombre entier
                $joueurs_min = (int)$joueurs_min;
                $joueurs_max = (int)$joueurs_max;
                $duree = (int)$duree;
                $id_categorie = (int)$id_categorie;

                // on recupere l'id du membre directement inscrit dans les données de sa session en cours 
                $id_membre = (int)$_SESSION['id_membre'];
                $request = $pdo->prepare(
                    "INSERT INTO Jeu
                    (titre, joueurs_min, joueurs_max, duree, id_membre, id_categorie)
                    VALUES (?, ?, ?, ?, ?, ?);"
                );

                // on lie les variables aux bons emplacement de nos values ?,? 
                $request->bindParam(1, $titre, PDO::PARAM_STR);
                $request->bindParam(2, $joueurs_min, PDO::PARAM_INT);
                $request->bindParam(3, $joueurs_max, PDO::PARAM_INT);
                $request->bindParam(4, $duree, PDO::PARAM_INT);
                $request->bindParam(5, $id_membre, PDO::PARAM_INT);
                $request->bindParam(6, $id_categorie, PDO::PARAM_INT);

                $request->execute();

                $message = "Le jeu a été ajouté à la bd avec succes";
            }
        }
    }

}catch(Exception $error){
    die($error->getMessage());
}

?>

<?php include('includes/header.php'); ?>

<main>
    <h2>Ajouter un jeu</h2>

    <form action="" method="post">

        <label for="titre">Titre du jeu :</label>
        <input type="text" id="titre" name="titre">

        <label for="joueurs_min">Nombre minimum de joueurs :</label>
        <input type="number" id="joueurs_min" name="joueurs_min">

        <label for="joueurs_max">Nombre maximum de joueurs :</label>
        <input type="number" id="joueurs_max" name="joueurs_max">

        <label for="duree">Durée de la partie (minutes) :</label>
        <input type="number" id="duree" name="duree">

        <label for="id_categorie">Catégorie :</label>
        <select id="id_categorie" name="id_categorie">
            <option value="">Choisir une catégorie</option>
            <?php echo($options); ?>
        </select>

        <input type="submit" name="submit" value="Ajouter le jeu">

    </form>

    <p>
        <?php echo(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')); ?>
    </p>

</main>

<?php include('includes/footer.php'); ?>
