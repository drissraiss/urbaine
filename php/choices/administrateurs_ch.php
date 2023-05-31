<?php

require_once("../classes/administrateurs_cl.php");

$choice = $_GET["choice"];
switch ($choice) {
    case "notifications":
        Administrateur::notifications();
        break;
    case "messages":
        Administrateur::messages();
        break;
    case "profile":
        Administrateur::profile();
        break;
    case "demandes":
        Administrateur::demandes();
        break;
    case "plaines":
        Administrateur::plaines();
        break;
    case "employes":
        Administrateur::employes();
        break;
    default:
        Administrateur::notifications();
}
