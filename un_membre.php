<?php

require "utils.php";

loginPage();

if (!isset($_GET['no_membre'])) {
    echo "Missing 'no_membre' query !";
    return;
}
$noMem = $_GET['no_membre'];

$query = "select NO_MEMBRE, UTILISATEUR_MEM, NOM_MEM, PRENOM_MEM, ADRESSE_MEM, LANGUE_CORRESPONDANCE_MEM, NOM_FICHIER_PHOTO_MEM from TP3_MEMBRE where NO_MEMBRE = $noMem fetch first 1 rows only";

$stid = oci_parse($conn, $query);
oci_execute($stid);

while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS))) {
    $membre = $row;
}
?>

<style>
    .container {
        display: flex;
        flex-direction: column;
    }
</style>

<html>

<head>
    <title>Membre</title>
</head>

<body>
    <?php require 'header.php' ?>
    <div class="container">
        <div>
            Numéros: <b><?= $membre['NO_MEMBRE'] ?></b>
        </div>
        <div>
            Nom utilisateur: <b><?= $membre['UTILISATEUR_MEM'] ?></b>
        </div>
        <div>
            Nom: <b><?= $membre['NOM_MEM'] ?></b>
        </div>
        <div>
            Prénom: <b><?= $membre['PRENOM_MEM'] ?></b>
        </div>
        <div>
            Adresse: <b><?= $membre['ADRESSE_MEM'] ?></b>
        </div>
        <div>
            Langue: <b><?= $membre['LANGUE_CORRESPONDANCE_MEM'] ?></b>
        </div>
        <div>
            <img src="<?= $membre['NOM_FICHIER_PHOTO_MEM'] ?>">
        </div>
    </div>
    <?php require 'footer.php' ?>
</body>

</html>
