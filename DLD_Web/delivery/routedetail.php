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

    <div class="row justify-content-around align-items-center">
        <div class="col">
            <div class="row d-flex flex-column">
                <div class="col">
                    <span class="badge rounded-pill text-bg-<?= $routeData["status"] == "PENDENTE" ? "warning" : "success" ?>"><?= $routeData["status"] ?></span>
                </div>
                <div class="col">
                    <h1 class="d-inline-flex justify-content-start">Rota <?= $routeData["id"] ?></h1>
                </div>
            </div>
        </div>
        <div class="col d-flex justify-content-end">
            <a href="<?= $BASE_URL ?>delivery/home.php" class="btn btn-outline-secondary btn-sm mt-3 mb-3">
                <i class="bi bi-caret-left-fill"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="d-grid gap-2 mt-3">
        <?php if ((!empty($routeLocations) || !empty($routeClients)) && $routeData["status"] == "PENDENTE") : ?>
            <form id="startRouteForm" action="<?= $BASE_URL ?>routeprocess.php" method="post">
                <input type="hidden" name="action" value="updateRouteStatus">
                <input type="hidden" name="routeid" value="<?= $routeData["id"] ?>">
                <input type="hidden" name="routestatus" value="INICIADA">
                <button id="startRouteButton" type="submit" class="btn btn-primary">
                    <i class="bi bi-play-fill"></i> Iniciar rota
                </button>
            </form>
        <?php elseif ($routeData["status"] == "INICIADA") : ?>
            <form action="<?= $BASE_URL ?>routeprocess.php" method="post">
                <input type="hidden" name="action" value="updateRouteStatus">
                <input type="hidden" name="routeid" value="<?= $routeData["id"] ?>">
                <input type="hidden" name="routestatus" value="FINALIZADA">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-stop-fill"></i> Finalizar rota
                </button>
            </form>
            <button id="buttonGoogleMapsRoute" class="btn btn-success" onclick="openGoogleMapsRoute()">
                Iniciar Rota
            </button>
        <?php endif; ?>
    </div>

    <!-- Start Div Locations -->
    <div class="container" id="locationList">
        <span class="fs-4"><strong>Localizações:</strong></span>
        <div class="table-responsive">
            <table class="table table-sm align-middle text-center table-striped">
                <thead>
                    <th scope="col">Cliente</th>
                    <th scope="col">Bairro</th>
                    <th scope="col">Mapa</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Ação</th>
                </thead>
                <tbody class="table-group-divider">
                    <?php if (isset($hasLocations)) : ?>
                        <?php foreach ($routeLocations as $location) : ?>
                            <tr scope="row text-nowrap">
                                <td><?= $location["clientName"] ?></td>
                                <td><?= $location["neighborhood"] ?></td>
                                <td><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#miniMapLocation<?= $location["id"] ?>" data-lat="<?= $location["latitude"] ?>" data-lng="<?= $location["longitude"] ?>"><i class="bi bi-map-fill"></i></button></td>
                                <td>
                                    <button type="button" class="btn btn-<?= $location["housepicture"] == null ? "secondary" : "primary" ?> btn-sm" data-bs-toggle="modal" data-bs-target="#picture<?= $location["id"] ?>">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                </td>
                                <td>
                                    <?php if ($routeData["status"] == "PENDENTE") : ?>
                                        <form action="<?= $BASE_URL ?>routeprocess.php" method="post">
                                            <input type="hidden" name="action" value="dellocation">
                                            <input type="hidden" name="routeid" value="<?= $location["routeid"] ?>">
                                            <input type="hidden" name="routelocationid" value="<?= $location["id"] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($hasLocations)) : ?>
            <?php foreach ($routeLocations as $location) : ?>
                <div class="modal fade" id="miniMapLocation<?= $location["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Mapa</h1>
                            </div>
                            <div class="modal-body text-center">
                                <iframe loading="lazy" height="300" width="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com.br/maps?width=100%25&amp;height=400&amp;hl=pt&amp;gl=BR&amp;q=<?= $location["latitude"] ?>,%20<?= $location["longitude"] ?>&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= $location["latitude"] ?>%2C<?= $location["longitude"] ?>&hl=pt-br" target="_blank" class="btn btn-primary">Abrir no Google Maps</a>
                                <button class="btn btn-primary" data-bs-target="#modalLocationList" data-bs-toggle="modal">Voltar para lista</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
                            <div class="row">
                                <div class="col text-center">
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
    </div>
    <!-- End Div Locations -->

    <?php if (!empty($routeClients)) : ?>
        <!-- Start Table Clients -->
        <div class="container" id="clientList">
            <span class="fs-4"><strong>Clientes:</strong></span>
            <div class="table-responsive">
                <table class="table table-sm align-middle text-center table-striped">
                    <thead>
                        <th scope="col"></th>
                        <th scope="col">Cliente</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($routeClients as $rClients) : ?>
                            <tr scope="row">
                                <?php if (!is_null($rClients["phonenumber"])) : ?>
                                    <td>
                                        <?php
                                        //Manipulate individual route status
                                        switch (intval($rClients["status"])) {
                                            case 0:
                                                $bootstrapIcon = "bi-play-fill";
                                                $bootstrapButtonType = "success";
                                                $formNextStatus = 1;
                                                break;

                                            case 1:
                                                $bootstrapIcon = "bi-stop-fill";
                                                $bootstrapButtonType = "danger";
                                                $formNextStatus = 2;
                                                break;

                                            case 2:
                                                unset($formNextStatus);
                                                break;
                                        }
                                        ?>
                                        <?php if (isset($formNextStatus)) : ?>
                                            <form action="<?= $BASE_URL ?>routeprocess.php" method="post">
                                                <input type="hidden" name="action" value="changeClientRouteStatus">
                                                <input type="hidden" name="status" value="<?= $formNextStatus ?>">
                                                <input type="hidden" name="routeid" value="<?= $routeData["id"] ?>">
                                                <input type="hidden" name="clientid" value="<?= $rClients["clientid"] ?>">
                                                <input type="hidden" name="phoneNumber" value="<?= $rClients["phonenumber"] ?>">
                                                <button type="submit" class="btn btn-<?= $bootstrapButtonType ?> btn-sm">
                                                    <i class="bi <?= $bootstrapIcon ?>"></i>
                                                </button>
                                            </form>
                                        <?php else : ?>
                                            <button type="button" class="btn btn-secondary btn-sm"><i class="bi bi-check-circle-fill"></i></button>
                                        <?php endif; ?>
                                    </td>
                                <?php else : ?>
                                    <td><button type="button" class="btn btn-secondary btn-sm"><i class="bi bi-play-fill"></i></button></td>
                                <?php endif; ?>
                                <td <?= intval($rClients["has_residence"]) == 0 ? "style='color: red; font-weight: bold;'" : "" ?>><?= !empty($rClients["name"]) ? $rClients["name"] : "Não encontrado" ?></td>
                                <td>
                                    <a class="btn btn-<?= is_null($rClients["phonenumber"]) ? "secondary" : "success" ?> btn-sm" href="<?= is_null($rClients["phonenumber"]) ? "#" : "https://wa.me/55" . $rClients["phonenumber"] ?>" <?= is_null($rClients["phonenumber"]) ? "" : "target='_blank'" ?>>
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </td>
                                <td>
                                    <form action="<?= $BASE_URL ?>locationprocess.php" method="post">
                                        <input type="hidden" name="action" value="find">
                                        <input type="hidden" name="origin" value="route">
                                        <input type="hidden" name="routeid" value="<?= $routeData["id"] ?>">
                                        <input type="hidden" name="txtClientId" value="<?= $rClients["clientid"] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- End Table Clients -->
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
                        <div class="table-responsive">
                            <table class="table table-sm align-middle text-center table-striped">
                                <thead>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Bairro</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </thead>
                                <tbody class="table-group-divider">
                                    <?php foreach ($client_locations as $cLocation) : ?>
                                        <tr scope="row">
                                            <td><?= $cLocation["type"] ?></td>
                                            <td><?= $cLocation["neighborhood"] ?></td>
                                            <td><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#picMap<?= $cLocation["id"] ?>"><i class="bi bi-map-fill"></i></button></td>
                                            <td><a href="<?= $BASE_URL ?>delivery/viewlocation.php?locationid=<?= $cLocation["id"] ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil-fill"></i></a></td>
                                            <td>
                                                <form action="<?= $BASE_URL ?>routeprocess.php" method="post"><input type="hidden" name="action" value="addlocation"><input type="hidden" name="routeid" value="<?= $routeData["id"] ?>"><input type="hidden" name="locationid" value="<?= $cLocation["id"] ?>"><input type="hidden" name="clientid" value="<?= $client_id ?>"><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-circle-fill"></i></button></form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-primary" href="<?= $BASE_URL ?>delivery/addlocation.php?clie=<?= $client_id ?>"><i class="bi bi-plus-circle-fill"></i> Nova Localização</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for Locations Lits -->

        <!-- Modal for minimaps -->
        <?php foreach ($client_locations as $location) : ?>
            <div class="modal fade" id="picMap<?= $location["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Mapa</h1>
                        </div>
                        <div class="modal-body text-center">
                            <iframe loading="lazy" height="300" width="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com.br/maps?width=100%25&amp;height=400&amp;hl=pt&amp;gl=BR&amp;q=<?= $location["latitude"] ?>,%20<?= $location["longitude"] ?>&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#modalHousePicture<?= $location["id"] ?>" data-bs-toggle="modal"><i class="bi bi-house-fill"></i> Foto da casa</button>
                            <button class="btn btn-primary" data-bs-target="#modalLocationList" data-bs-toggle="modal">Voltar para lista</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!-- End Modal for minimaps -->

        <!-- Modal for house picture -->
        <?php foreach ($client_locations as $location) : ?>
            <div class="modal fade" id="modalHousePicture<?= $location["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel"><i class="bi bi-house-fill"></i> Foto da Casa</h1>
                        </div>
                        <div class="modal-body text-center">
                            <img loading="lazy" src="<?= $location["housepicture"] !== null ? $BASE_URL . "imgs/houses/" . $location["housepicture"] : $BASE_URL . "imgs/picture.jpg" ?>" width="300" height="auto" alt="">
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#modalLocationList" data-bs-toggle="modal">Voltar para lista</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!-- End Modal for house picture -->
    <?php endif; ?>

