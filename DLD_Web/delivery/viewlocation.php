<?php
require_once(__DIR__ . "/../includes/header.php");
require_once(__DIR__ . "/../includes/db.php");
require_once(__DIR__ . "/../dao/LocationDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["locationid"]) && $_GET["locationid"] !== null) {
    $locationID = filter_input(INPUT_GET, "locationid", FILTER_SANITIZE_NUMBER_INT);

    if ($locationID !== false) {
        $locationDAO = new LocationDAO($dbConn);

        $locationData = $locationDAO->getLocation($locationID);
        if (empty($locationData)) {
            $returnMessage = [
                "msg" => "Localização inválida.",
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
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="txtLocationId" value="<?= $locationData["id"]?>">

            <div class="input-group mb-2">
                <span class="input-group-text" id="txtClientId">ID</span>
                <input type="text" name="txtClientId" class="form-control" value="<?= isset($loadedData) ? $locationData["clientid"] : "" ?>" required>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="txtClientName">Cliente</span>
                <input type="text" class="form-control" value="<?= isset($loadedData) ? $locationData["name"] : "" ?>" disabled>
            </div>


            <div class="input-group mb-2">
                <span class="input-group-text" id="txtCoordinates">Coordenadas</span>
                <input type="text" name="txtCoordinates" class="form-control" value="<?= isset($loadedData) ? $locationData["latitude"] . ", " . $locationData["longitude"] : "" ?>" required>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="optNeighborhood">Bairro</span>
                <select class="form-select" name="optNeighborhood" id="optNeighborhood" autocomplete="on" required>
                    <option value="ALAGADIÇO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "ALAGADIÇO" ? "selected" : "" ?>>Alagadiço</option>
                    <option value="ATLÂNTICO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "ATLÂNTICO" ? "selected" : "" ?>>Atlântico</option>
                    <option value="BARRA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "BARRA" ? "selected" : "" ?>>Barra</option>
                    <option value="BOCA DO POÇO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "BOCA DO POÇO" ? "selected" : "" ?>>Boca do Poço</option>
                    <option value="BOI MORTO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "BOI MORTO" ? "selected" : "" ?>>Boi Morto</option>
                    <option value="CABRA MORTA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CABRA MORTA" ? "selected" : "" ?>>Cabra Morta</option>
                    <option value="CAMPO DE AVIAÇÃO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CAMPO DE AVIAÇÃO" ? "selected" : "" ?>>Campo de Aviação</option>
                    <option value="CAMPO DE SEMENTE" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CAMPO DE SEMENTE" ? "selected" : "" ?>>Campo de Semente</option>
                    <option value="CARLOTAS" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CARLOTAS" ? "selected" : "" ?>>Carlotas</option>
                    <option value="CARRASCO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CARRASCO" ? "selected" : "" ?>>Carrasco</option>
                    <option value="CCF" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CCF" ? "selected" : "" ?>>CCF</option>
                    <option value="CENTRO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CENTRO" ? "selected" : "" ?>>Centro</option>
                    <option value="CONJUNTO NOVA ESPERANÇA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "CONJUNTO NOVA ESPERANÇA" ? "selected" : "" ?>>Conj. Nova Esperança</option>
                    <option value="COREIA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "COREIA" ? "selected" : "" ?>>Coreia</option>
                    <option value="FREXEIRA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "FREXEIRA" ? "selected" : "" ?>>Frexeira</option>
                    <option value="JARDIM DE BAIXO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "JARDIM DE BAIXO" ? "selected" : "" ?>>Jardim de Baixo</option>
                    <option value="JARDIM DE CIMA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "JARDIM DE CIMA" ? "selected" : "" ?>>Jardim de Cima</option>
                    <option value="JARDIM DO MEIO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "JARDIM DO MEIO" ? "selected" : "" ?>>Jardim do Meio</option>
                    <option value="LAGOA GRANDE" <?= isset($loadedData) == true && $locationData["neighborhood"] == "LAGOA GRANDE" ? "selected" : "" ?>>Lagoa Grande</option>
                    <option value="LAGOA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "LAGOA" ? "selected" : "" ?>>Lagoa</option>
                    <option value="MALEITAS" <?= isset($loadedData) == true && $locationData["neighborhood"] == "MALEITAS" ? "selected" : "" ?>>Maleitas</option>
                    <option value="MOCÓ" <?= isset($loadedData) == true && $locationData["neighborhood"] == "MOCÓ" ? "selected" : "" ?>>Mocó</option>
                    <option value="PARACURU BEACH" <?= isset($loadedData) == true && $locationData["neighborhood"] == "PARACURU BEACH" ? "selected" : "" ?>>Paracuru Beach</option>
                    <option value="PARAZINHO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "PARAZINHO" ? "selected" : "" ?>>Parazinho</option>
                    <option value="PLANALTO DA BARRA" <?= isset($loadedData) == true && $locationData["neighborhood"] == "PLANALTO DA BARRA" ? "selected" : "" ?>>Planalto da Barra</option>
                    <option value="POÇO DOCE" <?= isset($loadedData) == true && $locationData["neighborhood"] == "POÇO DOCE" ? "selected" : "" ?>>Poço Doce</option>
                    <option value="RIACHO DOCE" <?= isset($loadedData) == true && $locationData["neighborhood"] == "RIACHO DOCE" ? "selected" : "" ?>>Riacho Doce</option>
                    <option value="SÃO PEDRO DE BAIXO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "SÃO PEDRO DE BAIXO" ? "selected" : "" ?>>São Pedro de Baixo</option>
                    <option value="SÃO PEDRO" <?= isset($loadedData) == true && $locationData["neighborhood"] == "SÃO PEDRO" ? "selected" : "" ?>>São Pedro</option>
                    <option value="SÍTIO INGLÊS" <?= isset($loadedData) == true && $locationData["neighborhood"] == "SÍTIO INGLÊS" ? "selected" : "" ?>>Sítio Inglês</option>
                    <option value="TORRE" <?= isset($loadedData) == true && $locationData["neighborhood"] == "TORRE" ? "selected" : "" ?>>Torre</option>
                    <option value="VILA SÃO JOSÉ" <?= isset($loadedData) == true && $locationData["neighborhood"] == "VILA SÃO JOSÉ" ? "selected" : "" ?>>Vila São José</option>
                </select>
            </div>

            <div class="input-group mb-2">
                <span class="input-group-text" id="optLocationType">Tipo de Localização</span>
                <select class="form-select" name="optLocationType" id="optLocationType" autocomplete="on" required>
                    <option value="RESIDENCIA" <?= isset($loadedData) == true && $locationData["type"] == "RESIDENCIA" ? "selected" : "" ?>>Residência</option>
                    <option value="TRABALHO" <?= isset($loadedData) == true && $locationData["type"] == "TRABALHO" ? "selected" : "" ?>>Trabalho</option>
                    <option value="FAMILIAR" <?= isset($loadedData) == true && $locationData["type"] == "FAMILIAR" ? "selected" : "" ?>>Familiar</option>
                    <option value="ENTREGA" <?= isset($loadedData) == true && $locationData["type"] == "ENTREGA" ? "selected" : "" ?>>Entrega</option>
                </select>
            </div>

            <div class="input-group mb-3">
                <label class="input-group-text" for="txtObs">Obervações</label>
                <textarea class="form-control" name="txtObs" id="txtObs" maxlength="255" rows="3"><?= isset($loadedData) ? $locationData["obs"] : "" ?></textarea>
            </div>

            <div class="input-group mb-2">
                <label class="input-group-text" for="imgHousePic">Foto da casa</label>
                <input type="file" class="form-control" name="imgHousePic" id="imgHousePic" accept="image/png, image/jpeg">
            </div>

            <div class="d-grid gap-2">
                <input class="btn btn-primary" type="submit" value="Editar Localização">
                <a href="<?= $_SERVER["HTTP_REFERER"] ?>" class="btn btn-secondary">Voltar</a>
            </div>

        </form>
    </div>
</div>
<?php
require_once(__DIR__ . "/../includes/footer.php");
