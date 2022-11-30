<?php

require "utils.php";

// Check if a user is logged
if (isset($_POST["login"]) and !isset($_SESSION['user'])) {
    $username = $_POST['UTILISATEUR_MEM'];
    $password = $_POST['MOT_DE_PASSE_MEM'];

    if (empty($username) or empty($password)) {
        echo "Please do not let empty username and password.";
    } else {
        $username = addslashes($username);
        $password = addslashes($password);

        $where = "where UTILISATEUR_MEM = '$username' and MOT_DE_PASSE_MEM = '$password'";
        $request = 'select * from TP3_MEMBRE ' . $where . ' fetch first 1 rows only';

        $stid = oci_parse($conn, $request);

        oci_execute($stid);
        echo $request;
        if (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
            $_SESSION["client"] = $row;
        }

        header("Location: http://localhost/tp3/liste_projets.php");
        die;
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
	</head>

	<body>
        <div>
            <form method="post">
                <label type="text">Username</label> <br>
                <input type="text" name="UTILISATEUR_MEM" placeholder="Username">
                <br>
                <label type="text">Password</label><br>
                <input type="password" name="MOT_DE_PASSE_MEM" placeholder="Password">

                <br>
                <input type="submit" name="login" value="login">
            </form>
        </div>
        <?php require_once "footer.php"; ?>
	</body>
</html>
