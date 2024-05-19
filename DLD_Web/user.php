<?php
include_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/UserDAO.php");

$userDAO = new UserDAO($dbConn);
$allUsers = $userDAO->listUsers();

function getRoleText($role)
{
    switch ($role) {
        case 1:
            return "Entregador";
        case 2:
            return "Operador";
        case 3:
            return "Gerente";
        case 4:
            return "Administrador";
        default:
            return "Papel Desconhecido";
    }
}

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
        <div id="page-content" class="col-auto col-md-9">
            <div class="container mt-5">

                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="container mb-3">
                    <div class="row">
                        <div class="col d-flex align-items-center justify-content-end">
                            <a href="<?= $BASE_URL ?>adduser.php" class="btn btn-success">
                                <i class="bi bi-person-plus"></i>
                                <span class="ms-2">Novo Usuário</span>
                            </a>
                        </div>
                    </div>
                </div>

                <table class="table align-middle table-striped">
                    <thead>
                        <th scope="col">Id</th>
                        <th scope="col">Usuário</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Acesso</th>
                        <th scope="col">Ativo</th>
                        <th scope="col">Ação</th>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($allUsers as $user) : ?>
                            <tr scope="row">
                                <td><?= $user["id"] ?></td>
                                <td><?= $user["username"] ?></td>
                                <td><?= $user["firstname"] ?> <?= $user["lastname"] ?></td>
                                <td><?= getRoleText($user["role"]) ?></td>
                                <td><?= $user["active"] == 1 ? "Sim" : "Não" ?></td>
                                <td><a class="btn btn-success btn-sm" href="edituser.php?id=<?= $user["id"] ?>"><i class="bi bi-pencil-fill"></i></a> <a class="btn btn-danger btn-sm" href="deluser.php?id=<?= $user["id"] ?>"><i class="bi bi-trash-fill"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>