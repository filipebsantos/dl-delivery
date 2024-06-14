<?php
require_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/UserDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["id"])) {
    $userData = new UserDAO($dbConn);

    $loadedData = $userData->getUser($_GET["id"]);
    if (!$loadedData) {
        header("Location: " . $BASE_URL . "user.php");
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <?php if ($_SESSION["activeUser"]["role"] >= 3) : ?>
            <div id="page-content" class="col-auto col-md-7">
                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container">
                        <div class="container mt-3" id="alert-box">
                            <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                                <?= $returnMessage["msg"] ?>
                            </div>
                        </div>
                        <a href="<?= $BASE_URL ?>user.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-caret-left-fill"></i>
                            Voltar
                        </a>
                    </div>
                <?php else : ?>
                    <div class="container text-center mt-4">
                        <h4 class="mb-4">Tem certeza que deseja exluir '<?= $loadedData["username"] ?>'?</h4>
                        <form action="<?= $BASE_URL ?>userprocess.php" method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="userId" value="<?= $loadedData["id"] ?>">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Sim</button>
                            <a href="<?= $BASE_URL ?>user.php" class="btn btn-danger"><i class="bi bi-x-lg"></i> Não</a>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <?php include_once(__DIR__ . "/includes/access-error.php"); ?>
        <?php endif; ?>
    </div>
</div>
<?php include("includes/footer.php"); ?>