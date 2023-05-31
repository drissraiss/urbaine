<?php
session_start();
require('partials/check_connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <!-- bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/profile.css">
    <title>CHATFAMS</title>
</head>

<body>
    <div style="min-height:100vh">
        <div class="" style="background-color: #cfcfcf; " id="div-header">
        <?php include('partials/header.php') ?>
        </div>
        <div class="row container-fluid p-0 m-0" style="min-height: calc(100vh - 70px)">
            <!--   -->
            <nav class="p-0 d-none d-sm-inline col-auto" id="nav">
                <div id="menu" class="mt-4">
                    <ul class="list-unstyled">
                        <li class="m-4 fw-bold btn-notifications active-now"><i data-toggle="tooltip"
                                title="Notifications" class="bi bi-bell-fill "></i><span
                                class="ms-3 d-none d-xl-inline">Notifications</span>
                        </li>
                        <li class="m-4 fw-bold btn-messages"><i data-toggle="tooltip" title="Messages"
                                class="bi bi-chat-left-text-fill"></i><span
                                class="ms-3 d-none d-xl-inline">Messages</span>
                        </li>
                        <li class="m-4 fw-bold btn-profile"><i data-toggle="tooltip" title="Profile"
                                class="bi bi-person-fill"></i><span class="ms-3 d-none d-xl-inline">Profile</span></li>
                        <li class="m-4 fw-bold btn-demandes"><i data-toggle="tooltip" title="Demandes"
                                class="bi bi-journal-text"></i><span class="ms-3 d-none d-xl-inline">Demandes</span>
                        </li>
                        <li class="m-4 fw-bold btn-plaines"><i data-toggle="tooltip" title="Plaintes"
                                class="bi bi-emoji-angry-fill"></i><span class="ms-3 d-none d-xl-inline">Plaintes</span>
                        </li>
                        <li class="m-4 fw-bold btn-employes"><i data-toggle="tooltip" title="Employés"
                                class="bi bi-person-gear"></i><span class="ms-3 d-none d-xl-inline">Employés</span></li>
                        </li>
                        <li class="m-4 fw-bold btn-deconnecter">
                            <a href="../php/logout.php" class="log-out text-danger text-decoration-none">
                                <i data-toggle="tooltip" title="Déconnecter" class="bi bi-box-arrow-right"></i><span
                                    class="ms-3 d-none d-xl-inline">Déconnecter</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <section class="col position-relative p-3"
                style="height: calc(100vh - 70px) !important ; overflow-y: auto; background-color: #f2f6fc; min-width: 430px;">
                <!-- Modale -->
                <style>
                    #myModal {
                        display: none;
                        position: fixed;
                        z-index: 1;
                        padding-top: 100px;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgba(0, 0, 0, 0.4);
                    }

                    .modal-warning .modal-header {
                        color: #9a6100;
                        background-color: #ffedb7;
                    }

                    .modal-warning .modal-body {
                        color: #603d00;
                    }

                    .modal-warning #close:hover {
                        color: #9a6100;
                    }
                </style>
                <div id="myModal" class="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 id="modal-header"></h2>
                            <span id="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p id="modal-body"></p>
                        </div>
                    </div>
                </div>
                <!-- end modale -->
                <div class="fs-5" id="section-content">
                </div>
        </div>
        </section>
    </div>

    <!-- s nav for phone-->
    <!-- <div style="height: 62px; border-top: red solid 3px;" > -->
    <div>
        <ul class="list-unstyled d-flex row nav-phone d-sm-none w-100 m-0 p-0">
            <li class="d-inline text-center py-3 col btn-notifications active-now">
                <i data-toggle="tooltip" title="Notifications" class="bi bi-bell-fill "></i>
            </li>
            <li class="d-inline text-center py-3 col btn-messages">
                <i data-toggle="tooltip" title="Messages" class="bi bi-chat-left-text-fill"></i>
            </li>
            <li class="d-inline text-center py-3 col btn-profile">
                <i data-toggle="tooltip" title="Profile" class="bi bi-person-fill"></i>
            </li>
            <li class="d-inline text-center py-3 col btn-demandes">
                <i data-toggle="tooltip" title="Demandes" class="bi bi-journal-text"></i>
            </li>
            <li class="d-inline text-center py-3 col btn-plaines">
                <i data-toggle="tooltip" title="Plaines" class="bi bi-emoji-angry-fill"></i>
            </li>
            <li class="d-inline text-center py-3 col btn-employes">
                <i data-toggle="tooltip" title="Employés" class="bi bi-person-gear"></i>
            </li>
            <li class="d-inline text-center py-3 col text-danger btn-deconnecter">
                <a href="../php/logout.php" class="log-out text-danger text-decoration-none">
                    <i data-toggle="tooltip" title="Déconnecter" class="bi bi-box-arrow-right"></i>
                </a>
            </li>
        </ul>
    </div>
    <!-- e for phone-->
    </div>
    <script src="../scripts/script.js"></script>
    <script src="../scripts/home.js"></script>
    <script src="../scripts/admin.js"></script>
    <script>
        setInterval(() => {
            $.ajax({
                url:"../php/php_cl/Administrateurs.php?do=mise_a_jour_status",
                success: function (result) {
                    //console.log("s = ", result)
                }
            })
        }, 1000);
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>