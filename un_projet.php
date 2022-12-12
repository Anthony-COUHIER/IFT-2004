<?php

require "utils.php";

loginPage();


function hasDataFromProject($index)
{
    global $detailProject;
    return (empty($detailProject[$index]) ? "" : $detailProject[$index]);
}

function findStatusEtatByCode($code)
{
    global $status;

    foreach ($status as &$s) {
        if ($code == $s['CODE_ETAT_RAP']) {
            return $s['NOM_ETAT_RAP'];
        }
    }
}

$modifMode = isset($_GET['no_projet']);

if ($modifMode) {
    $projetNumber = $_GET['no_projet'];
    $query = "select * from TP3_PROJET where $projetNumber = NO_PROJET";
    $stid = oci_parse($conn, $query);

    oci_execute($stid);

    if (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        $detailProject = $row;
    }
} else {
    $query = "select max(NO_PROJET) as max from TP3_PROJET";
    $stid = oci_parse($conn, $query);

    oci_execute($stid);

    if (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        $projetNumber = $row['MAX'] + 1;
    }
    $detailProject = array();
}

if ($modifMode) {
    $statusQuery = "select * from TP3_RAPPORT_ETAT";

    $stid = oci_parse($conn, $statusQuery);
    oci_execute($stid);
    $status = array();
    $nrows = oci_fetch_all($stid, $status, null, null, OCI_FETCHSTATEMENT_BY_ROW);
}
?>

<style>
    div>form {
        display: flex;
        flex-direction: column;
        width: fit-content;
        gap: 0.5rem;
    }

    .rapport-search,
    .equipe-search {
        display: flex;
        flex-direction: row;
    }

    .rapport-search-form,
    .equipe-search-form {
        display: flex;
        flex-direction: column;
    }

    .rapport-search-res,
    .membre-search-res {
        display: flex;
        align-items: center;
        padding-left: 1rem;
    }

    select {
        width: min-content;
    }

    .membre-search-res > a {
        background-color: lightgray;
        padding: 0.2rem;
        border-radius: 5px;
    }
    .membre-search-res > a:hover {
        cursor: pointer;
    }
</style>

<html>

<head>
    <title>Projet - <?= $projetNumber ?></title>
</head>

