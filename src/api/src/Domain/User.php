<?php

namespace DLDelivery\Domain;

use DLDelivery\Domain\Enum\UserRole;

class User
{
    private ?int $userID;
    private ?int $erpUserID;
    private string $name;
    private string $username;
    private string $password;
    private UserRole $access;

    public function __construct(string $name, string $username, UserRole $access, string $password, ?int $userID = null, ?int $erpUserID = null)
    {
        $this->name = $name;
        $this->username = $username;
        $this->access = $access;
        $this->password = $password;
        $this->userID = $userID;
        $this->erpUserID = $erpUserID;
    }

    public function getID(): ?int { return $this->userID; }
    public function getErpUserID(): ?int { return $this->erpUserID; }
    public function getName(): string { return $this->name; }
    public function getUsername(): string { return $this->username; }
    public function getPassword(): string { return $this->password; }
    public function getAccess(): UserRole { return $this->access; }  
}