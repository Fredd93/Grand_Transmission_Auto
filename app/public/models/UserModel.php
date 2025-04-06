<?php
// app/public/models/UserModel.php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../dto/UserDTO.php'; // Adjust path if needed

class UserModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct(); // Initialize the PDO connection from BaseModel
    }

    /**
     * Fetch a single user by username.
     *
     * @param string $username
     * @return UserDTO|null
     */
    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new UserDTO(
                $row['user_id'],
                $row['username'],
                $row['password_hash'],
                $row['role'],
                $row['created_at']
            );
        }
        return null;
    }

    /**
     * Verify a user's password by username.
     *
     * @param string $username
     * @param string $password
     * @return UserDTO|null
     */
    public function verifyUser($username, $password)
    {
        $user = $this->getUserByUsername($username);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $user;
        }
        return null;
    }

    /**
     * Fetch all users.
     *
     * @return UserDTO[]
     */
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new UserDTO(
                $row['user_id'],
                $row['username'],
                $row['password_hash'],
                $row['role'],
                $row['created_at']
            );
        }
        return $results;
    }

    /**
     * Fetch a single user by ID.
     *
     * @param int $userId
     * @return UserDTO|null
     */
    public function getUserById($userId)
    {
        $sql = "SELECT * FROM users WHERE user_id = :userId LIMIT 1";
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new UserDTO(
                $row['user_id'],
                $row['username'],
                $row['password_hash'],
                $row['role'],
                $row['created_at']
            );
        }
        return null;
    }
}
?>
