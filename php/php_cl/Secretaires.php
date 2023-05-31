<?php
@session_start();
require_once("../connect.php");

class CompteSecretaires
{
    public static function verification_de_la_requete()
    {
        $req = "select * from secretaires where id='" . $_SESSION['compte']['id'] . "'";
        $compte = mysqli_fetch_assoc(mysqli_query(CONNECT, $req));
        if (
            ($compte['email'] === $_SESSION["compte"]['email'])
            and ($compte['mote_de_pass'] === $_SESSION["compte"]['mote_de_pass'])
        ) {
            return true;
        }
        return false;
    }
    public static function email_exists($email)
    {
        $req = "SELECT email_exists('" . $email . "')";
        $query = mysqli_query(CONNECT, $req);
        return mysqli_fetch_column($query);
    }
    // profile
    public static function mise_a_jour_info()
    {
        $req = "UPDATE `administrateurs` SET  `prenom` = '" . ucfirst(strtolower($_POST['prenom'])) . "', `nom` = '" . ucfirst(strtolower($_POST['nom'])) . "', `telephone` = '" . $_POST['telephone'] . "', `date_de_naissance` = '" . $_POST['date_de_naissance'] . "', `genre` = '" . $_POST['genre'] . "', `ville` = '" . ucfirst(strtolower($_POST['ville'])) . "', `adresse` = '" . $_POST['adresse'] . "' WHERE `administrateurs`.`id` = '" . $_SESSION['compte']['id'] . "' AND `administrateurs`.`mote_de_pass` = '" . $_SESSION['compte']['mote_de_pass'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query == 1 ? 1 : 0;
    }
    public static function mise_a_jour_image_de_profil()
    {
        $new_profil_img = $_FILES['new-profil-img'];
        define("MB", 1024e+9);
        if (substr($new_profil_img['type'], 0, 5) != "image") {
            return "error_not_img";
        }
        if ($new_profil_img['size'] > 5 * MB) {
            return "error_not_size";
        }
        // name profile
        $r = explode('/', $new_profil_img['type']);
        $e = end($r);
        $name_image_de_profil = $_SESSION['compte']['id'] . "_admin." . $e;
        // -----
        move_uploaded_file($new_profil_img['tmp_name'], "../../data/profiles/" . $name_image_de_profil);
        $req = "UPDATE `administrateurs` SET `image_de_profil` = '" . $name_image_de_profil . "'  WHERE `administrateurs`.`id` = '" . $_SESSION['compte']['id'] . "' AND `administrateurs`.`mote_de_pass` = '" . $_SESSION['compte']['mote_de_pass'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function mise_a_jour_email()
    {

        $req1 = "SELECT * FROM administrateurs where id='" . $_SESSION['compte']['id'] . "' and email='" . $_SESSION['compte']['email'] . "' and mote_de_pass='" . md5($_POST['password']) . "'";
        //return $req1;
        $query1 = mysqli_query(CONNECT, $req1);
        if (mysqli_num_rows($query1) == 0) {
            return "error_password";
        }

        if (self::email_exists($_POST['email'])) {
            return "error_email_exists";
        }
        $req3 = "update administrateurs set email='" . $_POST['email'] . "' WHERE id='" . $_SESSION['compte']['id'] . "' and email='" . $_SESSION['compte']['email'] . "' and mote_de_pass='" . md5($_POST['password']) . "'";
        mysqli_query(CONNECT, $req3);
        if (mysqli_affected_rows(CONNECT)) {
            return "success";
        }
    }
    public static function mise_a_jour_mote_de_pass()
    {
        if ($_POST['newPassword'] !== $_POST['confirmPassword']) {
            return "error_confirm_password";
        }
        $req1 = "select mote_de_pass from administrateurs where id='" . $_SESSION['compte']['id'] . "' and email='" . $_SESSION['compte']['email'] . "'";
        $query1 = mysqli_query(CONNECT, $req1);
        if (mysqli_fetch_column($query1) != md5($_POST['currentPassword'])) {
            return "error_current_password";
        }
        $req2 = "UPDATE administrateurs SET mote_de_pass='" . md5($_POST['confirmPassword']) . "' where id='" . $_SESSION['compte']['id'] . "' and email='" . $_SESSION['compte']['email'] . "'";
        mysqli_query(CONNECT, $req2);
        if (mysqli_affected_rows(CONNECT)) {
            return "success";
        }
    }
    // employes
    public static function afficher_les_employes()
    {
        $req = "SELECT *,service.nom as nom_service,administrateurs.nom as nom_admin, administrateurs.id as id_admin  FROM `administrateurs` inner join service on service.id= administrateurs.id_service";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_employe()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT *,administrateurs.nom as nom_admin, service.nom as nom_service ,  administrateurs.id as id_admin  FROM `administrateurs` inner join service on administrateurs.id_service=service.id WHERE administrateurs.id='" . $_POST['id_employe'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }


    public static function change_statu_compte_emp()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "UPDATE `administrateurs` SET `statut_du_compte` = '" . $_POST['new_statu'] . "' WHERE `administrateurs`.`id` ='" . $_POST['id_employe'] . "'";

        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function ajouter_emp()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        if (self::email_exists($_POST['email'])) {

            return "error_email_exists";
        }
        $req = "INSERT INTO `administrateurs` (`prenom`, `nom`, `id_service`, `email`, `mote_de_pass`) VALUES ('" . $_POST['prenom'] . "','" . $_POST['nom'] . "','" . $_POST['id_service'] . "','" . $_POST['email'] . "','" . md5($_POST['password']) . "');";

        $query = mysqli_query(CONNECT, $req);
        return $query;
    }

    public static function afficher_plaintes_emps()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "select  dir_vu, plaintes_directeur.id, expediteur, titre, plainte,  date, CONCAT_WS(' ',administrateurs.prenom, administrateurs.nom) as envoyeur from plaintes_directeur INNER JOIN administrateurs on plaintes_directeur.expediteur = administrateurs.id  ORDER BY date DESC";

        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_plainte()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "select plaintes_directeur.id, expediteur, titre, plainte, vu, date,  CONCAT_WS(' ',administrateurs.prenom, administrateurs.nom) as envoyeur from plaintes_directeur INNER JOIN administrateurs on plaintes_directeur.expediteur = administrateurs.id where  plaintes_directeur.id='" . $_POST['id_plainte'] . "'";

        $query = mysqli_query(CONNECT, $req);
        mysqli_query(CONNECT, "update plaintes_directeur set vu='1' where plaintes_directeur.id='" . $_POST['id_plainte'] . "'");
        return $query;
    }
    public static function envoyer_plainte_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "INSERT INTO `plaintes_directeur` (`expediteur`, `titre`, `plainte`) VALUES ('" . $_SESSION['compte']['id'] . "', '" . $_POST['titre'] . "', '" . $_POST['plainte'] . "')";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_plaintes_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "select * from plaintes_directeur where expediteur='" . $_SESSION['compte']['id'] . "'  ORDER BY date DESC";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_ma_plainte()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT * FROM `plaintes_directeur` where expediteur='" . $_SESSION['compte']['id'] . "' and id='" . $_POST['id_plainte'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_demandes_emps()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        //$req = "select * from demandes_directeur where expediteur='" . $_SESSION['compte']['id'] . "'  ORDER BY date DESC";
        $req = "select dir_vu, demandes_directeur.id, expediteur, titre, demande, reponse, date, CONCAT_WS(' ',administrateurs.prenom, administrateurs.nom) as envoyeur from demandes_directeur INNER JOIN administrateurs on demandes_directeur.expediteur = administrateurs.id  ORDER BY date DESC";

        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_demande()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "select demandes_directeur.id, expediteur, titre, demande, reponse, date,  CONCAT_WS(' ',administrateurs.prenom, administrateurs.nom) as envoyeur from demandes_directeur INNER JOIN administrateurs on demandes_directeur.expediteur = administrateurs.id where  demandes_directeur.id='" . $_POST['id_demande'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function envoyer_demande_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "INSERT INTO `demandes_directeur` (`expediteur`, `titre`, `demande`) VALUES ('" . $_SESSION['compte']['id'] . "', '" . $_POST['titre'] . "', '" . $_POST['demande'] . "')";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_ma_demande()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        //$req = "select * from demandes_directeur where expediteur='" . $_SESSION['compte']['id'] . "'  ORDER BY date DESC";
        $req = "SELECT * FROM `demandes_directeur` where expediteur='" . $_SESSION['compte']['id'] . "' and id='" . $_POST['id_demande'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_demandes_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "select * from demandes_directeur where expediteur='" . $_SESSION['compte']['id'] . "'  ORDER BY date DESC";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function mise_a_jour_reponse_demande()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "UPDATE `demandes_directeur` SET `dir_vu` = " . $_POST['reponse'] . " WHERE `demandes_directeur`.`id` = '" . $_POST['id_demande'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function mise_a_jour_reponse_plainte()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "UPDATE `plaintes_directeur` SET `dir_vu` = " . $_POST['reponse'] . " WHERE `plaintes_directeur`.`id` = '" . $_POST['id_demande'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function get_services()
    {
        $req = "SELECT * FROM `service` ";
        $query = mysqli_query(CONNECT, $req);
        echo "<select name='id_service' class='form-select '>";
        while ($data = mysqli_fetch_assoc($query)) {
            echo "<option value='" . $data['id'] . "'>" . $data['nom'] . "</option>";
        }
        echo "</select>";
    }
    public static function envoyer_notification_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "INSERT INTO `notifications_directeur` (`id_service`, `titre`, `notification`) VALUES ('" . $_POST['id_service'] . "', '" . $_POST['titre'] . "', '" . $_POST['notification'] . "')";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_notifications_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT * FROM `notifications_directeur`";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    /*
    public static function envoyer_notification_admin_dr()
    {
    if (!self::verification_de_la_requete()) {
    return "error_connect";
    }
    $req = "INSERT INTO `notifications_directeur` (`id_service`, `titre`, `notification`) VALUES ('" . $_SESSION['compte']['id_service'] . "', '" . $_POST['titre'] . "', '" . $_POST['notification'] . "')";
    $query = mysqli_query(CONNECT, $req);
    return $query;
    }
    public static function afficher_notifications_admin_dr()
    {
    if (!self::verification_de_la_requete()) {
    return "error_connect";
    }
    $req = "SELECT * FROM `notifications_directeur` where id_service='" . $_SESSION['compte']['id_service'] . "'";
    $query = mysqli_query(CONNECT, $req);
    return $query;
    } */
    public static function afficher_info_admin_ma_notifications()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT * FROM `notifications_directeur` where id='" . $_POST['id_plainte'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_emps_notifications_vu($id_notification)
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT id_service as id, concat_ws(' ',administrateurs.prenom, administrateurs.nom) as emp, date FROM `notifications_directeur_vu` inner JOIN administrateurs on administrateurs.id = notifications_directeur_vu.id_administrateur  WHERE id_notification='" . $id_notification . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_notification_admin()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        //$req = "SELECT * FROM `notifications_directeur` where id_service='" . $_SESSION['compte']['id_service'] . "'";
        $req = "select notifications_directeur.id, notifications_directeur.id_service, notifications_directeur.titre, notifications_directeur.notification, notifications_directeur.date, notifications_directeur_vu.id as vu from notifications_directeur left join notifications_directeur_vu on notifications_directeur_vu.id_notification=notifications_directeur.id where notifications_directeur.id_service='" . $_SESSION['compte']['id_service'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_info_notification_admin_content()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT * FROM `notifications_directeur` where id_service='" . $_SESSION['compte']['id_service'] . "' and id='" . $_POST['id_notification'] . "'";
        $query = mysqli_query(CONNECT, $req);
        self::lire_notif_directeur();
        return $query;
    }
    public static function lire_notif_directeur()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "INSERT IGNORE  INTO `notifications_directeur_vu` (`id_notification`, `id_administrateur`) VALUES ('" . $_POST['id_notification'] . "', '" . $_SESSION['compte']['id'] . "')";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function virifier_si_notif_directeur_et_lire()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }

        $req = "insert into";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_liste_box_messages_admin_emp()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req_admin = "SELECT * FROM administrateurs where id != '" . $_SESSION['compte']['id'] . "'";
        $query_admin = mysqli_query(CONNECT, $req_admin);
        @include('../fix/fix_date.php');
        while ($admin = mysqli_fetch_assoc($query_admin)) {
?>
            <div class="d-flex p-2 bg-white mt-4 position-relative c-pointer msg-short border" id_he="<?php echo $admin['id'] ?>" role_he="administrateurs">
                <img src="<?php if ($admin['image_de_profil'] == null) {
                                echo "../data/profiles/profil.jpeg";
                            } else {
                                echo "../data/profiles/" . $admin['image_de_profil'];
                            } ?>" class="rounded-circle border border-5 <?php echo (($admin['derniere_vu'] == null) or (strtotime($admin["derniere_vu"]) + 3 < time())) ? "border-danger-custom" : "border-success" ?>" style="width: 75px; height:75px" alt="">
                <div class="ms-3 my-auto">
                    <span class="d-block fs-5 fw-bold ">
                        <?php echo $admin['prenom'] . ' ' . $admin['nom'] ?>
                    </span>
                    <?php
                    self::recevoir_le_dernier_message($_SESSION['compte']['id'], "administrateurs", $admin['id'], "administrateurs")
                    ?>
                    <div class="position-absolute border border border-2 fw-bold" style="top:0;right:0; font-size:small;padding: 2px; background-color: #ffe185; opacity: .8;"> Administrateur </div>
                    <!-- &#x2605; -->
                </div>
            </div>
        <?php
        }
        $req_emp = "SELECT * FROM employes";
        $query_emp = mysqli_query(CONNECT, $req_emp);
        while ($emp = mysqli_fetch_assoc($query_emp)) {
        ?>
            <!-- <div class="d-flex p-2 bg-white mt-4 position-relative c-pointer msg-short">
                                                                <img src="<?php if ($emp['image_de_profil'] == null) {
                                                                                echo "../data/profiles/profil.jpeg";
                                                                            } else {
                                                                                echo "../data/profiles/" . $emp['image_de_profil'];
                                                                            } ?>" class="rounded-circle border border-5 border-success" style="width: 75px; height:75px" alt="">
                                                                <div class="ms-3 my-auto">
                                                                    <span class="d-block fs-5 fw-bold">Driss raiss</span>
                                                                    <p><span>You :</span> Hello world how are you !</p>
                                                                    <div class="position-absolute" style="top:30%;right:10px">12:10 PM</div>
                                                                </div>
                                                            </div> -->
            <div class="d-flex p-2 bg-white mt-4 position-relative c-pointer msg-short border" id_he="<?php echo $emp['id'] ?>" role_he="employes">
                <img src="<?php if ($emp['image_de_profil'] == null) {
                                echo "../data/profiles/profil.jpeg";
                            } else {
                                echo "../data/profiles/" . $emp['image_de_profil'];
                            } ?>" class="rounded-circle border border-5 <?php echo (($emp['derniere_vu'] == null) or (strtotime($emp["derniere_vu"]) + 3 < time())) ? "border-danger-custom" : "border-success" ?>" style="width: 75px; height:75px" alt="">
                <div class="ms-3 my-auto">
                    <span class="d-block fs-5 fw-bold ">
                        <?php echo $emp['prenom'] . ' ' . $emp['nom'] ?>
                    </span>
                    <?php
                    self::recevoir_le_dernier_message($_SESSION['compte']['id'], "administrateurs", $emp['id'], "employes")
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
        <script>
            $('.msg-short').click(function() {
                $("#list-messages").hide()
                $("#box-chatt").show()
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_box_msg",
                    type: "POST",
                    // $_POST['id_he'],$_POST['role_he']
                    data: {
                        role_he: $(this).attr("role_he"),
                        id_he: $(this).attr("id_he")
                    },
                    success: function(result) {
                        $("#box-chatt").html(result)
                    }
                })
            })
        </script>
    <?php
        /* return array_merge(
        mysqli_fetch_assoc($query_admin),
        mysqli_fetch_assoc($query_emp)
        ); */
    }
    public static function recevoir_le_dernier_message($me, $role_me, $he, $role_he)
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        /* $req1 = "SELECT * FROM `messages` where id_expediteur in($me, $he ) and id_destinataire in($me, $he) order by date desc limit 1";
        $query1 = mysqli_query(CONNECT, $req1);
        $req2 = "select * from ". mysqli_fetch_assoc($query1)["role"] == "admin" ? "administrateurs" : "employes" . " where id='$he'";
        $query2 = mysqli_query(CONNECT, $req2);
        $result = array_merge($query1, $query2); 
        return $result;*/
        $req = "SELECT * FROM `messages` where (id_expediteur =  '$me" . "_" . "$role_me' and id_destinataire = '$he" . "_" . "$role_he') or (id_expediteur = '$he" . '_' . "$role_he'  and id_destinataire = '$me" . '_' . "$role_me' ) order by date desc limit 1";
    ?>
        <script>
            //console.log("<?php echo $req ?>")
        </script>
        <?php
        $query = mysqli_query(CONNECT, $req);
        $result = mysqli_fetch_assoc($query);

