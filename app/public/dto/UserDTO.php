<?php
// app/public/dto/UserDTO.php

class UserDTO {
    private $user_id;
    private $username;
    private $password_hash;
    private $role;
    private $created_at;

    public function __construct($user_id, $username, $password_hash, $role, $created_at) {
        $this->user_id       = $user_id;
        $this->username      = $username;
        $this->password_hash = $password_hash;
        $this->role          = $role;
        $this->created_at    = $created_at;
    }

    public function getUserId() {
        return $this->user_id;
    }
    public function getUsername() {
        return $this->username;
    }
    public function getPasswordHash() {
        return $this->password_hash;
    }
    public function getRole() {
        return $this->role;
    }
    public function getCreatedAt() {
        return $this->created_at;
    }
}
?>
