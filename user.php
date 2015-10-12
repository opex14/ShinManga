<?php if(!defined('SMG')) die('USE MAIN SCRIPT!');
require_once "config.php";
session_start(); //Запускаем сессии

/** 
 * Класс для авторизации
 * @author дизайн студия ox2.ru 
 */ 
class AuthClass{

    private $sql, $query;
    
    function __construct() {
        $this->sql = new mysqli(CFG_MYSQL_HOST,CFG_MYSQL_USER,CFG_MYSQL_PASSWORD,CFG_MYSQL_DATABASE);
    }
    
    function doQuery($mode, $user) {
    $login = $this->sql->real_escape_string($user);
    if ($mode == 'name') {
    $querr = "user_login = '".$login ."'"; 
    } elseif ($mode == 'id') {
    $querr = "user_id = '".$login ."'";
    }
        $queryText = "SELECT user_id, user_login, user_password FROM ".CFG_MYSQL_PREFIX."users WHERE ".$querr." LIMIT 1";
        
        if($query = $this->sql->query($queryText)) {
            $results = $query->fetch_array();
            return $results;
        } else {
            return null;
        }
    }

    /**
     * Проверяет, авторизован пользователь или нет
     * Возвращает true если авторизован, иначе false
     * @return boolean 
     */
    public function isAuth() {
        if (isset($_SESSION["is_auth"])) { //Если сессия существует
            return $_SESSION["is_auth"]; //Возвращаем значение переменной сессии is_auth (хранит true если авторизован, false если не авторизован)
        }
        else return false; //Пользователь не авторизован, т.к. переменная is_auth не создана
    }
    
    /**
     * Авторизация пользователя
     * @param string $login
     * @param string $passwors 
     */
    public function auth($login, $passwors) {
    
        $database = $this->doQuery('name', $login);
        if (strtolower($login) == strtolower($database['user_login']) && md5(md5($passwors)) == $database['user_password']) { //Если логин и пароль введены правильно
            $_SESSION["is_auth"] = true; //Делаем пользователя авторизованным
            $_SESSION["user_id"] = $database['user_id']; 
            $_SESSION["login"] = $login; //Записываем в сессию логин пользователя
            return true;
        }
        else { //Логин и пароль не подошел
            $_SESSION["is_auth"] = false;
            return false; 
        }
    }
    
    /**
     * Метод возвращает логин авторизованного пользователя 
     */
    public function getLogin() {
        if ($this->isAuth()) { //Если пользователь авторизован
            return $_SESSION["login"]; //Возвращаем логин, который записан в сессию
        }
    }
    
    
    public function out() {
        $_SESSION = array(); //Очищаем сессию
        session_destroy(); //Уничтожаем
    }
    
    function __destruct(){
     //Close the Connection
     $this->sql->close();
    }
}
$auth = new AuthClass();

if (isset($_POST["login"]) && isset($_POST["password"])) { //Если логин и пароль были отправлены
    if (!$auth->auth($_POST["login"], $_POST["password"])) { //Если логин и пароль введен не правильно
        echo "<h2 style=\"color:red;\">Логин и пароль введен не правильно!</h2>";
    }
}

if (isset($_GET["is_exit"])) { //Если нажата кнопка выхода
    if ($_GET["is_exit"] == 1) {
        $auth->out(); //Выходим
        header("Location: ?is_exit=0"); //Редирект после выхода
    }
}
?>
