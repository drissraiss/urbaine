<?php
if (!isset($_SESSION["connect"])) {
    header('location:../');
    exit();
}
