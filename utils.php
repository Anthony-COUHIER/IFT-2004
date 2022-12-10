<?php

require "init.php";

function clientIsNotConnected()
{
    return empty($_SESSION["client"]);
}

function loginPage()
{
    if (clientIsNotConnected()) {
        header("Location: http://localhost/tp3/index.php");
        die;
    }
}

function isAdminOrSupervisor()
{
    return $_SESSION["client"]['EST_ADMINISTRATEUR_MEM'] or $_SESSION["client"]['EST_SUPERVISEUR_MEM'];
}
