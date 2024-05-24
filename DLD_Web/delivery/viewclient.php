<?php
require_once(__DIR__ . "/../includes/header.php");
require_once(__DIR__ . "/../includes/db.php");
require_once(__DIR__ . "/../dao/ClientDAO.php");
require_once(__DIR__ . "/../dao/LocationDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["clientid"]) && $_GET["clientid"] !== null) {
    $clientID = filter_input(INPUT_GET, "clientid", FILTER_SANITIZE_NUMBER_INT);

    if ($clientID !== false) {
        $clientDAO = new ClientDAO($dbConn);

        $clientData = $clientDAO->getClient($clientID);
        if (empty($clientData)) {
            $returnMessage = [
                "msg" => "Cliente não localizado.",
                "type" => "danger"
            ];
        } else {

            $locationDAO = new LocationDAO($dbConn);
            $clientLocations = $locationDAO->listLocations($clientID);
        }
    }
}
?>
<div class="container">
    <?php include(__DIR__ . "/../includes/offcanva-menu.php"); ?>
    <?php if (!empty($returnMessage["msg"])) : ?>
        <div class="container mt-3" id="alert-box">
            <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                <?= $returnMessage["msg"] ?>
            </div>
        </div>
    <?php endif; ?>

    <a href="<?= $BASE_URL ?>delivery/findclient.php" class="btn btn-outline-secondary btn-sm mt-3 mb-3">
        <i class="bi bi-caret-left-fill"></i>
        Voltar
    </a>

    <?php if (isset($clientData, $clientLocations)) : ?>
        <h3><?= $clientData["id"] ?> - <?= $clientData["name"] ?></h3>

        <div class="d-grid gap-2">
            <a href="<?= $BASE_URL ?>delivery/addlocation.php?clie=<?= $clientData["id"] ?>" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Nova localização</a>
        </div>

        <table class="table align-middle table-striped mt-3">
            <thead>
                <th scope="col">Tipo</th>
                <th scope="col">Mapa</th>
                <th scope="col">Foto</th>
                <th scope="col">Ação</th>
            </thead>
            <tbody class="table-group-divider">
                <?php foreach ($clientLocations as $location) : ?>
                    <tr scope="row">
                        <td><?= $location["type"] ?></td>
                        <td><a class="btn btn-primary btn-sm" target="_blank" href="https://www.google.com/maps/search/?api=1&query=<?= $location["latitude"] ?>%2C<?= $location["longitude"] ?>"><i class="bi bi-map-fill"></a></td>
                        <td><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#picture<?= $location["id"] ?>"><i class="bi bi-camera-fill"></button></td></td>
                        <td><a class="btn btn-primary btn-sm" href="<?= $BASE_URL ?>delivery/viewlocation.php?locationid=<?= $location["id"] ?>"><i class="bi bi-pencil-fill"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php foreach ($clientLocations as $location) : ?>
        <div class="modal fade" id="picture<?= $location["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Foto da Casa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <img loading="lazy" src="<?= $location["housepicture"] !== null ? $BASE_URL . "imgs/houses/" . $location["housepicture"] : $BASE_URL . "imgs/picture.jpg" ?>" width="300" height="auto" alt="">
                            </div>
                        </div>
                        <p class="mt-3"><strong>Obervações:</strong> <?= $location["obs"] ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
require_once(__DIR__ . "/../includes/footer.php");