</div>
<?php if (isset($loadedData)) : ?>
    <script type="text/javascript">
        $(window).on('load', function() {
            $('#modalLocationList').modal('show');
        });
    </script>
<?php endif; ?>
<script>
    function getGoogleMapsUrl() {
        const finalDestination = "-3.411313,-39.030812";
        const finalDestinationPlaceId = "ChIJMdZOFQMfwQcRrwq0qSA926A";

        let destinations = [];
        document.querySelectorAll("button[data-lat][data-lng]").forEach(function(element) {
            let lat = element.getAttribute("data-lat");
            let lng = element.getAttribute("data-lng");
            destinations.push(lat + "," + lng);
        });

        let googleMapsUrl = "https://www.google.com/maps/dir/?api=1&travelmode=driving";
        googleMapsUrl += "&waypoints=" + destinations.join("%7C");
        googleMapsUrl += "&destination=" + finalDestination + "&destination_place_id=" + finalDestinationPlaceId;

        return googleMapsUrl;
    }

    document.getElementById("startRouteButton").addEventListener("click", function(event) {
        event.preventDefault();

        if (document.querySelectorAll("button[data-lat][data-lng]").length > 0) {
            let googleMapsUrl = getGoogleMapsUrl();

            window.open(googleMapsUrl, "_blank");
            document.getElementById("startRouteForm").submit();
        } else {
            document.getElementById("startRouteForm").submit();
        }
    });

    function openGoogleMapsRoute() {
        let googleMapsUrl = getGoogleMapsUrl();
        window.open(googleMapsUrl, "_blank");
    }
</script>

<?php
require_once(__DIR__ . "/../includes/footer.php");