        if ($result) {
        ?>
            <p>
                <?php echo $result['id_expediteur'] == ($_SESSION["compte"]['id'] . "_administrateurs") ? "<span>Vous :</span>" : "" ?> <span class="<?php echo $result['id_expediteur'] == ($_SESSION["compte"]['id'] . "_administrateurs") ? "" : "text-primary" ?>"><?php echo $result['message'] ?></span>
            </p>
            <div class="position-absolute" style="top:30%;right:10px">
                <?php echo date("h:i A", strtotime($result['date'])) ?>
            </div>
        <?php
        } else {
            echo "<p class='fw-lighter' >Démarrer une nouvelle conversation</p>";
        }
    }
    public static function afficher_msgs($me, $role_me, $he, $role_he)
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "SELECT * FROM `messages` where (id_expediteur =  '$me" . '_' . "$role_me' and id_destinataire = '$he" . '_' . "$role_he') or (id_expediteur = '$he" . '_' . "$role_he'  and id_destinataire = '$me" . '_' . "$role_me' ) ";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function afficher_box_msg($role, $id_compte)
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "select concat( prenom, ' ', nom) as full_name, image_de_profil, derniere_vu from $role where id='$id_compte'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function envoyer_msg()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $id_expediteur = $_SESSION['compte']['id'] . '_' . $_POST['role_me'];
        $id_destinataire = $_POST['id_destinataire'] . '_' . $_POST['role_he'];
        $message = $_POST["message"];
        $req = "insert into messages (id_expediteur, id_destinataire, message) values ('$id_expediteur', '$id_destinataire', '$message')";

        $query = mysqli_query(CONNECT, $req);
        return $req;
    }
    public static function mise_a_jour_status()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $req = "UPDATE `administrateurs` SET `derniere_vu` = CURRENT_TIMESTAMP WHERE `administrateurs`.`id` = '" . $_SESSION['compte']['id'] . "'";
        $query = mysqli_query(CONNECT, $req);
        return $query;
    }
    public static function mise_a_jour_header_freind()
    {
        if (!self::verification_de_la_requete()) {
            return "error_connect";
        }
        $result = CompteSecretaires::afficher_box_msg($_POST['role_he'], $_POST['id_he']);
        $data = mysqli_fetch_assoc($result);
        @include('../fix/fix_date.php');
        echo  '<span id="statu_friend_span" role_he="' . $_POST['role_he'] . '" id_he="' . $_POST['id_he'] . '">';
        if ($data['derniere_vu'] == null) {
            echo '<span class="small text-danger fw-light">Déconnecté</span>';
        } else if (strtotime($data["derniere_vu"]) + 3 > time()) {
            echo '<span class="text-success fw-light">En ligne</span>' /* .  date ("h:i:s",time()) . date_default_timezone_get() */;
        } else {
            echo '<span class="fw-light">derniere vu ' . date("h:i A", strtotime($data['derniere_vu'])) . '</span>';
        }
        echo "</span>";
    }
    /* 
public static function func_name()
{
if (!self::verification_de_la_requete()) {
return "error_connect";
}
$req = "";
$query = mysqli_query(CONNECT, $req);
return $query;
} */
}
/**/
switch (@$_GET['do']) {
    case 'mise_a_jour_info':
        $result = CompteSecretaires::mise_a_jour_info();
        if ($result) {
            $req = "SELECT * FROM administrateurs WHERE email='" . $_SESSION['compte']['email'] . "' and mote_de_pass='" . $_SESSION['compte']['mote_de_pass'] . "'";
            $_SESSION['compte'] = mysqli_fetch_assoc(mysqli_query(CONNECT, $req));
        }
        echo $result;
        exit();
    case 'mise_a_jour_image_de_profil':
        $result = CompteSecretaires::mise_a_jour_image_de_profil();
        if ($result) {
            $req = "SELECT * FROM administrateurs WHERE email='" . $_SESSION['compte']['email'] . "' and mote_de_pass='" . $_SESSION['compte']['mote_de_pass'] . "'";
            $_SESSION['compte'] = mysqli_fetch_assoc(mysqli_query(CONNECT, $req));
        }
        echo $result;
        exit();
    case 'mise_a_jour_email':
        $result = CompteSecretaires::mise_a_jour_email();
        if ($result) {
            $req = "SELECT * FROM administrateurs WHERE id='" . $_SESSION['compte']['id'] . "' and mote_de_pass='" . $_SESSION['compte']['mote_de_pass'] . "'";
            $_SESSION['compte'] = mysqli_fetch_assoc(mysqli_query(CONNECT, $req));
        }
        echo $result;
        exit();
    case 'mise_a_jour_mote_de_pass':
        $result = CompteSecretaires::mise_a_jour_mote_de_pass();
        if ($result) {
            $req = "SELECT * FROM administrateurs WHERE id='" . $_SESSION['compte']['id'] . "' and email='" . $_SESSION['compte']['email'] . "'";
            $_SESSION['compte'] = mysqli_fetch_assoc(mysqli_query(CONNECT, $req));
        }
        echo $result;
        exit();

    case 'afficher_les_employes':
        $result = CompteSecretaires::afficher_les_employes();
        while ($data = mysqli_fetch_assoc($result)) {
            echo "<tr user-id='" . $data['id'] . "'>";
            echo "<td>" . $data['id'] . "</td>";
            echo "<td>" . $data['nom'] . "</td>";
            echo "<td>" . $data['prenom'] . "</td>";
            echo "<td>11</td>";
            //echo "<td>".$data['derniere_vue	']."</td>";
            echo "<td class='text-danger td-status'>déconnecté</td>";
            echo "<tr/>";
        }
        exit();
    case "afficher_info_employe":
        $result = CompteSecretaires::afficher_info_employe();
        $data = mysqli_fetch_assoc($result);
        ?>
        <div class="text-center my-4 ">
            <img src="<?php if ($data['image_de_profil'] == null) {
                            echo "../data/profiles/profil.jpeg";
                        } else {
                            echo "../data/profiles/" . $data['image_de_profil'];
                        } ?>" class="border border-3 border-dark" style="width:200px;" alt="">
        </div>
        <table class="table table-hover ">
            <tbody>
                <tr>
                    <th class="w-auto">ID : </th>
                    <td style="width: 85%;">
                        <?php echo $data['id_admin'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Nom : </th>
                    <td style="width: 85%;">
                        <?php echo $data['nom_admin'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Prénom : </th>
                    <td style="width: 85%;">
                        <?php echo $data['prenom'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Rôle : </th>
                    <td style="width: 85%;">
                        <?php echo $data['nom_service'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Email : </th>
                    <td style="width: 85%;">
                        <?php echo $data['email'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Téléphone : </th>
                    <td style="width: 85%;">
                        <?php echo $data['telephone'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">DOB : </th>
                    <td style="width: 85%;">
                        <?php echo $data['date_de_naissance'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Genre : </th>
                    <td style="width: 85%;">
                        <?php echo $data['genre'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Ville : </th>
                    <td style="width: 85%;">
                        <?php echo $data['ville'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Adresse : </th>
                    <td style="width: 85%;">
                        <?php echo $data['adresse'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">DOR : </th>
                    <td style="width: 85%;">
                        <?php echo $data['date_de_linscription'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Statut :</th>
                    <td style="width: 85%;">
                        <?php
                        @include('../fix/fix_date.php');
                        
                        if ($data['derniere_vu'] == null) {
                            echo '<span class="small text-danger">Déconnecté</span>';
                        } else if (strtotime($data["derniere_vu"]) + 3 > time()) {
                            echo '<span class="text-success">En ligne</span>';
                        } else {
                            echo '<span>derniere vu ' . date("h:i A", strtotime($data['derniere_vu'])) . '</span>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-auto">Statut du compte:</th>
                    <td style="width: 85%;">
                        <select class="form-select w-auto" id="change_statu_compte_emp" id-employe="<?php echo $data['id_admin'] ?>">
                            <option value="normal" <?php if ($data['statut_du_compte'] == "normal") {
                                                        echo "selected";
                                                    } ?> class="text-success">Normal</option>
                            <option value="suspendu" <?php if ($data['statut_du_compte'] == "suspendu") {
                                                            echo "selected";
                                                        } ?> class="text-danger">Suspendu</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <script>
            $('#change_statu_compte_emp').on('change', function() {
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=change_statu_compte_emp",
                    type: "POST",
                    data: {
                        id_employe: $(this).attr('id-employe'),
                        new_statu: $(this).val()
                    },
                    success: function(result) {
                        //console.log(result)
                    }
                })
            })
        </script>
    <?php
        exit();
    case 'change_statu_compte_emp':
        $result = CompteSecretaires::change_statu_compte_emp();
        //echo $result;
        exit();

    case 'ajouter_emp':
        $result = CompteSecretaires::ajouter_emp();
        echo $result;
        exit();
        // plaintes 
        /*     case 'afficher_plaintes_emps':
    $result = CompteSecretaires::afficher_plaintes_emps();
    while ($plainte = mysqli_fetch_assoc($result)) {
    echo "<tr class='plainte-employee-short'>";
    echo "<td>" . $plainte['id'] . "</td>";
    echo "<td>" . $plainte['titre'] . "</td>";
    echo "<td>" . $plainte['envoyeur'] . "</td>";
    echo "<td>" . $plainte['date'] . "</td>";
    echo "</tr>";
    }
    exit();
    */

    case 'afficher_info_plainte':
        $result = CompteSecretaires::afficher_info_plainte();
        $data = mysqli_fetch_assoc($result);
    ?>
        <div class="mb-3 pt-4">
            <label class="form-label">Envoyeur</label>
            <div class="form-control">
                <?php echo $data['envoyeur'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <div class="form-control">
                <?php echo $data['titre'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <div class="form-control">
                <?php echo $data['date'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Plainte</label>
            <div class="form-control">
                <pre><?php echo $data['plainte'] ?></pre>
            </div>
        </div>
    <?php
        exit();
    case 'envoyer_plainte_admin':
        $result = CompteSecretaires::envoyer_plainte_admin();
        echo $result;
        exit();
    case 'afficher_info_ma_plainte':
        $result = CompteSecretaires::afficher_info_ma_plainte();
        $data = mysqli_fetch_assoc($result);
    ?>
        <div class="mb-3 pt-4">
            <label class="form-label">Titre</label>
            <div class="form-control">
                <?php echo $data['titre'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <div class="form-control">
                <?php echo $data['date'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Plainte</label>
            <div class="form-control">
                <pre><?php echo $data['plainte'] ?></pre>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Vu</label>
            <div class="form-control">
                <?php
                if ($data['vu']) {
                    echo "<span class='text-success'>Oui</span>";
                } else {
                    echo "<span class='text-danger'>Non</span>";
                }
                ?>
            </div>
        </div>
    <?php
        exit();
        // demande
        /* case 'afficher_demandes_emps':
    $result = CompteSecretaires::afficher_demandes_emps();
    while ($demande = mysqli_fetch_assoc($result)) {
    echo "<tr class='demande-employee-short'>";
    echo "<td>" . $demande['id'] . "</td>";
    echo "<td>" . $demande['titre'] . "</td>";
    echo "<td>" . $demande['envoyeur'] . "</td>";
    echo "<td>" . $demande['date'] . "</td>";
    ?>
    <td>
    <select class="form-select w-auto force-not-work" id-demande="<?php echo $demande['id'] ?>">
    <option <?php if($demande['reponse'] == null){echo "selected";} ?> value="null">Traitement</option>
    <option <?php if($demande['reponse'] == 1){echo "selected";} ?> value="1">Accepter</option>
    <option <?php if($demande['reponse'] == 0 ){echo "selected";} ?> value="0">Refuser</option>
    </select>
    </td>
    <?php
    echo "</tr>";
    }
    exit(); */

    case 'afficher_info_demande':
        $result = CompteSecretaires::afficher_info_demande();
        $demande = mysqli_fetch_assoc($result);
    ?>
        <div class="mb-3 pt-4">
            <label class="form-label">Envoyeur</label>
            <div class="form-control">
                <?php echo $demande['envoyeur'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <div class="form-control">
                <?php echo $demande['titre'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <div class="form-control">
                <?php echo $demande['date'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Demande</label>
            <div class="form-control">
                <pre><?php echo $demande['demande'] ?></pre>
            </div>
        </div>

        <script>
            $(".reponse_demande_select_i").on('change', function() {
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=mise_a_jour_reponse_demande",
                    type: "POST",
                    data: {
                        reponse: $(this).val(),
                        id_demande: $(this).attr('id-demande')
                    },
                    success: (result) => {
                        //$(".reponse_demande_select[id-demande=" + $(this).attr('id-demande') + "]")[0].setAttribute('id-demande', $(this).val())
                        //$(".reponse_demande_select[id-demande=" + $(this).attr('id-demande') + "]")[0].value = $(this).val()
                        //console.log(".reponse_demande_select [id-demande="+$(this).attr('id-demande')+"]")
                        $(".reponse_demande_select[id-demande=" + $(this).attr('id-demande') + "]").val($(this).val())
                    }
                })
            })
        </script>
    <?php
        exit();
    case 'envoyer_demande_admin':
        $result = CompteSecretaires::envoyer_demande_admin();
        echo $result;
        exit();
    case 'afficher_info_ma_demande':
        $result = CompteSecretaires::afficher_info_ma_demande();
        $data = mysqli_fetch_assoc($result);
    ?>
        <div class="mb-3 pt-4">
            <label class="form-label">Titre</label>
            <div class="form-control">
                <?php echo $data['titre'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <div class="form-control">
                <?php echo $data['date'] ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Demande</label>
            <div class="form-control">
                <pre><?php echo $data['demande'] ?></pre>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Réponse</label>
            <div class="form-control">
                <?php
                if ($data['reponse'] === '1') {
                    echo "<span class='text-success'>Accepter</span>";
                } else if ($data['reponse'] === '0') {
                    echo "<span class='text-danger'>Refuser</span>";
                } else if ($data['reponse'] === null) {
                    echo "<span>Traitement</span>";
                }
                ?>
            </div>
        </div>
    <?php
        exit();
    case "mise_a_jour_reponse_demande":
        $result = CompteSecretaires::mise_a_jour_reponse_demande();
        echo $result;
        exit();
    case "envoyer_notification_admin":
        $result = CompteSecretaires::envoyer_notification_admin();
        echo $result;
        exit();
    case "afficher_info_admin_ma_notifications":
        $result = CompteSecretaires::afficher_info_admin_ma_notifications();
        $notification = mysqli_fetch_assoc($result);
    ?>
        <div class="bg-white">
            <table class="table w-auto table-borderless  fs-4">
                <tr>
                    <th>Date :</th>
                    <td>
                        <span class="">
                            <?php echo date('d M Y', strtotime($notification['date'])) ?>
                        </span>
                        <em class="small mark border fw-light">
                            <?php echo date('h:i:s a', strtotime($notification['date'])) ?>
                        </em>
                    </td>
                </tr>
                <tr>
                    <th>Titre :</th>
                    <td>
                        <?php echo $notification['titre'] ?>
                    </td>
                </tr>
            </table>

            <div id="div-content-notification" class="border border-3 p-3 ">
                <pre><?php echo $notification['notification'] ?></pre>
            </div>
            <!-- <button class="btn btn-success my-3 w-100 fs-3">Télecharger</button>
        <hr>
        <h1>Assets :</h1>
        <div>
            <button class="btn btn-primary">Fichier 1</button>
            <button class="btn btn-primary">Fichier 2</button>
            <button class="btn btn-primary">Fichier 3</button>
        </div> -->
        </div>
        <hr>
        <div class="bg-white p-2">
            <h1 class="text-decoration-underline">Vu par:</h1>
            <div class="table-responseve">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employe</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = CompteSecretaires::afficher_emps_notifications_vu($notification['id']);
                        while ($emp = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $emp['id'] . "</td>";
                            echo "<td>" . $emp['emp'] . "</td>";
                            echo "<td>" . $emp['date'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        exit();
    case "afficher_info_notification_admin":
        $result = CompteSecretaires::afficher_info_notification_admin();
        while ($notif = mysqli_fetch_assoc($result)) {
        ?>
            <tr class="<?php echo $notif['vu'] != null ? "tr-notification-unread" : "tr-notification-read" ?> tr-notification" notif-id="<?php echo $notif['id'] ?>">
                <!--<td class="td-notif-input"><input type="checkbox" class="form-check-input form-check-notification"></td>-->
                <td><span class="title-notification">
                        <?php echo $notif['titre'] ?>
                    </span></td>
                <td>
                    <?php echo substr($notif['notification'], 0, 15) ?>...
                </td>
                <td>
                    <span class="notification-short-del-read">
                        <i class="bi bi-envelope-open-fill read-notification "></i>
                        <!-- <i class="bi bi-trash3 trash-notifications"></i> -->
                    </span>
                    <span class="notification-date">
                        <?php echo $notif['date'] ?>
                    </span>
                </td>
            </tr>
        <?php
        }
        exit();
    case "afficher_info_notification_admin_content":
        $result = CompteSecretaires::afficher_info_notification_admin_content();
        $notification = mysqli_fetch_assoc($result);
        ?>
        <table class="table w-auto table-borderless  fs-4">
            <tr>
                <th>Date :</th>
                <td>
                    <span class="">
                        <?php echo date('d M Y', strtotime($notification['date'])) ?>
                    </span>
                    <em class="small mark border fw-light">
                        <?php echo date('h:i:s a', strtotime($notification['date'])) ?>
                    </em>
                </td>
            </tr>
            <tr>
                <th>Titre :</th>
                <td>
                    <?php echo $notification['titre'] ?>
                </td>
            </tr>
        </table>

        <div id="div-content-notification" class="border border-3 p-3 ">
            <pre><?php echo $notification['notification'] ?></pre>
        </div>
    <?php
        exit();
    case "lire_notif_directeur":
        $result = CompteSecretaires::lire_notif_directeur();
        echo $result;
        exit();
    case "virifier_si_notif_directeur_et_lire":
        $result = CompteSecretaires::virifier_si_notif_directeur_et_lire();
        echo $result;
        exit();
    case "afficher_msgs":
        $result = CompteSecretaires::afficher_msgs($_SESSION["compte"]['id'], "administrateurs", $_POST['id_he'], $_POST['role_he']);
        while ($msg = mysqli_fetch_assoc($result)) {
            print("<pre/>");
            print_r($msg);
            print("<pre/>");
        }
        exit();
    case "afficher_box_msg":
        $result = CompteSecretaires::afficher_box_msg($_POST['role_he'], $_POST['id_he']);
        $data = mysqli_fetch_assoc($result);
        @include('../fix/fix_date.php');
    ?>
        <div class="d-flex p-2  position-relative border-bottom border-3" id="header-freind-dd" role_he="<?php echo $_POST['role_he'] ?>" id_he="<?php echo $_POST['id_he']; ?>">

            <img src="<?php if ($data['image_de_profil'] == null) {
                            echo "../data/profiles/profil.jpeg";
                        } else {
                            echo "../data/profiles/" . $data['image_de_profil'];
                        } ?>" class="rounded border border-2" style="width: 90px; height:90px" alt="">
            <div class="ms-3 my-auto">
                <span class="d-block fs-5 fw-bold">
                    <?php echo $data['full_name'] ?>
                </span>
                <?php
                /* 
                if ($data['derniere_vu'] == null) {
                    echo '<span class="small text-danger fw-light">Déconnecté</span>';
                } else if (strtotime($data["derniere_vu"]) + 3 > time()) {
                    echo '<span class="text-success fw-light">En ligne</span>' /* .  date ("h:i:s",time()) . date_default_timezone_get() ;
                } else {
                    echo '<span class="fw-light">derniere vu ' . date("h:i A", strtotime($data['derniere_vu'])) . '</span>';
                    //echo date("y-m-d h-i-s",strtotime($data["derniere_vu"])) . " / " . date("y-m-d h-i-s",time()) ;
                } */
                echo  '<span id="statu_friend_span" role_he="' . $_POST['role_he'] . '" id_he="' . $_POST['id_he'] . '">';
                if ($data['derniere_vu'] == null) {
                    echo '<span class="small text-danger fw-light">Déconnecté</span>';
                } else if (strtotime($data["derniere_vu"]) + 3 > time()) {
                    echo '<span class="text-success fw-light">En ligne</span>';
                } else {
                    echo '<span class="fw-light">derniere vu ' . date("h:i A", strtotime($data['derniere_vu'])) . '</span>';
                }
                echo "</span>"
                ?>
            </div>

            <i class="bi bi-x-square-fill fs-1 c-pointer text-danger" id="btn-close-box-chatt" style="position: absolute; right: 10px; top:5px"></i>
        </div>
        <div class="p-2">
            <!-- ssssssssssssssssssssssssss -->
            <div class="frameMSG" id="frameMSG_" style="height: 600px; overflow-y:auto">

                <?php
                $result = CompteSecretaires::afficher_msgs($_SESSION["compte"]['id'], "administrateurs", $_POST['id_he'], $_POST['role_he']);
                while ($msg = mysqli_fetch_assoc($result)) {
                    if ($msg['id_expediteur'] == ($_SESSION["compte"]['id'] . "_administrateurs")) {
                ?>
                        <div class="me">
                            <div class="mmmsg">
                                <?php echo $msg['message'] ?>
                                <div class="info_Msgg"><span>
                                        <?php echo date("h:i", strtotime($msg['date'])) ?>
                                    </span></div>
                            </div>
                        </div>
                    <?php
                    } else { ?>
                        <div class="he">
                            <div class="mmmsg">
                                <?php echo $msg['message'] ?>
                                <div class="info_Msgg"><span>
                                        <?php echo date("h:i", strtotime($msg['date'])) ?>
                                    </span></div>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
            <!-- eeeeeeeeeeeeeeeeeeeeeeeeee -->
        </div>
        <div>
            <div class="input-group input-group-lg ">
                <input id="input-message-aeria" type="text" class="form-control rounded-0 border-3 border-top border-bottom-0 border-end-0 border-start-0" placeholder="Ecrire un message...">
                <button class="btn btn-success rounded-0" type="button" id="button-send-msg"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
        <script>
            function send_msg() {
                if ($("#input-message-aeria").val().trim() != "") {
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=envoyer_msg",
                        type: "POST",
                        data: {
                            message: $("#input-message-aeria").val(),
                            role_me: "administrateurs",
                            role_he: $("#header-freind-dd").attr('role_he'),
                            id_destinataire: $("#header-freind-dd").attr('id_he')
                        },
                        success: function(result) {
                            //console.log(result)
                        }
                    })
                }
                $("#input-message-aeria").val("")
                $("#input-message-aeria").focus()
                /* setTimeout(() => {
                    try {
                        var frame_msg = document.querySelector("#frameMSG_")
                        frame_msg.scrollTo(0, frame_msg.scrollHeight)
                    } catch (e) {

                    }
                }, 1); */

            }
            $('#btn-close-box-chatt').click(() => {
                $("#box-chatt").hide()
                $("#list-messages").show()
                clearInterval(update_box_msg)
            })
            $('#button-send-msg').click(() => {
                send_msg();
            })
            $('#input-message-aeria').on('keydown', function(e) {
                if (e.keyCode === 13) {
                    send_msg()
                }
            })
            var frame_msg = document.querySelector("#frameMSG_")
            frame_msg.scrollTo(0, frame_msg.scrollHeight)
            $("#input-message-aeria").focus()
            var update_box_msg = setInterval(() => {
                try {
                    if ($("#header-freind-dd").length == 0) {
                        throw 1011231
                    }
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=mise_a_jour_box_message",
                        type: "POST",
                        data: {
                            id_he: $("#header-freind-dd").attr('id_he'),
                            role_he: $("#header-freind-dd").attr('role_he')
                        },
                        success: function(result) {
                            $("#frameMSG_").html(result)
                        }
                    })
                } catch (e) {
                    clearInterval(update_box_msg)
                }
            }, 500);

            var inter_val_header_friend = setInterval(() => {
                try {
                    if ($("#statu_friend_span").length == 0) {
                        throw 1011231
                    }
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=mise_a_jour_header_freind",
                        type: "POST",
                        data: {
                            id_he: $('#statu_friend_span').attr('id_he'),
                            role_he: $('#statu_friend_span').attr('role_he')
                        },
                        success: function(result) {
                            $('#statu_friend_span').html(result);
                            //console.log(result)
                        }
                    })
                } catch (e) {
                    clearInterval(inter_val_header_friend)
                }
            }, 2000);
        </script>
        <?php
        exit();
    case "envoyer_msg":
        $result = CompteSecretaires::envoyer_msg();
        echo $result;
        exit();
    case "mise_a_jour_box_message":
        $result = CompteSecretaires::afficher_msgs($_SESSION["compte"]['id'], "administrateurs", $_POST['id_he'], $_POST['role_he']);

        while ($msg = mysqli_fetch_assoc($result)) {
            if ($msg['id_expediteur'] == ($_SESSION["compte"]['id'] . "_administrateurs")) {
        ?>
                <div class="me">
                    <div class="mmmsg">
                        <?php echo $msg['message'] ?>
                        <div class="info_Msgg"><span>
                                <?php echo date("h:i", strtotime($msg['date'])) ?>
                            </span></div>
                    </div>
                </div>
            <?php
            } else { ?>
                <div class="he">
                    <div class="mmmsg">
                        <?php echo $msg['message'] ?>
                        <div class="info_Msgg"><span>
                                <?php echo date("h:i", strtotime($msg['date'])) ?>
                            </span></div>
                    </div>
                </div>
<?php
            }
        }

        exit();
    case "afficher_list_friend_short":
        CompteSecretaires::afficher_liste_box_messages_admin_emp();
        exit();
    case "mise_a_jour_status":
        $result = CompteSecretaires::mise_a_jour_status();
        echo $result;
        exit();
    case "mise_a_jour_header_freind":
        $result = CompteSecretaires::mise_a_jour_header_freind();
        echo $result;
        exit();
    case "mise_a_jour_reponse_plainte":
        $result = CompteSecretaires::mise_a_jour_reponse_plainte();
        echo $result;
        exit();
        /* case "":
    $result = CompteSecretaires::func();
    echo $result;
    exit(); */
    default:
        # code...
        break;
}
