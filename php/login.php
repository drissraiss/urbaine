<?php
session_start();
require('connect.php');
if (!(isset($_POST['email']) and isset($_POST['password']))) {
    header("location:../index.php");
    exit();
}
$email = $_POST['email'];
$password = md5($_POST['password']);

$req_directeurs = "SELECT * FROM directeurs WHERE email='$email' and mote_de_pass='$password'";
$req_administrateurs = "SELECT * FROM administrateurs WHERE email='$email' and mote_de_pass='$password'";
$req_employes = "SELECT * FROM employes WHERE email='$email' and mote_de_pass='$password'";
$req_secretaires = "SELECT * FROM secretaires WHERE email='$email' and mote_de_pass='$password'";

$query_directeurs = mysqli_query(CONNECT, $req_directeurs);
$query_administrateurs = mysqli_query(CONNECT, $req_administrateurs);
$query_employes = mysqli_query(CONNECT, $req_employes);
$query_secretaires = mysqli_query(CONNECT, $req_secretaires);

if (mysqli_num_rows($query_directeurs)) {
    $_SESSION['connect'] = true;
    $_SESSION['compte'] = mysqli_fetch_assoc($query_directeurs);
    $_SESSION['role'] = "directeurs";
    header('location:../interfaces/directeurs.php');
} elseif (mysqli_num_rows($query_administrateurs)) {
    $data = mysqli_fetch_assoc($query_administrateurs);
    if ($data['statut_du_compte'] == 'suspendu') {
        header("location:../index.php?error=suspendu");
        exit();
    }
    $_SESSION['connect'] = true;
    $_SESSION['compte'] = $data;
    $_SESSION['role'] = "administrateurs";
    header('location:../interfaces/administrateurs.php');
} elseif (mysqli_num_rows($query_employes)) {
    $data = mysqli_fetch_assoc($query_employes);
    if ($data['statut_du_compte'] == 'suspendu') {
        header("location:../index.php?error=suspendu");
        exit();
    }
    $_SESSION['connect'] = true;
    $_SESSION['compte'] = $data;
    $_SESSION['role'] = "employes";
    header('location:../interfaces/employes.php');
} elseif (mysqli_num_rows($query_secretaires)) {
    $data = mysqli_fetch_assoc($query_secretaires);
    if ($data['statut_du_compte'] == 'suspendu') {
        header("location:../index.php?error=suspendu");
        exit();
    }
    $_SESSION['connect'] = true;
    $_SESSION['compte'] = $data;
    $_SESSION['role'] = "secretaires";
    header('location:../interfaces/secretaires.php');
} else {
    header("location:../index.php?error=not_found");
}
