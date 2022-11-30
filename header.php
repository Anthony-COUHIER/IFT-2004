<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    header {
        display: flex;
        flex-direction: row;
        width: 100vw;
        height: 2.5rem;
        padding: 0.5rem;
        background-color: #ccc;
        padding: 0.5rem;
    }

    .header-logged {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }
</style>

<header>
    <?php if (!empty($_SESSION['client'])) : ?>
        <div class="header-logged">
            <div>
                Bienvenue <?= $_SESSION['client']['PRENOM_MEM'] ?> <?= $_SESSION['client']['NOM_MEM'] ?>
            </div>
            <div>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    <?php else : ?>
        <div><a href="logout.php">Logout</a></div>
    <?php endif; ?>
</header>
