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
                    <span class="input-group-text" id="txtCoordinates">Coordenadas</span>
                    <input type="text" name="txtCoordinates" class="form-control" required>
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
        </div>
    </div>
    <script src="<?= $BASE_URL ?>js/custom/camera.js"></script>
    <?php
    require_once(__DIR__ . "/../includes/footer.php");
