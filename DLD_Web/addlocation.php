<?php
require_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/ClientDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["clie"])) {
    $clientId = filter_input(INPUT_GET, "clie", FILTER_SANITIZE_NUMBER_INT);

    if ($clientId) {

        $clientDAO = new ClientDAO($dbConn);
        $client = $clientDAO->getClient(intval($clientId));

        if ($client) {
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
                                <h3 class="mb-4">Cadastrar nova localização</h3>
                            </div>
                            <div class="col align-self-center">
                                <a href="<?= isset($loadedData) ? $BASE_URL . "location.php?clie=" . $client["id"] : $BASE_URL . "location.php" ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-caret-left-fill"></i>
                                    Voltar
                                </a>
                            </div>
                        </div>

                        <form action="<?= $BASE_URL ?>locationprocess.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="txtClientId" value="<?= isset($loadedData) ? $client["id"] : "" ?>">

                            <div class="row mb-3">
                                <div class="col-sm-12 col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text" id="txtClientId">ID</span>
                                        <input type="text" name="txtClientId" class="form-control" value="<?= isset($loadedData) ? $client["id"] : "" ?>" disabled>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-9">
                                    <div class="input-group">
                                        <span class="input-group-text" id="txtClientName">Cliente</span>
                                        <input type="text" class="form-control" value="<?= isset($loadedData) ? $client["name"] : "" ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-auto col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text" id="txtCoordinates">Coordenadas</span>
                                        <input type="text" name="txtCoordinates" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-auto col-md-6">
                                    <div class="input-group">
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
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <div class="input-group">
                                        <span class="input-group-text" id="optLocationType">Tipo de Localização</span>
                                        <select class="form-select" name="optLocationType" id="optLocationType" autocomplete="on" required>
                                            <option value="RESIDENCIA" selected>Residência</option>
                                            <option value="TRABALHO">Trabalho</option>
                                            <option value="FAMILIAR">Familiar</option>
                                            <option value="ENTREGA">Entrega</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="input-group">
                                        <label class="input-group-text" for="imgHousePic">Foto da casa</label>
                                        <input type="file" class="form-control" name="imgHousePic" id="imgHousePic" accept="image/png, image/jpeg">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <label class="input-group-text" for="txtObs">Obervações</label>
                                <textarea class="form-control" name="txtObs" id="txtObs" maxlength="255" rows="3"></textarea>
                            </div>

                            <div class="row mt-3">
                                <div class="col d-flex align-items-center justify-content-center">
                                    <input type="submit" class="btn btn-primary mt-2" value="Salvar Localização">
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