<style>
    .header-logged {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        height: max-content;
    }

    .button {
        background-color: lightslategray;
        border-radius: 15px;
        padding: 8px;
    }
    a {
        all: unset;
        cursor: pointer;
    }
</style>

<div>
    <?php if (!empty($_SESSION['client'])) : ?>
        <div class="header-logged">
            <div>
                Bienvenue <?= $_SESSION['client']['PRENOM_MEM'] ?> <?= $_SESSION['client']['NOM_MEM'] ?>
            </div>
            <div class="button">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    <?php else : ?>
        <div><a href="logout.php">Logout</a></div>
    <?php endif; ?>
</div>
