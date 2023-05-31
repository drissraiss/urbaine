<?php

require_once("../classes/directeurs_cl.php");

$choice = $_GET["choice"];
switch ($choice) {
    case "notifications":
        Directeur::notifications();
        break;
    case "messages":
        Directeur::messages();
        break;
    case "profile":
        Directeur::profile();
        break;
    case "demandes":
        Directeur::demandes();
        break;
    case "plaines":
        Directeur::plaines();
        break;
    case "employes":
        Directeur::employes();
        break;
    case "Directeurs":
        //Directeur::administrateurs();
        break;
    default:
        Directeur::notifications();
}