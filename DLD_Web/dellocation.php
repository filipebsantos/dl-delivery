<?php
require_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/LocationDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["id"])) {
    $locationData = new LocationDAO($dbConn);

    $loadedData = $locationData->getLocation(intval($_GET["id"]));
    
    if (!$loadedData) {
        header("Location: " . $BASE_URL . "location.php");
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <div id="page-content" class="col-auto col-md-7">
            <?php if (!empty($returnMessage["msg"])) : ?>
                <div class="container">
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                    <a href="<?= $BASE_URL ?>location.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-caret-left-fill"></i>
                        Voltar
                    </a>
                </div>
            <?php else : ?>
                <div class="container mt-4">
                    <h4 class="mb-4">Tem certeza que deseja exluir a localização de '<?= $loadedData["name"] ?>'?</h4>

                    <div class="row">
                        <div class="col d-flex justify-content-end">
                            <iframe height="300" width="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=400&amp;hl=en&amp;q=<?= $loadedData["latitude"] ?>,%20<?= $loadedData["longitude"] ?>&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                        </div>
                        <div class="col d-flex flex-column justify-content-start align-self-center">
                            <span class="fs-3">Tipo: <?= $loadedData["type"] ?></span>
                            <span class="fs-3">Bairro: <?= $loadedData["neighborhood"] ?></span> 
                        </div>
                    </div>
                    <div class="container text-center mt-3">
                        <form action="<?= $BASE_URL ?>locationprocess.php" method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="locationId" value="<?= $loadedData["id"] ?>">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Sim</button>
                            <a href="<?= $BASE_URL ?>location.php?clie=<?= $loadedData["clientid"] ?>" class="btn btn-danger"><i class="bi bi-x-lg"></i> Não</a>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>