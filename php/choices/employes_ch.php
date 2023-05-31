<?php

require_once("../classes/employes_cl.php");

$choice = $_GET["choice"];
switch ($choice) {
    case "notifications":
        Employe::notifications();
        break;
    case "messages":
        Employe::messages();
        break;
    case "profile":
        Employe::profile();
        break;
    case "demandes":
        Employe::demandes();
        break;
    case "plaines":
        Employe::plaines();
        break;
    default:
        Employe::notifications();
}
