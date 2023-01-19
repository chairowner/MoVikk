<?php
/**
 * Класс пользователя
 * @param int $id идентификатор пользователя
 * @param PDO $conn подключение к БД
 * @param string $mainTable основная таблица
 * @param bool $isGuest является ли пользователь гостем
 */
class User {
    protected int $id = 0;
    protected bool $isGuest = true;
    private PDO $conn;
    private string $mainTable = "users";

    /**
     * @param PDO $conn подключение к БД
     * @param array $session данные сессии
     */
    public function __construct(PDO $conn = null) {
        if (isset($conn)) {
            $this->conn = $conn;
            session_start(['read_and_close'=>true]);
            $session = $_SESSION;
            session_write_close();
            if (isset($session, $session['user'], $session['user']['id']) && !empty($session) && !empty($session['user'])) {
                $user = $this->conn->prepare("SELECT `isEmailConfirmed`, `ban` FROM `{$this->mainTable}` WHERE `id` = :id");
                $user->execute(['id' => (int) $session['user']['id']]);
                $user = $user->fetch(PDO::FETCH_ASSOC);
                if (isset($user) && !empty($user)) {
                    $user['ban'] = (bool) $user['ban'];
                    $user['isEmailConfirmed'] = (bool) $user['isEmailConfirmed'];
                    if ($user['ban'] || !$user['isEmailConfirmed']) {
                        # если пользователя забанили или его почта не подтверждена
                        session_start();
                        unset($_SESSION['user']);
                        session_write_close();
                    } else /* если всё хорошо */ {
                        $this->id = (int) $session['user']['id'];
                        $this->isGuest = false;
                    }
                }
            }
        }
    }
    
    /**
     * ID пользователя
     * @return int 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Проверка на уровень "гость"
     * @return bool 
     */
    public function isGuest() {
        return $this->isGuest;
    }
    
    /**
     * Проверка на уровень "администратор"
     * @return bool 
     */
    public function isAdmin() {
        $isAdmin = false;
        try {
            $isAdmin = $this->conn->prepare("SELECT `isAdmin` FROM `{$this->mainTable}` WHERE `id` = :id");
            $isAdmin->execute(['id' => $this->id]);
            $isAdmin = $isAdmin->fetch(PDO::FETCH_ASSOC);
            $isAdmin = isset($isAdmin['isAdmin']) ? (bool) $isAdmin['isAdmin'] : false;
        } catch (PDOException $e) {
            $isAdmin = false;
        }

        return $isAdmin;
    }

    /**
     * Выборка данных
     * @param array $tabelFields поля таблицы
     * @return array 
     */
    public function get(array $tabelFields = ['*']) {
        $response = [];
        if (is_array($tabelFields) && isset($tabelFields) && !empty($tabelFields)) {
            try {
                $response = $this->conn->prepare("SELECT ".implode(', ', $tabelFields)." FROM `{$this->mainTable}` WHERE `id` = :id");
                $response->execute(['id' => $this->id]);
                $response = $response->fetch(PDO::FETCH_ASSOC);
                if (count($tabelFields) === 1) {
                    if (trim($tabelFields[0]) !== '*') {
                        $response = $response[$tabelFields[0]];
                    }
                }
            } catch (PDOException $e) {
                $response['error'] = $e->getMessage();
            }
        }
        return $response;
    }

    /**
     * Реализация выхода из системы
     */
    public function logout() {
        if (!$this->isGuest()) {
            session_start();
            session_destroy();
            session_write_close();
        }
    }

    /**
     * Реализация входа в систему
     * @param string $email E-мail адрес, привязанный к аккаунту
     * @param string $password пароль от аккаунта
     * @return array ['status' - bool,'redirect' - string, 'info' - array]
     */
    public function login(string $email, string $password) {
        $response = [
            'status' => false,
            'redirect' => null,
            'info' => [],
        ];
        if (!$this->isGuest()) return $response;
        $user = $this->conn->prepare("SELECT `id`, `password`, `isAdmin` FROM `{$this->mainTable}` WHERE `email` = :email");
        $user->execute(['email' => $email]);
        $user = $user->fetch(PDO::FETCH_ASSOC);
        if (isset($user) && !empty($user)) {
            $user['password'] = trim($user['password']);
            $user['isAdmin'] = (bool) $user['isAdmin'];
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user']['id'] = (int) $user['id'];
                session_write_close();
                $response['status'] = true;
                $response['redirect'] = $user['isAdmin'] ? '/admin' : '/';
                $response['info'][] = 'Вы успешно авторизовались!';
            } else {
                $response['info'][] = 'Неверная почта или пароль';
            }
        } else {
            $response['info'][] = 'Неверная почта или пароль';
        }
        return $response;
    }

    /**
     * 
     * @return array
     */
    public function registration(string $email, string $password, string $passwordRepeat, string $name, string $surname, string $patronymic = null) {
        $response = [
            'status' => false,
            'info' => []
        ];
        $minPasswordLength = 6;
        $maxPasswordLength = 50;
        $pattern = "/^[0-9A-Za-z%$#@!?&]{".$minPasswordLength.",".$maxPasswordLength."}$/";
        if (strlen($password) > $minPasswordLength) {
            if (preg_match($pattern, $password)) {
                if ($password === $passwordRepeat) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $hash = null;
                    while (true) {
                        $hash = md5($email . time());
                        $hashUnique = $this->conn->prepare("SELECT `id` FROM `{$this->mainTable}` WHERE `hash` = ?");
                        $hashUnique->execute([$hash]);
                        $hashUnique = $hashUnique->fetch(PDO::FETCH_ASSOC);
                        if ($hashUnique === false) {
                            unset($hashUnique);
                            break;
                        }
                    }

                    $queryData = [
                        'email' => $email,
                        'name' => $name,
                        'surname' => $surname,
                        'password' => $passwordHash,
                        'hash' => $hash
                    ];
                    if (isset($patronymic)) $queryData['patronymic'] = $patronymic;

                    $sql = "INSERT INTO `{$this->mainTable}` SET ";
                    $i = 0;
                    foreach ($queryData as $key => $value) {
                        if ($i > 0) $sql .= ", ";
                        else $i = 1;
                        $sql .= "`$key` = :$key";
                    }
                    unset($i);
                    $query = $this->conn->prepare($sql);
                    if ($query->execute($queryData)) {
                        $response['status'] = true;
                        $response['hash'] = $hash;
                        $response['info'][] = 'Аккаунт успешно зарегистрирован!';
                    } else {
                        $response['info'][] = 'При регистрации возникла ошибка. Перезагрузите страницу и повторите попытку.';
                    }

                } else {
                    $response['info'][] = 'Пароли должны совпадать';
                }
            } else {
                $response['info'][] = 'Пароль может включать в себя только буквы латинского алфавита, цифры и следующие спец. символы: %$#@!?&';
            }
        } else {
            $response['info'][] = 'Пароль должен состоять минимум из 6 символов';
        }
        return $response;
    }
}