<?php

require "utils.php";

loginPage();

function getSelectedProject($firstSelect)
{
    return (empty($_POST['projet']) ? $firstSelect : $_POST['projet']);
}

function isSearching()
{
    return isset($_SESSION['search']) && $_SESSION['search'] == true;
}

$client = $_SESSION["client"];
$first = 0;
if (isSearching()) {
    $sqlArray = $_SESSION['searching'];
}

if (isset($_POST['archive'])) {
    $date = $_POST['DATE_ARCHIVE'];
    $query = "exec TP3_SP_ARCHIVER_PROJET(to_date('$date', 'yyyy-mm-dd'), " . $client['NO_MEMBRE'] . ")";

    $stid = oci_parse($conn, $query);
    oci_execute($stid);
}

if (isset($_POST['tous'])) {
    $_SESSION['search'] = false;
    header("Location: http://localhost/tp3/liste_projets.php");
    die();
}

if (isset($_POST['update'])) {
    echo "searching for page : " . getSelectedProject($first);
}
?>

<style>
    .container {
        display: flex;
        flex-direction: column;
    }

    form {
        display: flex;
        flex-direction: column;
        max-width: 10rem;
    }

    .form {
        display: flex;
        flex-direction: row;
    }

    .button-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        margin: 0.2rem;
        padding: 0 0.2rem;
        background-color: lightslategray;
        border-radius: 10px;
        height: 2rem;
    }

    .button-row>* {
        padding: 0 0.8rem;
    }

    .button-row>*:hover {
        color: lightyellow;
    }

    .projet-res {
        width: 100%;
    }

    .archive-button {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0.5rem 0;
    }
</style>

<!DOCTYPE html>
<html>

<head>
    <title>Liste projets</title>
</head>

<body>
    <?php require_once "header.php"; ?>
    <div class="container">
        <div>
            <div class="button-row">
                <a href="http://localhost/tp3/un_projet.php?">Créer</a>
                <?php
                echo "<a href='http://localhost/tp3/un_projet.php?no_projet='" . getSelectedProject($first) . ">Mettre à jour</a>";
                ?>
                <a href="http://localhost/tp3/projet_rechercher.php">Rechercher</a>
            </div>
            <?php if (isSearching()) : ?>
                <form method="post">
                    <input type="submit" name="tous" value="Tous">
                </form>
            <?php endif ?>
        </div>



        <div class="form">
            <form method="post" action="liste_projets.php">
                <select size="20" name="projet">
                    <?php
                    $subquery = "(select NO_PROJET from TP3_EQUIPE_PROJET where NO_MEMBRE = " . $client['NO_MEMBRE'];

                    if (isSearching()) {
                        $subquery = $subquery . " and NO_PROJET in " . $sqlArray;
                    }
                    $subquery = $subquery . ")";
                    $query = "SELECT * FROM TP3_PROJET where NO_PROJET in $subquery order by DATE_DEBUT_PRO desc";

                    $stid = oci_parse($conn, $query);
                    echo $query;
                    oci_execute($stid);
                    ?>
                    qd
                    <?php while (($projet = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS))) : ?>
                        <?php
                        if (!isset($first)) {
                            $select = "selected='selected'";
                            $first = $projet['NO_PROJET'];
                        } else {
                            $select = "";
                        }
                        echo "<option " . $select . " value='" . $projet['NO_PROJET'] . "'>" . $projet['NOM_PRO'] . "</option>";
                        ?>
                    <?php endwhile; ?>


                    <!-- DISPLAY ARCHIVE PROJECTS -->
                    <?php if (isAdminOrSupervisor()) : ?>
                        <?php
                        $query = "select * from TP3_PROJET_ARCHIVE";

                        if (isSearching()) {
                            $query = $query . " where NO_PROJET in " . $sqlArray;
                        }
                        $query = $query . " order by DATE_DEBUT_PRO desc";
                        $stid = oci_parse($conn, $query);
                        echo $query;
                        oci_execute($stid);
                        ?>
                        <?php while (($archive = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS))) : ?>
                            <?php
                            if (!isset($first)) {
                                $select = "selected='selected'";
                                $first = $archive['NO_PROJET'];
                            } else {
                                $select = "";
                            }
                            echo "<option " . $select . " value='" . $archive['NO_PROJET'] . "'>" . $archive['NOM_PRO'] . "</option>";
                            ?>
                        <?php endwhile; ?>

                    <?php endif; ?>
                </select>
                <input type="submit" value="Chercher" />
                <?php if (isAdminOrSupervisor()) : ?>
                    <div class="archive-button">
                        <input type="date" name="DATE_ARCHIVE">
                        <input type="submit" name="archive" value="Archiver">
                    </div>
                <?php endif ?>
            </form>
            <div class="projet-res">
                <?php
                $query = "SELECT * FROM (
                select * from TP3_PROJET
                    union all
                select * from TP3_PROJET_ARCHIVE
            ) where NO_PROJET = " . getSelectedProject($first) . " fetch first 1 rows only";
                $stid = oci_parse($conn, $query);
                oci_execute($stid);

                if (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                    echo $row['NO_PROJET']
                        . " "
                        . $row['NOM_PRO']
                        . " "
                        . $row['MNT_ALLOUE_PRO']
                        . " "
                        . $row['STATUT_PRO']
                        . " "
                        . $row['DATE_DEBUT_PRO'];
                }
                ?>
            </div>
        </div>
    </div>
    <?php require_once "footer.php"; ?>
</body>

</html>
