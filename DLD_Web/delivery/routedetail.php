<?php
require_once(__DIR__ . "/../includes/header.php");
require_once(__DIR__ . "/../includes/db.php");
require_once(__DIR__ . "/../dao/RouteDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["routeid"]) && $_GET["routeid"] !== "") {

    $routeDAO = new RouteDAO($dbConn);
    $routeData = $routeDAO->getRoute($_GET["routeid"]);
    $routeLocations = $routeDAO->getRouteLocations($_GET["routeid"]);
    $routeClients = $routeDAO->listRouteClients(intval($_GET["routeid"]));

    if (!empty($routeLocations) || !empty($routeClients)) {
        $hasLocations = true;
    }
} else {
    $message->setMessage("Rota inválida ou não definida", "danger", "delivery/home.php");
}

if (isset($_SESSION["locationReturn"])) {
    $client_id = $_SESSION["locationReturn"]["clieId"];
    $client_name = $_SESSION["locationReturn"]["clieName"];
    $client_locations = $_SESSION["locationReturn"]["locations"];
    $loadedData = true;
    unset($_SESSION["locationReturn"]);
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

    <h1 class="d-inline-flex justify-content-start mt-3">Rota <?= $routeData["id"] ?></h1>
    <div class="row d-flex flex-column">
        <div class="col-3">
            <span class="badge rounded-pill text-bg-<?= $routeData["status"] == "PENDENTE" ? "warning" : "success" ?>"><?= $routeData["status"] ?></span>
        </div>
        <div class="col">
            <a href="<?= $BASE_URL ?>delivery/home.php" class="btn btn-outline-secondary btn-sm mt-3 mb-3">
                <i class="bi bi-caret-left-fill"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="d-grid gap-2">
        <?php if (!empty($routeLocations) && $routeData["status"] == "PENDENTE") : ?>
            <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="updateRouteStatus"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="routestatus" value="INICIADA"><button type="submit" class="btn btn-primary"><i class="bi bi-play-fill"></i> Iniciar rota</button></form>
        <?php elseif ($routeData["status"] == "INICIADA") : ?>
            <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="updateRouteStatus"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="routestatus" value="FINALIZADA"><button type="submit" class="btn btn-danger"><i class="bi bi-stop-fill"></i> Finalizar rota</button></form>
        <?php endif; ?>
    </div>

    <div class="container">
        <span class="fs-4"><strong>Localizações:</strong></span>
        <table class="table align-middle text-center table-striped">
            <thead>
                <th scope="col">Cliente</th>
                <th scope="col">Mapa</th>
                <th scope="col">Foto</th>
                <th scope="col">Ação</th>
            </thead>
            <tbody class="table-group-divider">
                <?php if (isset($hasLocations)) : ?>
                    <?php foreach ($routeLocations as $location) : ?>
                        <tr scope="row">
                            <td class="align-self-center"><?= $location["clientName"] ?></td>
                            <td><a class="btn btn-primary btn-sm" target="_blank" href="https://www.google.com/maps/search/?api=1&query=<?= $location["latitude"] ?>%2C<?= $location["longitude"] ?>"><i class="bi bi-map-fill"></a></td>
                            <td><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#picture<?= $location["id"] ?>"><i class="bi bi-camera-fill"></button></td>
                            </td>
                            <td>
                                <?php if ($routeData["status"] == "PENDENTE") : ?>
                                    <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="dellocation"><input type="hidden" name="routeid" value="<?= $location["routeid"] ?>"><input type="hidden" name="routelocationid" value="<?= $location["id"] ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button></form>
                                <?php else : ?>
                                    <button class="btn btn-secondary btn-sm" disabled><i class="bi bi-slash-circle"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($routeClients)) : ?>
            <!-- Table for added clients -->
            <div class="container">
                <span class="fs-4"><strong>Clientes:</strong></span>
                <table class="table align-middle text-center table-striped">
                    <thead>
                        <th scope="col">Cliente</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($routeClients as $rClients) : ?>
                            <tr scope="row">
                                <td><?= !empty($rClients["name"]) ? $rClients["name"] : "Não encontrado" ?></td>
                                <td><a class="btn btn-primary btn-sm" href="<?= $BASE_URL ?>delivery/addlocation.php?clie=<?= $rClients["clientid"] ?>"><i class="bi bi-plus-circle-fill"></i></a></td>
                                <td>
                                    <form action="<?= $BASE_URL ?>locationprocess.php" method="post"><input type="hidden" name="action" value="find"><input type="hidden" name="origin" value="route"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="txtClientId" value="<?= $rClients["clientid"] ?>"><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i></button></form>
                                </td>
                                <td>
                                    <?php if ($routeData["status"] == "PENDENTE") : ?>
                                        <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="delclientroute"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="clientid" value="<?= $rClients["clientid"] ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button></form>
                                    <?php else : ?>
                                        <button class="btn btn-secondary btn-sm" disabled><i class="bi bi-slash-circle"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Table for added clients -->
        <?php endif; ?>

        <?php foreach ($routeLocations as $location) : ?>
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

        <?php if (isset($loadedData)) : ?>
            <!-- Modal for Locations Lits -->
            <div class="modal fade" id="modalLocationList" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalLocationListLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalLocationListLabel"><?= $client_name ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <thead>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Mapa</th>
                                    <th scope="col">Foto</th>
                                    <th scope="col">Ação</th>
                                </thead>
                                <tbody class="table-group-divider">
                                    <?php foreach ($client_locations as $cLocation) : ?>
                                        <tr scope="row">
                                            <td><?= $cLocation["type"] ?></td>
                                            <td><a class="btn btn-primary btn-sm" target="_blank" href="https://www.google.com/maps/search/?api=1&query=<?= $cLocation["latitude"] ?>%2C<?= $cLocation["longitude"] ?>"><i class="bi bi-map-fill"></a></td>
                                            <td><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#picLoc<?= $cLocation["id"] ?>"><i class="bi bi-camera-fill"></i></button></td>
                                            <td>
                                                <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="addlocation"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="locationid" value="<?= $cLocation["id"] ?>"><input type="hidden" name="clientid" value="<?= $client_id ?>"><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-circle-fill"></i></button></form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="addclient2route"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="clientid" value="<?= $client_id ?>"><button type="submit" class="btn btn-primary">Adicionar Cliente</button></form>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal for Locations Lits -->

            <!-- Modals for house picture -->
            <?php foreach ($client_locations as $cLocation) : ?>
                <div class="modal fade" id="picLoc<?= $cLocation["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Foto da Casa</h1>
                            </div>
                            <div class="modal-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <img loading="lazy" src="<?= $cLocation["housepicture"] !== null ? $BASE_URL . "imgs/houses/" . $cLocation["housepicture"] : $BASE_URL . "imgs/picture.jpg" ?>" width="300" height="auto" alt="">
                                    </div>
                                </div>
                                <p class="mt-3"><strong>Obervações:</strong> <?= $cLocation["obs"] ?></p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" data-bs-target="#modalLocationList" data-bs-toggle="modal">Voltar para lista</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- Modals for house picture -->

        <?php endif; ?>
    </div>
</div>
<?php if (isset($loadedData)) : ?>
    <script type="text/javascript">
        $(window).on('load', function() {
            $('#modalLocationList').modal('show');
        });
    </script>
<?php endif; ?>
<?php
require_once(__DIR__ . "/../includes/footer.php");
