<?php

require "init.php";

function clientIsNotConnected() {
    return empty($_SESSION["client"]);
}

function loginPage() {
    if (clientIsNotConnected()) {
        echo 'is not working';
        // header("Location: http://localhost/tp3/index.php");
        // die;
    }
}
