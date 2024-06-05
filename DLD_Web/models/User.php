<?php
    /*This class implements the Users.*/

    class User {
        
        private $id;
        private $username;
        private $firstname;
        private $lastname;
        private $password;
        private $role;
        private $token;
        private $tokenExpiration;
        private $active;
        private $phoneNumber;

        public function __construct() {
            
        }

        // User id
        public function getId() {
            return $this->id;
        }

        public function setId(int $id) {
            $this->id = intval($id);
        }

        // Username
        public function getUsername() {
            return $this->username;
        }

        public function setUsername(string $username) {
            $this->username = $username;
        }

        // User's firstname
        public function getFirstname() {
            return $this->firstname;
        }

        public function setFirstname(string $firstname) {
            $this->firstname = $firstname;
        }

        // User's lastname
        public function getLastname() {
            return $this->lastname;
        }

        public function setLastname(string $lastname) {
            $this->lastname = $lastname;
        }

        // User's password
        public function getPassword() {
            return $this->password;
        }

        public function setPassword(string $password) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }

        //User's role
        public function getRole() {
            return $this->role;
        }

        public function setRole(int $role) {
            if ($role >= 1 && $role <= 4){
                $this->role = intval($role);
            } else {
                $this->role = 1;
            }
        }

        // User's login token
        public function getToken() {
            return $this->token;
        }

        public function setToken(string $token) {
            if(isset($token)){
                $this->token = $token;
            }
        }  

        public function getTokenExpiration() {
            return $this->tokenExpiration;
        }

        public function setTokenExpiration(DateTime $tokenExpiration) {
            $this->tokenExpiration = $tokenExpiration;
        }

        // User's active
        public function getUserActive() {
            return $this->active;
        }

        public function setUserActive(int $active) {
            $this->active = intval($active);
        }

        public function setPhoneNumber(string $phone) {
            $this->phoneNumber = $phone;
        }

        public function getPhoneNumber() {
            return $this->phoneNumber;
        }

    }

    interface UserInterface {
        // List user
        public function listUsers(bool $onlyActive = false);
        
        // Create user
        public function createUser(User $user);

        // Update user
        public function updateUser(User $user);
        
        // Get user data
        public function getUser(int $userid);

        // Delete user
        public function delUser(int $userid);

        // Validate login user
        public function loginUser(string $username, string $password);

        // Save token for "Remember me"
        public function saveToken(User $user);

        // Login user by remember me token
        public function loginByToken(string $token);

        // Update user password
        public function updatePassword(string $password, int $userID);
    }