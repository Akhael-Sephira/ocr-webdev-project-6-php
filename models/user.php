<?php 

require_once('./utility/db_model.php');

define('SALT_FACTOR', 10);

enum FailedSignup {
    case InvalidEmail;
    case InvalidPassword;
    case EmailAlreadyUsed;
}

enum FailedLogin {
    case NotFound;
    case PasswordIncorrect;
    case MaxAttempts;
}

class User extends DBModel {

    public static $schema = [
        'id'=>'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'email'=>'VARCHAR(50) NOT NULL UNIQUE',
        'password'=>'VARCHAR(100)',
        'lockUntil'=>'INT(6)',
        'loginAttempts'=>'INT(6)',
    ];

    public function preSave() {
        if ($this->id === NULL) {
            if (!User::isEmailValid($this->email)) return false;
            if (!User::isPasswordValid($this->password)) return false;
            $this->password = password_hash($this->password, PASSWORD_BCRYPT, ['cost'=>SALT_FACTOR]);
        }
        return true;
    }

    /** Returns true if the given password is in a valid format */
    public static function isPasswordValid($password) {
        preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[*.!@$%^&(){}\[\]:;<>,.?\/~_+\-=|\\\]).{8,32}$/', 
        $password, $output_array);
        return sizeof($output_array) > 0;
    }
    /** Returns true if the given email is in a valid format */
    public static function isEmailValid($password) {
        preg_match('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 
        $password, $output_array);
        return sizeof($output_array) > 0;
    }
    /** Verify email & password, returns the associated user if successful */
    public static function getAuthenticated($email, $password) {
        $user = User::find(            
            "email = :email", 
            [':email'=> $_POST['email'] ?? ''],
            1
        );
        if (!$user) return false;
        if ($user->isLocked()) return false;
        if (!$user->validatePassword($password)) {
            $user->incLoginAttempts();
            echo "Failed to authenticate";
            return false;
        };
        // Reset loginLock
        if ($user->loginAttempts != 0 || $user->lockUntil) {
            $user->loginAttempts = 0;
            $user->lockUntil = NULL;
            $user->update(["loginAttempts", "lockUntil"]);
        }
        return $user;
    }
    /** Returns true if the user's login attempt is currently locked */
    public function isLocked() {
        return $this->lockUntil && $this->lockUntil > time();
    }
    /** Increment the user's login attempts & lock it if reached the limit of allowed attempts */
    public function incLoginAttempts() {
        if ($this->lockUntil && $this->lockUntil < time()) {
            $this->loginAttempts = 0;
            $this->lockUntil = NULL;
        }
        $config = include('./config.php');
        $this->loginAttempts++;
        if ($this->loginAttempts >= $config->auth['max_login_attempts'] && !$this->isLocked()) {
            $this->lockUntil = time() + $config->auth['lock_time'];
        }
        $this->update(['loginAttempts', 'lockUntil']);
    }
    /** Compare the given password to the user's password */
    public function validatePassword($password) {
        return password_verify($password, $this->password);
    }
}

?>