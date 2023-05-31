<?php
session_start();
class Secretaire
{
    public static function notifications()
    {
?>
        <div class="notifications" style="position: relative;">
            <style>
                .notifications .nav-borders .nav-link.active {
                    color: #0061f2;
                    border-bottom-color: #0061f2;
                }

                .notifications .nav-borders .nav-link {
                    color: #69707a;
                    border-bottom-width: 0.125rem;
                    border-bottom-style: solid;
                    border-bottom-color: transparent;
                    padding-top: 0.5rem;
                    padding-bottom: 0.5rem;
                    padding-left: 0;
                    padding-right: 0;
                    margin-left: 1rem;
                    margin-right: 1rem;
                }
            </style>
            <nav class="nav nav-borders ">
                <span class="nav-link ms-0 active c-pointer" id="btn-add-notification">Ajouter un notification</span>
                <span class="nav-link c-pointer" id="btn-notification-emp">Mes notifications</span>
            </nav>
            <hr class="mt-0 mb-0">

            <div id="div-add-notification" class="div-frame mt-4">
                <!-- s add-notification -->
                <form id="form-add-notification">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input required type="text" class="form-control" id="title" name="titre" placeholder="Tapez un titre approprié pour la notification">
                    </div>
                    <div class="mb-3">
                        <label for="notification" class="form-label">Notification</label>
                        <textarea required class="form-control" id="notification" rows="13" name="notification" placeholder="Écrivez la notification ici"></textarea>
                    </div>
                    <div class="mb-3">
                        <?php
                        require_once('../php_cl/Secretaires.php');
                        CompteSecretaires::get_services();
                        ?>
                    </div>
                    <button class="btn btn-success w-100 btn-lg">Envoyer</button>
                </form>
                <!-- e add-notification -->
            </div>
            <div id="div-notification-emp" class="div-frame" style="display: none;">
                <div class="table-responsive mt-4">
                    <table class="table table-hover bg-white" id="notification-my-table-all">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Notification</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once('../php_cl/Secretaires.php');
                            $result = CompteSecretaires::afficher_notifications_admin();
                            while ($notification = mysqli_fetch_assoc($result)) {
                                echo "<tr class='notification-my-short' id-notification='" . $notification['id'] . "'>";
                                echo "<td>" . $notification['id'] . "</td>";
                                echo "<td>" . $notification['titre'] . "</td>";
                                echo "<td>" . substr($notification['notification'], 0, 15) . "...</td>";
                                echo "<td>" . $notification['date'] . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div id="notification-info-my" style="display: none;" class="position-relative">
                        <i class="bi bi-x-square-fill fs-1 c-pointer btn-close-hover text-danger" id="btn-close-notification-ma-info" style="position: absolute; right: 0; top:0"></i>
                        <div id="notification-info-my-content" class="">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="../scripts/notifications.js"></script>
        <script>
            function active_btn(elm) {
                $(".notifications .nav .nav-link").each(function() {
                    $(this).removeClass('active')
                })
                $(elm).addClass('active')
            }

            function active_div(elm) {
                $(".notifications .div-frame").each(function() {
                    $(this).hide()
                })
                $(elm).show()
            }

            $("#btn-add-notification").click(function() {
                active_btn($(this))
                active_div("#div-add-notification")
            })
            $("#btn-notification-emp").click(function() {
                active_btn($(this))
                active_div("#div-notification-emp")
            })
            $("#form-add-notification").on("submit", function(e) {
                e.preventDefault()
                let formData = new FormData(this)
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=envoyer_notification_admin",
                    type: "POST",
                    data: formData,
                    a: console.log(formData),
                    success: function(result) {
                        if (result == 1) {
                            modale(
                                "Success",
                                "La notifications a été envoyée avec succès",
                                "modal-success"
                            )
                            $('.btn-notifications').click()
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                })
            })
            //------------
            $('.notifications #div-notification-emp .notification-my-short').click(function() {
                $('#notification-my-table-all').hide()
                $('#notification-info-my').show()
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_info_admin_ma_notifications",
                    type: "POST",
                    data: {
                        id_plainte: $(this).attr('id-notification')
                    },
                    success: function(result) {
                        $('#notification-info-my-content').html(result)
                        //console.log(result)
                    }
                })
            })
            $('#btn-close-notification-ma-info').click(function() {
                $('#notification-info-my').hide()
                $('#notification-my-table-all').show()
            })
            //------------
            $(".tr-notification").each(function() {
                $(this).click(() => {
                    $(this).removeClass('tr-notification-unread')
                    $(this).addClass('tr-notification-read')
                    $("#div-notii-info").show()
                    $("#table-notification-first").hide()
                    //console.log($(this).attr("notif-id"))
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=afficher_info_notification_admin_content",
                        type: "POST",
                        data: {
                            id_notification: $(this).attr("notif-id")
                        },
                        success: function(result) {
                            $('#div-notii-info-content').html(result)
                        }
                    })
                })
            })
            $("#btn-close-notification-details_").click(() => {
                $("#div-notii-info").hide()
                $("#table-notification-first").show()

            })
            $(".read-notification").click(function() {
                $(this).parent().parent().parent().removeClass("tr-notification-unread")
                $(this).parent().parent().parent().addClass("tr-notification-read")
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=lire_notif_directeur",
                    type: "POST",
                    data: {
                        id_notification: $(this).parent().parent().parent().attr('notif-id')
                    },
                    success: function(result) {
                        if (result !== '1') {
                            //console.log("error duplicate")
                        } else {
                            //console.log(result)
                        }
                    }
                })
            })
        </script>
    <?php
    }

    public static function messages()
    {
    ?>
        <style>
            .msg-short:hover {
                background-color: #e6edf3 !important;
            }
        </style>
        <style>
            .he>div,
            .me>div {
                position: relative;
                min-width: 100px;
                overflow-wrap: break-word;
                border: 1px solid;
                padding-bottom: 25px;
            }

            .he>div {
                background-color: #EFEFEF;
                border-bottom-right-radius: 8px;
                border-top-right-radius: 8px;
                margin-left: 5px;
            }

            .me>div {
                float: right;
                background-color: #a7c4ff66;
                border-bottom-left-radius: 8px;
                border-top-left-radius: 8px;
                margin-right: 5px;
            }

            .mmmsg {
                display: inline-block;
                padding: 5px;
                max-width: 85%;
                font-size: 20px;
            }

            .info_Msgg {
                position: absolute;
                bottom: 0;
                right: 0;
                height: 20px;
                border-top: 1px solid;
                border-left: 1px solid;
                display: flex;
            }

            .info_Msgg span {
                margin: auto 2px;
                font-size: 13px;
                font-weight: 500;
            }

            .frameMSG>div {
                margin-top: 5px;
                overflow: hidden;
            }

            #btn-close-box-chatt {
                opacity: .8;
            }

            #btn-close-box-chatt:hover {
                opacity: 1;
            }
        </style>
        <div class="messages">
            <div id="list-messages">
                <!-- <div class="input-group input-group-lg mb-3">
                    <input type="text" class="form-control" id="input-serch-input" placeholder="Nom, prénom..." aria-describedby="button-addon2">
                    <button class="btn btn-primary" type="button" id="button-addon2">Recherche</button>
                </div> -->
                <div id="div_list_short_friend">
                    <?php
                    require_once('../php_cl/Secretaires.php');
                    CompteSecretaires::afficher_liste_box_messages_admin_emp();
                    ?>
                </div>
            </div>
            <div id="box-chatt" class="border border-3 bg-white" style="display: none;">
            </div>
        </div>
        <script>
            setInterval(() => {
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_list_friend_short",
                    success: function(result) {
                        $('#div_list_short_friend').html(result)
                    }
                })
            }, 3000);
        </script>
    <?php
    }
    public static function profile()
    {
    ?>
        <div class="profile">
            <div class="">
                <nav class="nav nav-borders">
                    <span class="nav-link active ms-0 c-pointer" id="btn-profil">Profil</span>
                    <span class="nav-link c-pointer" id="btn-security">Sécurité</span>
                </nav>
                <hr class="mt-0 mb-4">
                <!-- s change info -->
                <div class="row" id="row-profil">
                    <div class="col-xl-4">

                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Image de profil</div>
                            <div class="card-body text-center">
                                <style>
                                    .profile .img-account-profile {
                                        height: 200px;
                                        width: 200px;

                                    }
                                </style>
                                <img class="img-account-profile rounded-circle mb-2 border border-2" src="<?php if ($_SESSION['compte']['image_de_profil'] == null) {
                                                                                                                echo "../data/profiles/profil.jpeg";
                                                                                                            } else {
                                                                                                                echo "../data/profiles/" . $_SESSION['compte']['image_de_profil'];
                                                                                                            } ?>" alt="profil" id="profil-img__">

                                <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                                <form id="form-update-profil" method="POST" action="../test.php" enctype="multipart/form-data">
                                    <input type="file" name="new-profil-img" class="d-none" id="input-profil-img">
                                    <button class="btn btn-primary" type="button" id="btn-profil-img">Télécharger une nouvelle image</button>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">

                        <div class="card mb-4">
                            <div class="card-header">Détails du compte</div>
                            <div class="card-body">
                                <form id="form_update_info">

                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputFirstName">Prénom</label>
                                            <input class="form-control" id="inputFirstName" type="text" required name="prenom" placeholder="Entrez votre prénom" value="<?php echo $_SESSION['compte']['prenom'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputLastName">Nom</label>
                                            <input class="form-control" id="inputLastName" type="text" required placeholder="Entrez votre nom" name="nom" value="<?php echo $_SESSION['compte']['nom'] ?>">
                                        </div>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputTel">Téléphone</label>
                                            <input class="form-control" id="inputTel" type="tel" required placeholder="Entrez votre numéro de téléphone" name="telephone" value="<?php echo $_SESSION['compte']['telephone'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputDOB">Date de naissance</label>
                                            <input class="form-control" id="inputDOB" type="date" required name="date_de_naissance" value="<?php echo $_SESSION['compte']['date_de_naissance'] ?>">
                                        </div>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputGender">Genre</label>
                                            <select name="genre" required id="inputGender" class="form-control">
                                                <option value="homme" <?php if ($_SESSION['compte']['genre'] == "homme") {
                                                                            echo "selected";
                                                                        } ?>>Homme</option>
                                                <option value="femme" <?php if ($_SESSION['compte']['genre'] == "femme") {
                                                                            echo "selected";
                                                                        } ?>>Femme</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <labedl class="small mb-1" for="inputCity">Ville</labedl>
                                            <input class="form-control" required id="inputCity" type="text" name="ville" value="<?php echo $_SESSION['compte']['ville'] ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputAddress">Adresse</label>
                                        <input class="form-control" id="inputAddress" type="text" required placeholder="Entrez votre adresse" name="adresse" value="<?php echo $_SESSION['compte']['adresse'] ?>">
                                    </div>
                                    <!--  -->
                                    <button class=" btn btn-primary" type="submit" id="update-info">Sauvegarder les modifications</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- e change info -->
                <!-- s change password and email -->
                <div class="row" style="display:none" id="row-security">
                    <div class="col">
                        <div class="card mb-4">
                            <div class="card-header">Changer l'email adresse</div>
                            <div class="card-body">
                                <form id="form-update-email" method="POST">
                                    <div class="mb-3">
                                        <label class="mb-1" for="email">Email</label>
                                        <input required class="form-control" id="email" name="email" type="email" placeholder="Entrez votre email" value="<?php echo $_SESSION['compte']['email'] ?>">
                                    </div>
                                    <div class=" mb-3">
                                        <label class="mb-1" for="inputPassword">Mot de passe</label>
                                        <input required class="form-control" id="inputPassword" name="password" type="password" placeholder="Entrez le mot de passe">
                                    </div>
                                    <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                </form>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">Changer le mot de passe</div>
                            <div class="card-body">
                                <form id="form-update-password" method="POST">
                                    <div class="mb-3">
                                        <label class="small mb-1" for="currentPassword">Mot de passe actuel</label>
                                        <input required class="form-control" name="currentPassword" id="currentPassword" type="password" placeholder="Entrer le mot de passe actuel">
                                    </div>

                                    <div class="mb-3">
                                        <label class="small mb-1" for="newPassword">Nouveau mot de passe</label>
                                        <input required class="form-control" name="newPassword" id="newPassword" type="password" placeholder="Entrez un nouveau mot de passe">
                                    </div>

                                    <div class="mb-3">
                                        <label class="small mb-1" for="confirmPassword">Confirmez le mot de passe</label>
                                        <input required class="form-control" name="confirmPassword" id="confirmPassword" type="password" placeholder="Confirmer le nouveau mot de passe">
                                    </div>
                                    <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- e change password and email -->
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $("#btn-profil").click(function() {
                    $(this).addClass("active")
                    $("#btn-security").removeClass("active")

                    $("#row-profil").show()
                    $("#row-security").hide()
                })
                $("#btn-security").click(function() {
                    $(this).addClass("active")
                    $("#btn-profil").removeClass("active")

                    $("#row-security").show()
                    $("#row-profil").hide()
                })
                $("#btn-profil-img").click(() => {
                    $("#input-profil-img").click()
                })
                $("#input-profil-img").change(function() {
                    if ($(this).val() != "") {
                        $("#form-update-profil").submit()
                    }
                })
                $("#form-update-profil").submit(function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=mise_a_jour_image_de_profil",
                        type: "POST",
                        data: formData,
                        success: function(result) {
                            if (result == 1) {
                                $.get('../interfaces/header.php', function(data) {
                                    $('#div-header').html(data)
                                    $("#profil-img__").attr('src', $("#profil-img").attr('src'))
                                    modale(
                                        "Success",
                                        "Votre photo de profil a été mise à jour avec succès",
                                        "modal-success"
                                    )
                                })
                            } else if (result == "error_not_img") {
                                modale(
                                    "Erreur",
                                    "Le profil doit être une image",
                                    "modal-danger"
                                )
                            } else if (result == "error_not_size") {
                                modale(
                                    "Erreur de taille",
                                    "Votre photo de profil n'a pas été mise à jour en raison de sa grande taille. Réessayez avec une photo de moins de 5 Mo.",
                                    "modal-warning"
                                )
                            } else {
                                modale(
                                    "Erreur",
                                    "Votre photo de profil n'a pas été mise à jour, veuillez réessayer plus tard",
                                    "modal-warning"
                                )
                            }
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                })
                $("#change-password").click(() => {
                    if (1) {
                        modale(
                            "Le mot de passe a été changé avec succès",
                            "",
                            "modal-success"
                        )
                    }
                })

                // --------------
                $("#form_update_info").on("submit", function(ev) {
                    ev.preventDefault(); // Prevent browser default submit.

                    let formData = new FormData(this);

                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=mise_a_jour_info",
                        type: "POST",
                        data: formData,
                        success: function(result) {

                            if (result == 1) {
                                $.get('../interfaces/header.php', function(data) {
                                    $('#div-header').html(data)
                                })
                                modale(
                                    "Vos informations ont été mises à jour avec succès",
                                    "",
                                    "modal-success"
                                )
                            } else {
                                modale(
                                    "Erreur",
                                    "Vos informations n'ont pas été mises à jour, veuillez réessayer plus tard",
                                    "modal-warning"
                                )
                            }
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                });
                // --------------
                $('#form-update-email').on('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    // update email
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=mise_a_jour_email",
                        type: "POST",
                        data: formData,
                        success: function(result) {
                            //alert(result)
                            if (result == "success") {
                                modale(
                                    "Success",
                                    "Votre e-mail a été modifié avec succès",
                                    "modal-success"
                                )
                            } else if (result == "error_email_exists") {
                                modale(
                                    "Erreur",
                                    "vous ne pouvez pas utiliser cet email car il existe déjà",
                                    "modal-warning"
                                )
                                $('#email').css("border", "red solid 1px")
                                $("#email").on('focus', function() {
                                    $(this).css('border', 'solid #69707a 1px')
                                })
                            } else if (result == "error_password") {
                                modale(
                                    "Erreur",
                                    "Le mot de passe que vous avez entré est incorrect",
                                    "modal-danger"
                                )
                                $('#inputPassword').css("border", "red solid 1px")
                                $("#inputPassword").on('focus', function() {
                                    $(this).css('border', 'solid #69707a 1px')
                                })
                            } else {
                                modale(
                                    "Erreur",
                                    "Votre email n'a pas été mis à jour, veuillez réessayer plus tard",
                                    "modal-warning"
                                )
                            }
                            $('#inputPassword').val('')
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                })
                $('#form-update-password').on('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    if ($("#confirmPassword").val() != $("#newPassword").val()) {
                        modale(
                            "Avertissement",
                            "Les mots de passe que vous avez entrés ne correspondent pas",
                            "modal-warning"
                        )
                        $("#newPassword").css("border", "red solid 1px")
                        $("#confirmPassword").css("border", "red solid 1px")
                        $("#newPassword").on('focus', function() {
                            $(this).css('border', 'solid #69707a 1px')
                        })
                        $("#confirmPassword").on('focus', function() {
                            $(this).css('border', 'solid #69707a 1px')
                        })
                        return
                    }
                    $.ajax({
                        url: "../php/php_cl/Secretaires.php?do=mise_a_jour_mote_de_pass",
                        type: "POST",
                        data: formData,
                        success: function(result) {
                            if (result == "success") {
                                modale(
                                    "Success",
                                    "Votre mot de passe a été changé avec succès",
                                    "modal-success"
                                )
                                $("#currentPassword").val("")
                                $("#newPassword").val("")
                                $("#confirmPassword").val("")
                            } else if (result == "error_confirm_password") {
                                modale(
                                    "Avertissement",
                                    "Les mots de passe que vous avez entrés ne correspondent pas",
                                    "modal-warning"
                                )
                                $("#newPassword").css("border", "red solid 1px")
                                $("#confirmPassword").css("border", "red solid 1px")
                                $("#newPassword").on('focus', function() {
                                    $(this).css('border', 'solid #69707a 1px')
                                })
                                $("#confirmPassword").on('focus', function() {
                                    $(this).css('border', 'solid #69707a 1px')
                                })
                            } else if (result == "error_current_password") {
                                modale(
                                    "Erreur",
                                    "Le mot de passe actuel que vous avez entré est incorrect",
                                    "modal-danger"
                                )
                            } else {
                                modale(
                                    "Erreur",
                                    "Votre mot de passe n'a pas été mis à jour, veuillez réessayer plus tard",
                                    "modal-warning"
                                )
                            }
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    })
                })
            });
        </script>
    <?php
    }
    public static function demandes()
    {
    ?>
        <style>
            .demandes .nav-borders .nav-link.active {
                color: #0061f2;
                border-bottom-color: #0061f2;
            }

            .demandes .nav-borders .nav-link {
                color: #69707a;
                border-bottom-width: 0.125rem;
                border-bottom-style: solid;
                border-bottom-color: transparent;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
                padding-left: 0;
                padding-right: 0;
                margin-left: 1rem;
                margin-right: 1rem;
            }

            .btn-close-hover {
                opacity: .8;
            }

            .btn-close-hover:hover {
                opacity: 1;
            }

            .demande-short {
                cursor: pointer;
            }
        </style>
        <div class="demandes ">





            <!-- e show-employees-demandes -->
            <div id="show-employees-demandes" class="div-frame">
                <div>
                    <div id="demande-employee-table" class="table-responsive mt-4">
                        <table class="table table-hover bg-white">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Envoyeur</th>
                                    <th>Date</th>
                                    <th>Directeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once('../php_cl/Secretaires.php');
                                $result = CompteSecretaires::afficher_demandes_emps();
                                while ($demande = mysqli_fetch_assoc($result)) {
                                    echo "<tr class='demande-employee-short' id-demande='" . $demande['id'] . "'>";
                                    echo "<td>" . $demande['id'] . "</td>";
                                    echo "<td>" . $demande['titre'] . "</td>";
                                    echo "<td>" . $demande['envoyeur'] . "</td>";
                                    echo "<td>" . $demande['date'] . "</td>";
                                ?>
                                    <td>
                                        <select name="d" class="form-select w-auto force-not-work reponse_demande_select" id-demande="<?php echo $demande['id'] ?>">
                                            <option <?php if ($demande['dir_vu'] == 1) {
                                                        echo "selected";
                                                    } ?> value="1">Oui</option>
                                            <option <?php if ($demande['dir_vu'] == 0) {
                                                        echo "selected";
                                                    } ?> value="0">Non</option>
                                        </select>
                                    </td>
                                <?php
                                    echo "</tr>";
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="demande-employee-info" style="display: none;" class="position-relative">
                        <i class="bi bi-x-square-fill fs-1 c-pointer btn-close-hover text-danger" id="btn-close-employee-demande-info" style="position: absolute; right: 0; top:0"></i>
                        <div id="demande-employee-info-content">

                        </div>
                    </div>
                </div>
            </div>
            <!-- e show-employees-demandes -->
        </div>
        <script>
            function active_btn(elm) {
                $(".demandes .nav .nav-link").each(function() {
                    $(this).removeClass('active')
                })
                $(elm).addClass('active')
            }

            function active_div(elm) {
                $(".demandes .div-frame").each(function() {
                    $(this).hide()
                })
                $(elm).show()
            }
            $("#btn-add-demande").click(function() {
                active_btn($(this))
                active_div("#add-demande")
            })
            $("#btn-show-my-demandes").click(function() {
                active_btn($(this))
                active_div("#show-my-demandes")
            })
            $("#btn-show-employees-demandes").click(function() {
                active_btn($(this))
                active_div("#show-employees-demandes")
            })
            $('#btn-close-demande-info').click(function() {
                $('#demande-table').show()
                $('#demande-info').hide()
            })


            $('#btn-close-employee-demande-info').click(function() {
                $('#demande-employee-table').show()
                $('#demande-employee-info').hide()
            })
            $(".force-not-work").click((e) => {
                e.stopPropagation()
            })
            //--------form-demande-admin---------
            $('#form-demande-admin').on('submit', function(e) {
                e.preventDefault()
                let formData = new FormData(this)
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=envoyer_demande_admin",
                    type: "POST",
                    data: formData,
                    success: function(result) {
                        if (result == 1) {
                            modale(
                                "Success",
                                "La demande a été envoyée avec succès",
                                "modal-success"
                            )
                            $('.btn-demandes').click()
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                })
            })
            /* $('.demandes #show-employees-demandes .demande-employee-short').click(function() {
                $('#demande-employee-table').hide()
                $('#demande-employee-info').show()
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_info_demande",
                    type: "POST",
                    data: {
                        id_demande: $(this).attr('id-demande')
                    },
                    success: function(result) {
                        $('#demande-employee-info-content').html(result)
                    }
                })
            }) */
            //-------
            $('.demandes #show-my-demandes .demande-short').click(function() {
                $('#demande-table').hide()
                $('#demande-info').show()
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_info_ma_demande",
                    type: "POST",
                    data: {
                        id_demande: $(this).attr('id-demande')
                    },
                    success: function(result) {
                        $('#demande-info-content').html(result)
                    }
                })
            })
            $(".reponse_demande_select").on('change', function() {
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=mise_a_jour_reponse_demande",
                    type: "POST",
                    data: {
                        reponse: $(this).val(),
                        id_demande: $(this).attr('id-demande')
                    },
                    success: function(result) {
                        //alert(result)
                    }
                })
            })
        </script>
    <?php
    }
    public static function plaines()
    {
    ?>
        <style>
            .plaintes .nav-borders .nav-link.active {
                color: #0061f2;
                border-bottom-color: #0061f2;
            }

            .plaintes .nav-borders .nav-link {
                color: #69707a;
                border-bottom-width: 0.125rem;
                border-bottom-style: solid;
                border-bottom-color: transparent;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
                padding-left: 0;
                padding-right: 0;
                margin-left: 1rem;
                margin-right: 1rem;
            }

            .btn-close-hover {
                opacity: .8;
            }

            .btn-close-hover:hover {
                opacity: 1;
            }

            .plainte-short {
                cursor: pointer;
            }
        </style>
        <div class="plaintes">




            <!-- e show-employees-plaintes -->
            <div id="show-employees-plaintes" class="div-frame">
                <div>
                    <div id="plainte-employee-table" class="table-responsive mt-4">
                        <table class="table table-hover bg-white">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Envoyeur</th>
                                    <th>Date</th>
                                    <th>Directeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once('../php_cl/Secretaires.php');
                                $result = CompteSecretaires::afficher_plaintes_emps();
                                while ($plainte = mysqli_fetch_assoc($result)) {
                                    echo "<tr class='plainte-employee-short' id-plainte='" . $plainte['id'] . "'>";
                                    echo "<td>" . $plainte['id'] . "</td>";
                                    echo "<td>" . $plainte['titre'] . "</td>";
                                    echo "<td>" . $plainte['envoyeur'] . "</td>";
                                    echo "<td>" . $plainte['date'] . "</td>";
                                    echo "<td>";
                                ?>
                                    <select name="d" class="form-select w-auto force-not-work reponse_demande_select" id-demande="<?php echo $plainte['id'] ?>">
                                        <option <?php if ($plainte['dir_vu'] == 1) {
                                                    echo "selected";
                                                } ?> value="1">Oui</option>
                                        <option <?php if ($plainte['dir_vu'] == 0) {
                                                    echo "selected";
                                                } ?> value="0">Non</option>
                                    </select>
                                <?php
                                    echo "<td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="plainte-employee-info" style="display: none;" class="position-relative">
                        <i class="bi bi-x-square-fill fs-1 c-pointer btn-close-hover text-danger" id="btn-close-employee-plainte-info" style="position: absolute; right: 0; top:0"></i>
                        <div id="plainte-info-content">

                        </div>
                    </div>
                </div>
            </div>
            <!-- e show-employees-plaintes -->
        </div>
        <script>
            function active_btn_pl(elm) {
                $(".plaintes .nav .nav-link").each(function() {
                    $(this).removeClass('active')
                })
                $(elm).addClass('active')
            }

            function active_div_pl(elm) {
                $(".plaintes .div-frame").each(function() {
                    $(this).hide()
                })
                $(elm).show()
            }
            $("#btn-add-plainte").click(function() {
                active_btn_pl($(this))
                active_div_pl("#add-plainte")
            })
            $("#btn-show-my-plaintes").click(function() {
                active_btn_pl($(this))
                active_div_pl("#show-my-plaintes")
            })
            $("#btn-show-employees-plaintes").click(function() {
                active_btn_pl($(this))
                active_div_pl("#show-employees-plaintes")
            })

            $('#btn-close-plainte-info').click(function() {
                $('#plainte-table').show()
                $('#plainte-info').hide()
            })
            $(".force-not-work").click((e) => {
                e.stopPropagation()
            })
            /* $('.plaintes #show-employees-plaintes .plainte-employee-short').click(function() {
                $('#plainte-employee-table').hide()
                $('#plainte-employee-info').show()
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_info_plainte",
                    type: "POST",
                    data: {
                        id_plainte: $(this).attr('id-plainte')
                    },
                    success: function(result) {
                        $('#plainte-info-content').html(result)
                    }
                })
            }) */
            $('#btn-close-employee-plainte-info').click(function() {
                $('#plainte-employee-table').show()
                $('#plainte-employee-info').hide()
            })
            $('#form-plainte-admin').on('submit', function(e) {
                e.preventDefault()
                let formData = new FormData(this)
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=envoyer_plainte_admin",
                    type: "POST",
                    data: formData,
                    success: function(result) {
                        if (result == 1) {
                            modale(
                                "Success",
                                "La plainte a été envoyée avec succès",
                                "modal-success"
                            )
                            $('.btn-plaines').click()
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                })
            })

            //------------------ 
            $('.plaintes #show-my-plaintes .plainte-short').click(function() {
                $('#plainte-table').hide()
                $('#plainte-info').show()
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_info_ma_plainte",
                    type: "POST",
                    data: {
                        id_plainte: $(this).attr('id-plainte')
                    },
                    success: function(result) {
                        $('#plainte-ma-info-content').html(result)
                    }
                })
            })
            //------------------
            $(".reponse_demande_select").on('change', function() {
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=mise_a_jour_reponse_plainte",
                    type: "POST",
                    data: {
                        reponse: $(this).val(),
                        id_demande: $(this).attr('id-demande')
                    },
                    success: function(result) {
                        //alert(result)
                        
                    }
                })
            })
        </script>
    <?php
    }
    public static function employes()
    {
    ?>
        <div>
            <!-- employes -->
            <div class="div-employes pt-3">
                <button class="btn btn-primary btn-lg w-100" id="btn-add-employe">Ajouter un administrateur</button>
                <br>
                <br>
                <!-- s ajouter un employé -->
                <style>
                    #div-add-employe {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        min-height: 100%;
                        background-color: #00000080;
                    }
                </style>
                <div class="py-5" style="display:none" id="div-add-employe">
                    <article class="m-auto p-5 pt-2" id="article-add-employe">
                        <i class="bi bi-x-square-fill text-danger" id="btn-close-add-employe"></i>
                        <h4 class="card-title mt-3 text-center fw-bold">Ajouter un administrateur</h4>
                        <form id="form-ajouter-emp">
                            <div>
                                <br>
                                <div>
                                    <label for="prenom">Prénom: </label><br>
                                    <input type="text" required class="w-100" name="prenom" id="prenom">
                                </div>
                                <div>
                                    <label for="nom">Nom: </label><br>
                                    <input type="text" required class="w-100" name="nom" id="nom">
                                </div>
                                <div>
                                    <label for="role">Role: </label><br>

                                    <?php
                                    require_once('../php_cl/Secretaires.php');
                                    CompteSecretaires::get_services();
                                    ?>
                                </div>
                                <hr>
                                <div>
                                    <label for="email">Email: </label><br>
                                    <input type="email" required class="w-100" name="email" id="email">
                                </div>
                                <div>
                                    <label for="password">Mote de pass: </label><br>
                                    <input type="text" required class="w-100" name="password" id="password">
                                </div>
                                <br>
                                <button class="btn btn-success w-100">Ajouter</button>
                            </div>
                        </form>
                </div>
            </div>
            <!-- e ajouter un employé -->
            <div id="div-info-employe" style="display:none">
                <div class="table-responsive bg-white p-2" style="position: relative;" id="div-content-info-emp">
                    <i class="bi bi-arrow-left-square-fill text-secondary" id="btn-close-info-employe"></i>
                    <div id="div-info-employe-content">

                    </div>
                </div>
            </div>
            <div style="width: 100%; height: 100%; background-color: #00000080;">
            </div>
            <div class="d-flex" role="search">
                <input class="form-control rounded-0" id="input-filtre" type="search" placeholder="ID, Nom, Prenom..." aria-label="Search">
                <button class="btn btn-search rounded-0 text-white fw-bold" id="btn-filtre" type="button">Filtre</button>
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-hover table-employes bg-white">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Rôle</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody style="max-height: 600px !important; overflow-y: auto !important; " id="liste-employes">
                        <?php
                        require_once('../php_cl/Secretaires.php');
                        $result = CompteSecretaires::afficher_les_employes();
                        while ($data = mysqli_fetch_assoc($result)) {
                            echo "<tr id-employe='" . $data['id_admin'] . "'>";
                            echo "<td>" . $data['id_admin'] . "</td>";
                            echo "<td>" . $data['nom_admin'] . "</td>";
                            echo "<td>" . $data['prenom'] . "</td>";
                            echo "<td>" . $data['nom_service'] . "</td>";
                            echo "<td>";

                            @include('../fix/fix_date.php');
                            if (strtotime($data["derniere_vu"]) + 3 > time()) {
                                echo '<span class="text-success">En ligne</span>';
                            } else {
                                echo '<span class="text-danger">Déconnecté</span>';
                            }
                            echo "</td>";

                            echo "<tr/>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script src="../scripts/employes.js"></script>
        <script>
            $('#btn-filtre').click(filter_table)
            $('#input-filtre').keydown((e) => {
                if (e.keyCode == 13) {
                    filter_table()
                }
            })

            function filter_table() {
                const value = $('#input-filtre').val().trim().toLowerCase();
                if (value == "") {
                    $('.table-employes tbody tr').each(function() {
                        $(this).show()
                        return true
                    })
                }
                $('.table-employes tbody tr').each(function() {
                    var exist = false
                    $(this).children().each(function() {
                        if (!$(this).hasClass('td-status')) {
                            if ($(this).text().toLowerCase().includes(value)) {
                                let new_val = $(this).text().substr($(this).text().toLowerCase().indexOf(value), value.length)
                                exist = true
                                $(this).html($(this).text().replace(new_val, "<span class='bg-warning'>" + new_val + "</span>"))
                            } else {
                                $(this).html($(this).text())
                            }
                        }
                    })
                    if (!$(this).hasClass('td-status')) {
                        if (!exist) {
                            $(this).hide()
                        } else {
                            $(this).show()
                        }
                    }
                })
            }

            $('#btn-add-employe').click(() => {
                $('#div-add-employe').fadeIn()
                $('#div-add-employe').click(() => {
                    $('#div-add-employe').fadeOut(100)
                })
                $('#article-add-employe').click((e) => {
                    e.stopPropagation()
                })
            })

            $("#btn-close-info-employe").click(() => {
                $("#div-info-employe").hide()
            })
            $("#div-info-employe").click(() => {
                $("#div-info-employe").hide()
            })
            $("#div-content-info-emp").click((e) => {
                e.stopPropagation()
            })
            $('.table-employes tbody tr').click(function() {
                $("#div-info-employe").show()
                //console.log($(this).eq(0).attr('user-id'))
                //
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=afficher_info_employe",
                    type: "POST",
                    data: {
                        id_employe: $(this).eq(0).attr('id-employe')
                    },
                    success: function(result) {
                        $('#div-info-employe-content').html(result)
                    }
                })
            })
            $('#btn-close-add-employe').click(() => {
                $('#div-add-employe').hide()
            })
            $('#form-ajouter-emp').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "../php/php_cl/Secretaires.php?do=ajouter_emp",
                    type: "POST",
                    data: formData,
                    success: function(result) {
                        console.log(result)
                        if (result == "error_email_exists") {
                            return modale(
                                "Avertissement",
                                "Cet email il existe déjà, utiliser une autre adresse e-mail",
                                "modal-warning"
                            )
                        } else if (result == 1) {
                            modale(
                                "Success",
                                "Le nouvel employé a été ajouté avec succès",
                                "modal-success"
                            )
                        }
                        $('.btn-employes').click();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                })
            })
        </script>

<?php
    }
}
