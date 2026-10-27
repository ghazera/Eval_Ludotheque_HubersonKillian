
<?php
session_start();
include('utils/utils.php');

// Variable d'affichage
$message = "";

// ici je verif que je recois bien le formulaire lorsqu'on appuie sur submit 
if(isset($_POST['submit'])){

    // on recup les données
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    // on verif si tout les champs sont bien remplis sinon on renvoie un message || = ou, on met == "" pour dire si c'est vide
    if($email == "" || $password == ""){
        $message = "Email et/ou mot de passe incorrect";
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

            // on recupere le membre et son mail
            $request = $pdo->prepare("SELECT * FROM Membre WHERE email = ?;");

            $request->bindParam(1, $email, PDO::PARAM_STR);

            $request->execute();

            $data = $request->fetchAll(PDO::FETCH_ASSOC);

            // on vérifie qu'il existe bien en db
            if(count($data) == 1){

                if(password_verify($password, $data[0]['mot_de_passe'])){ // comparer le mot de passe saisi au hash enregistré en base de donné

                    // on renouvele la session php
                    session_regenerate_id(true);

                    //on enregistre les données dans la session 
                    $_SESSION['id_membre'] = $data[0]['id_membre'];
                    $_SESSION['prenom'] = $data[0]['prenom'];
                    $_SESSION['nom'] = $data[0]['nom'];
                    $_SESSION['email'] = $data[0]['email'];

                    // simple redirection vers notre index php ( accueil )
                    header("Location:index.php");
                    exit;
                }
                else{
                    $message = "Email et/ou mot de passe incorrect";
                }
            }
            else{
                $message = "Email et/ou mot de passe incorrect";
            }

        }catch(Exception $error){
            die($error->getMessage());
        }
    }
}
?>

<?php include('includes/header.php'); ?>

<main>
    <h2>Connexion</h2>

    <form action="" method="post">

        <label for="email">Email :</label>
        <input type="email" id="email" name="email">

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password">

        <input type="submit" name="submit" value="Se connecter">

    </form>

    <p><?php echo($message); ?></p>

</main>

<?php include('includes/footer.php'); ?>
