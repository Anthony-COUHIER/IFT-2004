<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    header {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        width: 100vw;
        height: fit-content;
        padding: 0.8rem;
        background-color: lightgray;
    }
</style>

<header>
    <div>
        CRIPÉ
    </div>
    <div>
        <?php require "etat_connexion.php" ?>
    </div>
</header>
