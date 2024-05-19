<?php
    include_once("includes/header.php");
    require_once("includes/db.php");
    require_once("dao/ClientDAO.php");
    require_once("dao/LocationDAO.php");

    $returnMessage = $message->getMessage();

    if (!empty($returnMessage)) {
        $message->clearMessage();
    }

    if (isset($_SESSION["locationReturn"])) {
        $client_id = $_SESSION["locationReturn"]["clieId"];
        $client_name = $_SESSION["locationReturn"]["clieName"];
        $client_locations = $_SESSION["locationReturn"]["locations"];
        $loadedData = true;
        unset($_SESSION["locationReturn"]);
    }

    if (isset($_GET["clie"])) {
        $clie = filter_input(INPUT_GET, "clie", FILTER_SANITIZE_NUMBER_INT);

        if ($clie) {

            $clientDAO = new ClientDAO($dbConn);
            $locationDAO = new LocationDAO($dbConn);

            $client = $clientDAO->getClient($clie);
            $location = $locationDAO->listLocations($clie);

            if ($client) {
                $client_id = $client["id"];
                $client_name = $client["name"];
                $client_locations = $location;
                $loadedData = true;
            }
        }
    }

?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <div id="page-content" class="col-auto col-md-9">
            <div class="container mt-5">

                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="container mt-3">
                    <h4>Selecione o cliente:</h4>
                    <div class="row">
                        <div class="col-8">
                            <form action="<?= $BASE_URL ?>locationprocess.php" method="post">
                                <input type="hidden" name="action" value="find">

                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">ID</span>
                                    <input class="form-control" type="text" name="txtClientId" id="txtClientId" placeholder="Informe o código do cliente...">
                                    <input class="btn btn-primary" type="submit" value="OK">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if (isset($loadedData)) : ?>
                    <div class="container mt-3 border rounded">
                        <div class="row mt-3">
                            <div class="col-auto col-md-9">
                                <span class="fs-4 fw-semibold">Cliente: <?= $client_name ?></span>
                            </div>
                            <div class="col-auto col-md-3">
                                <a href="<?= $BASE_URL ?>addlocation.php?clie=<?= $client_id ?>" class="btn btn-success">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span class="ms-2">Adicionar localização</span>
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <table class="table align-middle table-striped">
                                <thead>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Bairro</th>
                                    <th scope="col">Ver no mapa</th>
                                    <th scope="col">Foto da casa</th>
                                    <th scope="col">Ação</th>
                                </thead>
                                <tbody class="table-group-divider">
                                    <?php foreach ($client_locations as $loc) : ?>
                                        <tr scope="row">
                                            <td><?= $loc["type"] ?></td>
                                            <td><?= $loc["neighborhood"] ?></td>
                                            <td><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#picLocMap<?= $loc["id"] ?>"><i class="bi bi-map-fill"></button></td>
                                            <td><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#picLoc<?= $loc["id"] ?>"><i class="bi bi-camera-fill"></i></button></td>
                                            <td><a class="btn btn-primary btn-sm" href="editlocation.php?locid=<?= $loc["id"] ?>"><i class="bi bi-pencil-fill"></i></a> <a class="btn btn-danger btn-sm" href="dellocation.php?id=<?= $loc["id"] ?>"><i class="bi bi-trash-fill"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modals -->
                    <?php foreach ($client_locations as $loc) : ?>
                        <div class="modal fade" id="picLoc<?= $loc["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Foto da Casa</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <img loading="lazy" src="<?= $loc["housepicture"] !== null ? $BASE_URL . "imgs/houses/" . $loc["housepicture"] : $BASE_URL . "imgs/picture.jpg" ?>" width="750" height="auto" alt="">
                                            </div>
                                        </div>
                                        <p class="mt-3"><strong>Obervações:</strong> <?= $loc["obs"] ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="picLocMap<?= $loc["id"] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Mapa</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe loading="lazy" height="600" width="750" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=400&amp;hl=en&amp;q=<?= $loc["latitude"] ?>,%20<?= $loc["longitude"] ?>&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Modals -->
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>