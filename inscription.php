
<?php
session_start();
include('utils/utils.php');

$message = "";

// ici je verif que je recois bien le formulaire lorsqu'on appuie sur submit 
if(isset($_POST['submit'])){

    // on recup les données
    $prenom = trim($_POST['prenom'] ?? "");
    $nom = trim($_POST['nom'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirmation = $_POST['confirmation'] ?? "";

    // on verif si tout les champs sont bien remplis sinon on renvoie un message || = ou, on met == "" pour dire si c'est vide
    if($prenom == "" || $nom == "" || $email == "" || $password == "" || $confirmation == ""){
        $message = "Il manque des informations";
    }

    // ici on vérifie le format de lemail, c'est une vérification en plus de celle du frontend, mais pour plus de sécurité on utilise FILTER_VALIDATE_EMAIL qui est une fonction native de PHP
    elseif(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
        $message = "Frmat de l'email incorrect";
    }

    // on vérifie si le mot de passe à bien 12 caracteres ou +, strlen permet de compter le nombre de caractère ici on strlen($password) donc on compte le nombre de caractere que contient le mdp
    elseif(strlen($password) < 12){  
        $message = "Le mot de passe doit contenir au moins 12 caractères";
    }

    // verification si le mot de passe $password correspond à notre variable $confirmation ce qui permet de vérifier que les deux mdp sont identique
    elseif($password !== $confirmation){
        $message = "Les mots de passe ne sont pas les même";
    }

    else{
        try{
            // on se connecte à la bdd ludotheque
            $pdo = new PDO(
                'mysql:host=localhost;dbname=ludotheque;charset=utf8mb4',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // on vérif si l'email existe déjà en db ou non grâce à la request qu'on prepare avec pdo pour la recupérer et ensuite la vérifiere avec un if
            $request = $pdo->prepare("SELECT id_membre FROM Membre WHERE email = ?;");
 
            $request->bindParam(1, $email, PDO::PARAM_STR); // le bindparam permet de remplacer le "?" de notre select par la valeur de bindparam, 1 pour le premier param, $email pour la variable et param 
            // string car c'est une chaine de caractere.

            $request->execute();

            $data = $request->fetchAll(PDO::FETCH_ASSOC);

            if(count($data) > 0){
                $message = "email  déjà utilisé";
            }
            else{
                // on hash le mdp avec une fonction native de php
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $request = $pdo->prepare(
                    "INSERT INTO Membre
                    (prenom, nom, email, mot_de_passe, date_inscription)
                    VALUES (?, ?, ?, ?, NOW());"
                );

                $request->bindParam(1, $prenom, PDO::PARAM_STR);
                $request->bindParam(2, $nom, PDO::PARAM_STR);
                $request->bindParam(3, $email, PDO::PARAM_STR);
                $request->bindParam(4, $password_hash, PDO::PARAM_STR);

                $request->execute();

                $message = "Inscription effectué avec succès";
            }

        }catch(Exception $error){
            die($error->getMessage());
        }
    }
}
?>


<?php include('includes/header.php'); /*  ICi on récupere la partie de code présente dans le dossier include et le fichier header.php pour le réafficher sans avoir à le recoder, c'est une insertion */?> 

<main>
    <h2>Inscription</h2>

    <form action="" method="post">

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom"
        value="<?php echo(htmlspecialchars($_POST['prenom'] ?? "", ENT_QUOTES, 'UTF-8')); ?>">

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom"
        value="<?php echo(htmlspecialchars($_POST['nom'] ?? "", ENT_QUOTES, 'UTF-8')); ?>">

        <label for="email">Email :</label>
        <input type="email" id="email" name="email"
        value="<?php echo(htmlspecialchars($_POST['email'] ?? "", ENT_QUOTES, 'UTF-8')); ?>">

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password">

        <label for="confirmation">Confirmer le mot de passe :</label>
        <input type="password" id="confirmation" name="confirmation">

        <input type="submit" name="submit" value="S'inscrire">

    </form>

    <p><?php echo($message); ?></p>

</main>

<?php include('includes/footer.php'); /* meme chose que pour le header */ ?>