<body>
    <?php require 'header.php' ?>
    <div>
        <form method="post">
            <div>
                <label for="NO_PROJET">N° projet</label>
                <input readonly type="text" id="NO_PROJET" value="<?= $projetNumber ?>">
            </div>

            <div>
                <label for="NOM_PRO">Nom du projet</label>
                <input type="text" id="NOM_PRO" name="NOM_PRO" value="<?= hasDataFromProject('NOM_PRO') ?>">
            </div>

            <div>
                <?php if (isAdminOrSupervisor() || $modifMode) : ?>
                    <label for="MNT_ALLOUE_PRO">Montant alloué</label>
                <?php endif ?>
                <?php if (isAdminOrSupervisor()) : ?>
                    <input type="text" name="MNT_ALLOUE_PRO" value="<?= hasDataFromProject('MNT_ALLOUE_PRO') ?>">
                <?php elseif (!isAdminOrSupervisor() && $modifMode) : ?>
                    <input type="text" name="MNT_ALLOUE_PRO" value="<?= hasDataFromProject('MNT_ALLOUE_PRO') ?>">
                <?php endif ?>
            </div>

            <div>
                <label for="DATE_DEBUT_PRO">Date début</label>
                <input type="text" name="DATE_DEBUT_PRO" value="<?= hasDataFromProject('DATE_DEBUT_PRO') ?>">
            </div>
            <div>
                <label for="DATE_DEBUT_PRO">Date fin</label>
                <input type="text" name="DATE_DEBUT_PRO" value="<?= hasDataFromProject('DATE_DEBUT_PRO') ?>">
            </div>

            <div>
                <label for="STATUT_PRO">Status</label>
                <select name="STATUT_PRO">
                    <option value="Accepté">Accepté</option>
                    <option value="Préliminaire">Préliminaire</option>
                    <option value="Intermédiaire">Intermédiaire</option>
                    <option value="Final">Final</option>
                    <option value="Terminé">Terminé</option>
                </select>
            </div>

            <?php if ($modifMode) : ?>
                <div class="rapport-search">
                    <div class="rapport-search-form">
                        <label for="RAPPORT">Rapport</label>
                        <select name="RAPPORT" id="RAPPORT" size=5>
                            <?php
                            $query = "SELECT NO_RAPPORT, TITRE_RAP FROM (select * from TP3_RAPPORT union all select * from TP3_RAPPORT_ARCHIVE) where NO_PROJET = $projetNumber";
                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                                echo "<option value='";
                                echo $row['NO_RAPPORT'];
                                echo "'>";
                                echo $row['TITRE_RAP'];
                                echo "</option>";
                            }
                            ?>
                        </select>
                        <input type="submit" name="search_rapport" value="search rapport">
                    </div>

                    <?php if (!empty($_POST['RAPPORT']) || !empty($_SESSION['rapportNumber'])) : ?>
                        <div class="rapport-search-res">
                            <?php
                            if (!empty($_POST['RAPPORT']))
                                $_SESSION['rapportNumber'] = $_POST['RAPPORT'];
                            $rapportNumber = $_SESSION['rapportNumber'];
                            $query = "SELECT TITRE_RAP, CODE_ETAT_RAP FROM (select * from TP3_RAPPORT union all select * from TP3_RAPPORT_ARCHIVE) where NO_PROJET = $projetNumber and NO_RAPPORT = $rapportNumber fetch first 1 rows only";

                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            if (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                                echo '<b>';
                                echo findStatusEtatByCode($row['CODE_ETAT_RAP']);
                                echo '</b>';
                                echo '  - ';
                                echo $row['TITRE_RAP'];
                            }
                            ?>
                        </div>
                    <?php endif ?>
                </div>

                <div class="equipe-search">
                    <div class="equipe-search-form">
                        <label for="EQUIPE_PROJET">Equipe</label>
                        <select name="EQUIPE_PROJET" id="EQUIPE_PROJET" size=10>
                            <?php
                            $query = "select NO_MEMBRE, NOM_MEM, PRENOM_MEM from TP3_MEMBRE where NO_MEMBRE in (select NO_MEMBRE from TP3_EQUIPE_PROJET where NO_PROJET = $projetNumber)";
                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                                echo "<option value='";
                                echo $row['NO_MEMBRE'];
                                echo "'>";
                                echo $row['NOM_MEM'];
                                echo " ";
                                echo $row['PRENOM_MEM'];
                                echo "</option>";
                            }
                            ?>
                        </select>
                        <input type="submit" name="search_membre" value="search membre">
                    </div>

                    <?php if (!empty($_POST['EQUIPE_PROJET']) || !empty($_SESSION['membreNumber'])) : ?>
                        <div class="membre-search-res">
                            <?php
                            if (!empty($_POST['EQUIPE_PROJET']))
                                $_SESSION['membreNumber'] = $_POST['EQUIPE_PROJET'];
                            $membreNumber = $_SESSION['membreNumber'];
                            $query = "select NOM_MEM, PRENOM_MEM, COURRIEL_MEM from TP3_MEMBRE where NO_MEMBRE in (select NO_MEMBRE from TP3_EQUIPE_PROJET where NO_PROJET = $projetNumber and NO_MEMBRE = $membreNumber)";

                            $stid = oci_parse($conn, $query);
                            oci_execute($stid);

                            if (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                                echo "<a href='http://localhost/tp3/un_membre.php?no_membre=$membreNumber'>";
                                echo $row['NOM_MEM'];
                                echo " - ";
                                echo $row['PRENOM_MEM'];
                                echo " - ";
                                echo $row['COURRIEL_MEM'];
                                echo "</a>";
                            }
                            ?>
                        </div>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </form>
    </div>
    <?php require 'footer.php' ?>
</body>

</html>
