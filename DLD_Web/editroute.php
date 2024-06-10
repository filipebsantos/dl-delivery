<?php
require_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/RouteDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["routeid"]) && filter_input(INPUT_GET, "routeid", FILTER_SANITIZE_NUMBER_INT)) {
    $routeDAO = new RouteDAO($dbConn);

    $route = $routeDAO->getRoute(intval($_GET["routeid"]));
    $routeLocations = $routeDAO->getRouteLocations(intval($_GET["routeid"]));
    $routeClients = $routeDAO->listRouteClients(intval($_GET["routeid"]));

    // Change badge backgroud color based in route's status
    switch ($route["status"]) {
        case "PENDENTE":
            $badgeType = "warning";
            break;
        case "INICIADA":
            $badgeType = "primary";
            break;
        case "FINALIZADA":
            $badgeType = "success";
            break;
    }
}

if (isset($_SESSION["locationReturn"])) {
    $client_id = $_SESSION["locationReturn"]["clieId"];
    $client_name = $_SESSION["locationReturn"]["clieName"];
    $client_locations = $_SESSION["locationReturn"]["locations"];
    $loadedData = true;
    unset($_SESSION["locationReturn"]);
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <div id="page-content" class="col-auto col-md-7">
            <div class="container mt-5">
                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="<?= $BASE_URL ?>route.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-caret-left-fill"></i>
                    Voltar
                </a>

                <div class="continer mb-3" style="max-width: 500px;">
                    <span class="fs-3">Rota <?= $route["id"] ?> - Entregador: <?= $route["deliverymanName"] ?></span><br>
                    Status: <span class="badge rounded-pill text-bg-<?= $badgeType ?>"><?= $route["status"] ?></span><br>
                    Criado por: <?= $route["createBy"] ?><br>
                    Criada em: <?= date_format(date_create($route["datecreation"]), "d/m/Y à\s H:i:s") ?>
                </div>

                <div class="container mb-3">


                    <div class="row row-cols-2">
                        <div class="col">
                            <form action="locationprocess.php" method="post">
                                <input type="hidden" name="action" value="find">
                                <input type="hidden" name="origin" value="route">
                                <input type="hidden" name="routeid" value="<?= $route["id"] ?>">
                                <div class="input-group">
                                    <span class="input-group-text" id="txtClientId">Selecione o cliente</span>
                                    <input type="text" name="txtClientId" class="form-control" <?= $route["status"] != "PENDENTE" ? "disabled" : "required" ?>>
                                    <button class="btn btn-success" type="submit" <?= $route["status"] != "PENDENTE" ? "disabled" : "" ?>><i class="bi bi-search"></i> Buscar</button>
                                </div>
                            </form>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <?php if ((!empty($routeLocations) || !empty($routeClients)) && $route["status"] == "PENDENTE") : ?>
                                <form action="routeprocess.php" method="post"><input type="hidden" name="action" value="updateRouteStatus"><input type="hidden" name="routeid" value="<?= $route["id"] ?>"><input type="hidden" name="routestatus" value="INICIADA"><button type="submit" class="btn btn-primary"><i class="bi bi-play-fill"></i> Iniciar rota</button></form>
                            <?php elseif ($route["status"] == "INICIADA") : ?>
                                <form action="routeprocess.php" method="post"><input type="hidden" name="action" value="updateRouteStatus"><input type="hidden" name="routeid" value="<?= $route["id"] ?>"><input type="hidden" name="routestatus" value="FINALIZADA"><button type="submit" class="btn btn-danger"><i class="bi bi-stop-fill"></i> Finalizar rota</button></form>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <?php if (!empty($routeLocations)) : ?>
                    <div class="container">
                        <span class="fs-4"><strong>Localizações adicionadas:</strong></span>
                        <table class="table align-middle table-striped">
                            <thead>
                                <th scope="col">Cliente</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Ver no Mapa</th>
                                <th scope="col">Ver Foto</th>
                                <th scope="col">Ação</th>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php foreach ($routeLocations as $rLocation) : ?>
                                    <tr scope="row">
                                        <td><?= !empty($rLocation["clientName"]) ? $rLocation["clientName"] : "Não encontrado" ?></td>
                                        <td><?= $rLocation["type"] ?></td>
                                        <td><button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalMap<?= $rLocation["id"] ?>"><i class="bi bi-map-fill"></button></td>
                                        <td><button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPicture<?= $rLocation["id"] ?>"><i class="bi bi-camera-fill"></i></button></td>
                                        <td>
                                            <?php if ($route["status"] == "PENDENTE") : ?>
                                                <form action="routeprocess.php" method="post"><input type="hidden" name="action" value="dellocation"><input type="hidden" name="routeid" value="<?= $rLocation["routeid"] ?>"><input type="hidden" name="routelocationid" value="<?= $rLocation["id"] ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button></form>
                                            <?php else : ?>
                                                <button class="btn btn-secondary btn-sm" disabled><i class="bi bi-slash-circle"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Modals for maps -->
                        <?php foreach ($routeLocations as $rLocation) : ?>
                            <div class="modal fade" id="modalPicture<?= $rLocation["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Foto da Casa</h1>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col">
                                                    <img loading="lazy" src="<?= $rLocation["housepicture"] !== null ? $BASE_URL . "imgs/houses/" . $rLocation["housepicture"] : $BASE_URL . "imgs/picture.jpg" ?>" width="750" height="auto" alt="">
                                                </div>
                                            </div>
                                            <p class="mt-3"><strong>Obervações:</strong> <?= $rLocation["obs"] ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- Modals for maps -->

                        <!-- Modals for house picture -->
                        <?php foreach ($routeLocations as $rLocation) : ?>
                            <div class="modal fade" id="modalMap<?= $rLocation["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Mapa</h1>
                                        </div>
                                        <div class="modal-body">
                                            <iframe loading="lazy" height="600" width="750" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=400&amp;hl=en&amp;q=<?= $rLocation["latitude"] ?>,%20<?= $rLocation["longitude"] ?>&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- Modals for house picture -->

                    </div>
                <?php endif; ?>

                <?php if (!empty($routeClients)) : ?>
                    <!-- Table for added clients -->
                    <div class="container">
                        <span class="fs-4"><strong>Clientes adicionados:</strong></span>
                        <table class="table align-middle table-striped">
                            <thead>
                                <th scope="col">Cliente</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php foreach ($routeClients as $rClients) : ?>
                                    <tr scope="row">
                                        <td <?= intval($rClients["has_residence"]) == 0 ? "style='color: red; font-weight: bold;'" : "" ?>><?= !empty($rClients["name"]) ? $rClients["name"] : "Não encontrado" ?></td>
                                        <td><a class="btn btn-primary btn-sm" href="<?= $BASE_URL ?>addlocation.php?clie=<?= $rClients["clientid"] ?>">Cadastrar Localização</a></td>
                                        <td>
                                            <form action="locationprocess.php" method="post"><input type="hidden" name="action" value="find"><input type="hidden" name="origin" value="route"><input type="hidden" name="origin" value="route"><input type="hidden" name="routeid" value="<?= $route["id"] ?>"><input type="hidden" name="txtClientId" value="<?= $rClients["clientid"] ?>"><button type="submit" class="btn btn-primary btn-sm">Ver Localizações</button></form>
                                        </td>
                                        <td>
                                            <?php if ($route["status"] == "PENDENTE") : ?>
                                                <form action="routeprocess.php" method="post"><input type="hidden" name="action" value="delclientroute"><input type="hidden" name="routeid" value="<?= $route["id"] ?>"><input type="hidden" name="clientid" value="<?= $rClients["clientid"] ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button></form>
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
                                            <th scope="col">Ver no Mapa</th>
                                            <th scope="col">Ver foto</th>
                                            <th scope="col">Ação</th>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <?php foreach ($client_locations as $location) : ?>
                                                <tr scope="row">
                                                    <td><?= $location["type"] ?></td>
                                                    <td><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#picLocMap<?= $location["id"] ?>"><i class="bi bi-map-fill"></button></td>
                                                    <td><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#picLoc<?= $location["id"] ?>"><i class="bi bi-camera-fill"></i></button></td>
                                                    <td>
                                                        <form action="routeprocess.php" method="post"><input type="hidden" name="action" value="addlocation"><input type="hidden" name="routeid" value="<?= $route["id"] ?>"><input type="hidden" name="locationid" value="<?= $location["id"] ?>"><input type="hidden" name="clientid" value="<?= $client_id ?>"><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-circle-fill"></i></button></form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <form action="routeprocess.php" method="post"><input type="hidden" name="action" value="addclient2route"><input type="hidden" name="routeid" value="<?= $route["id"] ?>"><input type="hidden" name="clientid" value="<?= $client_id ?>"><button type="submit" class="btn btn-primary">Adicionar Cliente</button></form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal for Locations Lits -->

                    <!-- Modals for house picture -->
                    <?php foreach ($client_locations as $location) : ?>
                        <div class="modal fade" id="picLoc<?= $location["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Foto da Casa</h1>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <img loading="lazy" src="<?= $location["housepicture"] !== null ? $BASE_URL . "imgs/houses/" . $location["housepicture"] : $BASE_URL . "imgs/picture.jpg" ?>" width="750" height="auto" alt="">
                                            </div>
                                        </div>
                                        <p class="mt-3"><strong>Obervações:</strong> <?= $location["obs"] ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" data-bs-target="#modalLocationList" data-bs-toggle="modal">Voltar para lista</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Modals for house picture -->

                    <!-- Modals for maps -->
                    <?php foreach ($client_locations as $location) : ?>
                        <div class="modal fade" id="picLocMap<?= $location["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Mapa</h1>
                                    </div>
                                    <div class="modal-body">
                                        <iframe loading="lazy" height="600" width="750" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=400&amp;hl=en&amp;q=<?= $location["latitude"] ?>,%20<?= $location["longitude"] ?>&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" data-bs-target="#modalLocationList" data-bs-toggle="modal">Voltar para lista</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Modals for maps -->
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php if (isset($loadedData)) : ?>
    <script type="text/javascript">
        $(window).on('load', function() {
            $('#modalLocationList').modal('show');
        });
    </script>
<?php endif; ?>
<?php include("includes/footer.php"); ?>