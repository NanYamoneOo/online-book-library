<?php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Register user
    public function register($data) {
        // Prepare query
        $this->db->query('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Login user
    public function login($username, $password) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        if($row) {
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }
    
    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    // Find user by username
    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    // Get user by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get all users (for admin)
    public function getAllUsers() {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    // Update user profile
    public function updateProfile($data) {
        $this->db->query('UPDATE users SET username = :username, email = :email WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUser($data) {
    $this->db->query('UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id');
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':role', $data['role']);
    $this->db->bind(':id', $data['id']);
    
    return $this->db->execute();
}

    public function createUser($username, $email, $password, $role = 'user') {
    // Check if email already exists
    $this->db->query("SELECT id FROM users WHERE email = :email");
    $this->db->bind(':email', $email);
    $this->db->execute();

    if ($this->db->rowCount() > 0) {
        return false; // Email already exists
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $this->db->query("INSERT INTO users (username, email, password, role, created_at) 
                      VALUES (:username, :email, :password, :role, NOW())");
    $this->db->bind(':username', $username);
    $this->db->bind(':email', $email);
    $this->db->bind(':password', $hashed_password);
    $this->db->bind(':role', $role);

    return $this->db->execute();
}

    
    // Change password
    public function changePassword($id, $new_password) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':password', password_hash($new_password, PASSWORD_DEFAULT));
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    
    // Delete user
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        
        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}


?>