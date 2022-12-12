<style>
    form {
        max-width: 10rem;
        padding: 2rem;
    }

    form>div {
        display: flex;
        flex-direction: column;
        width: fit-content;
    }

    form>div:last-child {
        flex-direction: row;
        justify-content: space-between;
        padding: 0.3rem 0;
    }

    form>div:last-child>input {
        margin: 0 0.5rem;
    }
</style>

<?php

require "utils.php";

loginPage();

// TODO RAJOUTER SI CANCEL

if (isset($_POST["cancel"])) {
    header("Location: http://localhost/tp3/liste_projets.php");
    die;
}
if (isset($_POST["search"])) {
    $nom = $_POST['NOM_PRO'];
    $mnt = $_POST['MNT_ALLOUE_PRO'];
    $date = $_POST['DATE_DEBUT_PRO'];

    $where = "where ";

    if (!empty($nom)) {
        $whereNom = "NOM_PRO like '%$nom%'";
        $where = $where . $whereNom;
    }
    if (!empty($mnt)) {
        $whereMnt = "MNT_ALLOUE_PRO = $mnt";
        if (strlen($where) > 7) {
            $where = $where . " and ";
        }
        $where = $where . $whereMnt;
    }
    if (!empty($date)) {
        if (strlen($where) > 7) {
            $where = $where . " and ";
        }
        $where = $where . "DATE_DEBUT_PRO = to_date('$date', 'yyyy-mm-dd')";
    }

    $client = $_SESSION["client"];
    if (isAdminOrSupervisor()) {
        $table = "(select * from TP3_PROJET UNION select * from TP3_PROJET_ARCHIVE)";
    } else {
        $table = "TP3_PROJET";
    }
    $request = "select NO_PROJET from $table $where";
    echo $request;
    $stid = oci_parse($conn, $request);

    oci_execute($stid);
    $res = array();

    $nrows = oci_fetch_all($stid, $res);
    $sqlArray = '(' . join(',', $res['NO_PROJET']) . ')';
    $_SESSION['search'] = true;
    $_SESSION['searching'] = $sqlArray;
    header("Location: http://localhost/tp3/liste_projets.php");
    die;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Recherche</title>
</head>

<body>
    <?php require 'header.php' ?>
    <div>
        <form method="post">
            <div>
                <label type="text">Nom projet</label>
                <input type="text" name="NOM_PRO">
            </div>
            <div>
                <label type="text">Montant</label>
                <input type="number" step=".01" name="MNT_ALLOUE_PRO">
            </div>
            <div>
                <label type="text">Date début</label>
                <input type="date" name="DATE_DEBUT_PRO">
            </div>
            <div>
                <input type="submit" name="cancel" value="cancel">
                <input type="submit" name="search" value="ok">
            </div>
        </form>
    </div>
    <?php require 'footer.php' ?>
</body>

</html>
