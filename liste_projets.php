<?php

require "utils.php";

loginPage();
$client = $_SESSION["client"];
if (isset($_GET['no_projects'])) {
    $sqlArray = '(' . join(',', $_GET['no_projects']) . ')';
}
?>

<style>
    .container {
        display: flex;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    form input {
        width: 100%;
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
        <form method="post" action="liste_projets.php">
            <select size="20" name="projet">
                <?php
                $subquery = "(select NO_PROJET from TP3_EQUIPE_PROJET where NO_MEMBRE = " . $client['NO_MEMBRE'];

                if (isset($_GET['no_projects'])) {
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

                    if (isset($_GET['no_projects'])) {
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
        </form>
        <div>
            <?php
            $query = "SELECT * FROM (
                select * from TP3_PROJET
                    union all
                select * from TP3_PROJET_ARCHIVE
            ) where NO_PROJET = " . (empty($_POST['projet']) ? $first : $_POST['projet']) . " fetch first 1 rows only";
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
    <?php require_once "footer.php"; ?>
</body>

</html>
