<?php
session_start();
if (
    isset($_SESSION['connect']) and
    isset($_SESSION['compte']) and
    isset($_SESSION['role'])
) {
header("location:./interfaces/". $_SESSION['role'] .".php");
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    <link rel="stylesheet" href="./styles/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>CHATFAMS</title>
</head>

<body>
    <div class="center">
        <h1>Connectez vous</h1>
        <form method="post" action="./php/login.php">
            <?php
            if (isset($_GET['error'])) {
                if ($_GET['error'] == "not_found") {

            ?>
                    <p style="font-size:larger;font-weight: 500;color: red; margin-top: 15px; background-color: #ffe6e6;border: 1px solid; padding: 5px;">
                        <span class="text-decoration-underline fw-bold">L'adresse e-mail</span>
                        ou
                        <span class="text-decoration-underline fw-bold">le mot de passe </span>
                        que vous avez entré est incorrect
                    </p>
                <?php
                } elseif ($_GET['error'] == "suspendu") {
                ?>
                    <p style="font-size:larger;font-weight: 500;color: #724900; margin-top: 15px; background-color: #ffe79e;border: 1px solid; padding: 5px;">
                        Ce compte a été <span class="text-decoration-underline fw-bold">suspendu</span>, veuillez contacter l'administrateur pour supprimer la suspension
                    </p>
            <?php
                }
            }
            ?>
            <div class="txt_field">
                <input type="" name="email" autofocus required>
                <span></span>
                <label>Email</label>
            </div>
            <div class="txt_field">
                <input type="password" name="password" required value="1234">
                <label>Mot de passe</label>
            </div>
            <input type="submit" value="Connecter">
        </form>
    </div>
</body>

</html>