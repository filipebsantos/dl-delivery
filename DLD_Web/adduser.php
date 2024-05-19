<?php
require_once("includes/header.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
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

                <div class="container">
                    <div class="row">
                        <div class="col-10">
                            <h3 class="mb-4">Cadastrar novo usuário</h3>
                        </div>
                        <div class="col align-self-center">
                            <a href="<?= $BASE_URL ?>user.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-caret-left-fill"></i>
                                Voltar
                            </a>
                        </div>
                    </div>

                    <form action="<?= $BASE_URL ?>userprocess.php" method="POST">
                        <input type="hidden" name="action" value="create">

                        <div class="row mt-2">
                            <div class="col">
                                <input type="text" class="form-control" name="txtFirstname" id="txtFirstname" placeholder="Nome" aria-label="Nome" required>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" name="txtLastname" id="txtLastname" placeholder="Sobrenome" aria-label="Sobrenome" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <input type="text" class="form-control" name="txtUserName" id="txtUserName" placeholder="Usuário" aria-label="Usuário" required>
                            </div>
                            <div class="col">
                                <select name="optRole" class="form-select" id="optRole">
                                    <option value="1" selected>Entregador</option>
                                    <option value="2">Operador</option>
                                    <option value="3">Gerente</option>
                                    <option value="4">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <input type="password" class="form-control" name="txtPassword1" id="txtPassword1" placeholder="Digite a senha" aria-label="Digite a senha" required>
                            </div>
                            <div class="col">
                                <input type="password" class="form-control" name="txtPassword2" id="txtPassword2" placeholder="Repita a senha" aria-label="Repita a senha" required>
                            </div>
                            <div class="col align-self-center">
                                <div class="form-check form-switch">
                                    <input type="checkbox" role="switch" class="form-check-input" id="optUserActive" name="optUserActive">
                                    <label for="optUserActive" class="form-check-label">Usuário ativo</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col d-flex align-items-center justify-content-center">
                                <input type="submit" class="btn btn-primary mt-2" value="Cadastrar">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>