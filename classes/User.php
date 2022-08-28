<?php
/**
 * Класс пользователя
 * @param int $id идентификатор пользователя
 * @param bool $isAdmin является ли пользователь администратором
 * @param bool $isGuest является ли пользователь администратором
 * @param string $login логин пользователя
 * @param string $name имя пользователя
 * @param string $surname фамилия пользователя
 * @param string $patronymic отчество пользователя
 */
class User {
    public int $id = 0;
    public bool $isAdmin = false;
    public bool $isGuest = true;
    public string $login;
    public string $name;
    public string $surname;
    public string $patronymic;

    /**
     * @param PDO $conn подключение к БД
     * @param array $session данные сессии
     */
    function __construct(PDO $conn = null, array $session = null) {
        if (isset($conn)) {
            if (isset($session) && !empty($session) && isset($session['user'])) {
                $user = $conn->prepare("SELECT * FROM `users` WHERE `id` = :id AND `login` = :login");
                $user->execute([
                    'id' => (int) ($session['user']['id']),
                    'login' => trim($session['user']['login'])
                ]);
                $user = $user->fetch(PDO::FETCH_ASSOC);
                if (isset($user) && !empty($user)) {
                    $this->id = (int) ($user['id']);
                    $this->isAdmin = (int) ($user['isAdmin']) === 1 ? true : false;
                    $this->isGuest = false;
                    $this->login = trim($user['login']);
                    $this->name = trim($user['name']);
                    $this->surname = trim($user['surname']);
                    $this->patronymic = isset($user['patronymic']) && !empty($user['patronymic']) ? trim($user['patronymic']) : null;
                }
            }
        }
    }
}

if (isset($conn)) {
    if (isset($session)) {
        $_USER = new User($conn, $session);
    } else {
        $_USER = new User($conn);
    }
} else {
    $_USER = new User();
}