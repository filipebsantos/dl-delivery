<?php
require_once("includes/header.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <?php if ($_SESSION["activeUser"]["role"] >= 2) : ?>
            <div id="page-content" class="col-auto col-md-7">
                <div class="container mt-5">
                    <?php if (!empty($returnMessage["msg"])) : ?>
                        <div class="container mt-3" id="alert-box">
                            <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                                <?= $returnMessage["msg"] ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="container">
                        <div class="row">
                            <div class="col-10">
                                <h3 class="mb-4">Cadastrar novo cliente</h3>
                            </div>
                            <div class="col align-self-center">
                                <a href="<?= $BASE_URL ?>client.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-caret-left-fill"></i>
                                    Voltar
                                </a>
                            </div>
                        </div>

                        <form action="<?= $BASE_URL ?>clientprocess.php" method="POST">
                            <input type="hidden" name="action" value="create">

                            <div class="row">
                                <div class="col-sm-12 col-md-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="txtClientId">ID</span>
                                        <input type="text" name="txtClientId" class="form-control" placeholder="ID do cliente" aria-label="ID do cliente" aria-describedby="txtClientId" required>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-9">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="txtClientName">Nome do Cliente</span>
                                        <input type="text" name="txtClientName" class="form-control" placeholder="Nome do cliente" aria-label="Nome do cliente" aria-describedby="txtClientName" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col d-flex align-items-center justify-content-center">
                                    <input type="submit" class="btn btn-primary mt-2" value="Cadastrar">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <?php include_once(__DIR__ . "/includes/access-error.php"); ?>
        <?php endif; ?>
    </div>
</div>
<?php include("includes/footer.php"); ?>