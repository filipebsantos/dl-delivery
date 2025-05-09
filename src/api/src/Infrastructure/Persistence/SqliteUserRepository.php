<?php

namespace DLDelivery\Infrastructure\Persistence;

use DLDelivery\Application\DTO\User\CreateUserDTO;
use DLDelivery\Application\DTO\User\UpdateUserDTO;
use DLDelivery\Application\DTO\User\UserListFilterDTO;
use DLDelivery\Domain\Enum\UserRole;
use DLDelivery\Domain\Interface\UserRepositoryInterface;
use DLDelivery\Domain\User;
use DLDelivery\Exception\User\UserNotFoundException;
use DLDelivery\Exception\User\UserAlreadyExistsException;
use PDO;

class SqliteUserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function getUserByID(int $userID): User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE userID = :userID");
        $stmt->execute([':userID' => $userID]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new UserNotFoundException;
        }

        return $this->hydrateUser($row);
    }

    public function getUserByUsername(string $username): User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new UserNotFoundException;
        }

        return $this->hydrateUser($row);
    }

    private function hydrateUser(array $row): User
    {
        return new User(
            name: $row['name'],
            username: $row['username'],
            access: UserRole::from($row['access']),
            password: $row['password'],
            userID: (int) $row['userID'],
            erpUserID: $row['erpUserID'] !== null ? (int) $row['erpUserID'] : null
        );
    }

    public function listUsers(UserListFilterDTO $dto): array
    {
        $sql = "SELECT * FROM users";
        $params = [];

        if (!empty($dto->filters)) {
            $clauses = [];
            foreach ($dto->filters as $key => $value) {
                $clauses[] = "$key LIKE :$key";
                $params[$key] = "%$value%";
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $dto->perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($dto->page - 1) * $dto->perPage, PDO::PARAM_INT);

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'hydrateUser'], $rows);
    }

    public function createUser(CreateUserDTO $dto): User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $dto->username]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            throw new UserAlreadyExistsException;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO users (erpUserID, name, username, password, access)
            VALUES (:erpUserID, :name, :username, :password, :access)
        ");

        $stmt->execute([
            ':erpUserID' => $dto->erpUserID,
            ':name' => $dto->name,
            ':username' => $dto->username,
            ':password' => $dto->password,
            ':access' => $dto->access->value
        ]);

        $userID = (int) $this->pdo->lastInsertId();

        return new User(
            $dto->name,
            $dto->username,
            $dto->access,
            $dto->password,
            $userID,
            $dto->erpUserID
        );
    }

    public function updateUser(UpdateUserDTO $dto): User
    {
        $fields = [];
        $params = [':userID' => $dto->userID];

        if (!is_null($dto->erpUserID)) {
            $fields[] = 'erpUserID = :erpUserID';
            $params[':erpUserID'] = $dto->erpUserID;
        }

        if (!is_null($dto->name)) {
            $fields[] = 'name = :name';
            $params[':name'] = $dto->name;
        }

        if (!is_null($dto->password)) {
            $fields[] = 'password = :password';
            $params[':password'] = $dto->password;
        }

        if (!is_null($dto->access)) {
            $fields[] = 'access = :access';
            $params[':access'] = $dto->access->value;
        }

        if (empty($fields)) {
            return $this->getUserByID($dto->userID);
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE userID = :userID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $this->getUserByID($dto->userID);
    }

    public function deleteUser(int $userID): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE userID = :userID");
        return $stmt->execute([':userID' => $userID]);
    }
}