<?php
require_once(__DIR__ . "/../includes/header.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}
?>
<div class="container">
    <?php include(__DIR__ . "/../includes/offcanva-menu.php"); ?>
    <h1 class="d-inline-flex justify-content-start mt-3">Clientes</h1>

    <div class="container">
        <form action="<?= $BASE_URL ?>delivery/viewclient.php" method="get">
            <div class="input-group">
                <span class="input-group-text">ID</span>
                <input class="form-control" type="tel" name="clientid">
                <input class="btn btn-primary" type="submit" value="Buscar">
            </div>
        </form>
    </div>
</div>
<?php
require_once(__DIR__ . "/../includes/footer.php");
