<?php
require_once(__DIR__ . "/../includes/header.php");
require_once(__DIR__ . "/../includes/db.php");
require_once(__DIR__ . "/../dao/ClientDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["clie"]) && $_GET["clie"] !== null) {
    $clie = filter_input(INPUT_GET, "clie", FILTER_SANITIZE_NUMBER_INT);

    if ($clie !== false) {
        $clieDAO = new ClientDAO($dbConn);

        $clientData = $clieDAO->getClient($clie);
        if (empty($clientData)) {
            $returnMessage = [
                "msg" => "Cliente não encontrado.",
                "type" => "danger"
            ];
        } else {
            $loadedData = true;
        }
    }
}
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<div class="container">
    <?php include(__DIR__ . "/../includes/offcanva-menu.php"); ?>
    <?php if (!empty($returnMessage["msg"])) : ?>
        <div class="container mt-3" id="alert-box">
            <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                <?= $returnMessage["msg"] ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-2">
        <h3 class="mb-2">Localização</h3>
        <form action="<?= $BASE_URL ?>locationprocess.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="capturedPhoto" id="capturedPhoto">
            <div class="input-group mb-2">
                <span class="input-group-text" id="txtClientId">ID</span>
                <input type="text" name="txtClientId" class="form-control" value="<?= isset($loadedData) ? $clientData["id"] : "" ?>" required>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="txtClientName">Cliente</span>
                <input type="text" class="form-control" value="<?= isset($loadedData) ? $clientData["name"] : "" ?>" disabled>
            </div>


            <div class="input-group mb-2">
                <span class="input-group-text" id="txtCoordinatesLabel">Coordenadas</span>
                <input type="text" name="txtCoordinates" id="txtCoordinates" class="form-control" required>
                <button id="buttonGetCoordinates" class="btn btn-secondary" type="button">
                    <svg id="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-satellite">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3.707 6.293l2.586 -2.586a1 1 0 0 1 1.414 0l5.586 5.586a1 1 0 0 1 0 1.414l-2.586 2.586a1 1 0 0 1 -1.414 0l-5.586 -5.586a1 1 0 0 1 0 -1.414z" />
                        <path d="M6 10l-3 3l3 3l3 -3" />
                        <path d="M10 6l3 -3l3 3l-3 3" />
                        <path d="M12 12l1.5 1.5" />
                        <path d="M14.5 17a2.5 2.5 0 0 0 2.5 -2.5" />
                        <path d="M15 21a6 6 0 0 0 6 -6" />
                    </svg>
                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="optNeighborhood">Bairro</span>
                <select class="form-select" name="optNeighborhood" id="optNeighborhood" autocomplete="on" required>
                    <option value="ALAGADIÇO">Alagadiço</option>
                    <option value="ATLÂNTICO">Atlântico</option>
                    <option value="BARRA">Barra</option>
                    <option value="BOCA DO POÇO">Boca do Poço</option>
                    <option value="BOI MORTO">Boi Morto</option>
                    <option value="CABRA MORTA">Cabra Morta</option>
                    <option value="CAMPO DE AVIAÇÃO">Campo de Aviação</option>
                    <option value="CAMPO DE SEMENTE">Campo de Semente</option>
                    <option value="CARLOTAS">Carlotas</option>
                    <option value="CARRASCO">Carrasco</option>
                    <option value="CCF">CCF</option>
                    <option value="CENTRO">Centro</option>
                    <option value="CONJUNTO NOVA ESPERANÇA">Conj. Nova Esperança</option>
                    <option value="COREIA">Coreia</option>
                    <option value="FREXEIRA">Frexeira</option>
                    <option value="JARDIM DE BAIXO">Jardim de Baixo</option>
                    <option value="JARDIM DE CIMA">Jardim de Cima</option>
                    <option value="JARDIM DO MEIO">Jardim do Meio</option>
                    <option value="LAGOA GRANDE">Lagoa Grande</option>
                    <option value="LAGOA">Lagoa</option>
                    <option value="MALEITAS">Maleitas</option>
                    <option value="MOCÓ">Mocó</option>
                    <option value="PARACURU BEACH">Paracuru Beach</option>
                    <option value="PARAZINHO">Parazinho</option>
                    <option value="PLANALTO DA BARRA">Planalto da Barra</option>
                    <option value="POÇO DOCE">Poço Doce</option>
                    <option value="RIACHO DOCE">Riacho Doce</option>
                    <option value="SÃO PEDRO DE BAIXO">São Pedro de Baixo</option>
                    <option value="SÃO PEDRO">São Pedro</option>
                    <option value="SÍTIO INGLÊS">Sítio Inglês</option>
                    <option value="TORRE">Torre</option>
                    <option value="VILA SÃO JOSÉ">Vila São José</option>
                </select>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="optLocationType">Tipo de Localização</span>
                <select class="form-select" name="optLocationType" id="optLocationType" autocomplete="on" required>
                    <option value="RESIDENCIA">Residência</option>
                    <option value="TRABALHO">Trabalho</option>
                    <option value="FAMILIAR">Familiar</option>
                    <option value="ENTREGA">Entrega</option>
                </select>
            </div>

            <div class="input-group mb-3">
                <label class="input-group-text" for="txtObs">Obervações</label>
                <textarea class="form-control" name="txtObs" id="txtObs" maxlength="255" rows="3"></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="button" id="openCameraModal" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cameraModal">
                    <i class="bi bi-camera-fill"></i> Tirar foto
                </button>
                <input class="btn btn-primary" type="submit" value="Salvar Localização">
                <a href="<?= $_SERVER["HTTP_REFERER"] ?>" class="btn btn-secondary">Voltar</a>
            </div>

            <!-- Camera Modal -->
            <div class="modal fade" id="cameraModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="cameraModalLabel">Foto da Casa</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="videoContainer" id="videoContainer">
                                <video id="player" autoplay playsinline></video>
                            </div>
                            <div class="canvasContainer" id="canvasContainer">
                                <canvas id="canvas" id="canvas" width="800" height="600"></canvas>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="captureButton" type="button" class="btn btn-primary"><i class="bi bi-camera-fill"></i> Tirar foto</button>
                            <button id="closeModal" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Camera Modal -->
        </form>
        <!-- Modal for Get Coordinate map -->
        <div class="modal fade" id="locationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="locationModalLabel">Sua Localização</h5>
                        <span id="accuracyTag">Precisão:</span>
                    </div>
                    <div class="modal-body text-center">
                        <span id="mapText" class="d-none"></span>
                        <div id="map" style="height: 300px;">Obtendo Localização...</div>
                    </div>
                    <div class="modal-footer">
                        <button id="grabCoordinates" class="btn btn-primary" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                            </svg>
                            Obter Localização
                        </button>
                        <button id="closeCoordinateModal" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal for Get Coordinate map -->
    </div>
</div>
<script src="<?= $BASE_URL ?>js/custom/camera.js"></script>
<script src="<?= $BASE_URL ?>js/custom/coordinate.js"></script>
<?php
require_once(__DIR__ . "/../includes/footer.php");
