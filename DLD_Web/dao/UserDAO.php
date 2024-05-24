<?php

    /* Data Access Objects for User's class */
    include(__DIR__ . "/../models/User.php");

    class UserDAO implements UserInterface {

        private $dbConn;

        public function __construct(PDO $dbConn) {
            $this->dbConn = $dbConn;   
        }

        public function getUser(int $userid) {
            

            $stmt = $this->dbConn->prepare("SELECT id, username, firstname, lastname, token, tokenexpiration, role, active FROM users WHERE id = :id");
            $stmt->bindParam(":id", $userid);

            try{
                $stmt->execute();
                $queryResult = $stmt->fetch();
                return $queryResult;
                
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function listUsers() {

            $stmt = $this->dbConn->query("SELECT id, username, firstname, lastname, role, active FROM users");
            
            try{
                return $stmt->fetchAll();
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function createUser(User $user) {

            // Verify if the username is alrady taken
            $stmt = $this->dbConn->prepare("SELECT COUNT(id) FROM users WHERE username = :username");
            $stmt->bindValue(":username", $user->getUsername());
            
            try {
                $stmt->execute();
                $queryResult = $stmt->fetch();

                // If found a register return that the username is in use
                if ($queryResult[""] >= 1) {
                    throw new Exception("Usuário '" . $user->getUsername() ."' já existe.");
                    exit();
                }

            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }

            // Save the user in database
            $stmt = $this->dbConn->prepare("INSERT INTO users (username, firstname, lastname, password, role, active) VALUES (:username, :firstname, :lastname, :password, :role, :active)");
            $stmt->bindValue(":username", $user->getUsername());
            $stmt->bindValue(":firstname", $user->getFirstname());
            $stmt->bindValue(":lastname", $user->getLastname());
            $stmt->bindValue(":password", $user->getPassword());
            $stmt->bindValue(":role", $user->getRole());
            $stmt->bindValue(":active", $user->getUserActive());
            
            try {
                return $stmt->execute();
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function updateUser (User $user) {

            // Check user id and user role is set
            if (($user->getId() != null) && ($user->getFirstname() != null) && ($user->getLastname() != null) && ($user->getRole() != null)){

                $stmt = $this->dbConn->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, role = :role, active = :active WHERE id = :id");
                $stmt->bindValue(":firstname", $user->getFirstname());
                $stmt->bindValue(":lastname", $user->getLastname());
                $stmt->bindValue(":role", $user->getRole());
                $stmt->bindValue(":active", $user->getUserActive());
                $stmt->bindValue(":id", $user->getId());

                try{
                    return $stmt->execute();
                } catch (PDOException $pdoError) {
                    throw new Exception($pdoError->getMessage());
                }
            } else {
                return false;
            }
        }

        public function delUser(int $userid) {

            $stmt = $this->dbConn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(":id", $userid);
            
            try{
                return $stmt->execute();
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function loginUser(string $username, string $password) {

            if (isset($username) && isset($password)){

                $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(":username", $username);

                try{
                    $stmt->execute();
                    $queryResult = $stmt->fetch();

                    if($queryResult){

                        if (password_verify($password, $queryResult["password"])) {
                            $user = new User;

                            $user->setId($queryResult["id"]);
                            $user->setUsername($queryResult["username"]);
                            $user->setFirstname($queryResult["firstname"]);
                            $user->setLastname($queryResult["lastname"]);
                            $user->setRole($queryResult["role"]);
                            $user->setUserActive($queryResult["active"]);

                            return $user;
                        } else {
                            return false;
                        }
                    } else {
                        throw new Exception("Usuário não localizado!");
                    }
                } catch (PDOException $pdoError) {
                    throw new Exception($pdoError->getMessage());
                }
            } else {
                return false;
            }
        }

        public function saveToken(User $user) {

            try{
                $stmt = $this->dbConn->prepare("UPDATE users SET token = :token, tokenexpiration = :tokenexpiration WHERE id = :id");
                $stmt->bindValue(":token", $user->getToken());
                $stmt->bindValue(":tokenexpiration", date_format($user->getTokenExpiration(), "d-m-Y H:i:s"));
                $stmt->bindValue(":id", $user->getId());
                $stmt->execute();

            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function loginByToken(string $token) {

            $stmt = $this->dbConn->prepare("SELECT id, username, firstname, lastname, role, token, tokenexpiration FROM users WHERE token = :token");
            $stmt->bindParam(":token", $token);
            try{
                $stmt->execute();

                $queryResult = $stmt->fetch();

                if($queryResult){
                    $user = new User;

                    $user->setId($queryResult["id"]);
                    $user->setUsername($queryResult["username"]);
                    $user->setFirstname($queryResult["firstname"]);
                    $user->setLastname($queryResult["lastname"]);
                    $user->setRole($queryResult["role"]);
                    $user->setToken($queryResult["token"]);
                    $user->setTokenExpiration(date_create($queryResult["tokenexpiration"], new DateTimeZone("America/Fortaleza")));

                    $dateNow = date_create("now");

                    if($dateNow < $user->getTokenExpiration()){
                        return $user;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
            
        }

        public function updatePassword (string $password, int $userID) {
            if (isset($password) && isset($userID)) {
                
                $stmt = $this->dbConn->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
                $stmt->bindValue(":id", $userID);
                try{
                    return $stmt->execute();
                } catch (PDOException $pdoError) {
                    throw new Exception($pdoError->getMessage());
                }
            }
        }
    }