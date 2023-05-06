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
    private int $maxNameLength = 64;

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
    public function GetId() {
        return $this->id;
    }
    
    /**
     * get table
     * @return string
     */
    public function GetTable() {
        return $this->mainTable;
    }

    /**
     * Получить максимальную длину для фамилии/имя/отчества
     * @return int
     */
    public function GetMaxNameLength() {
        return $this->maxNameLength;
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
    public function Logout() {
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
    public function Login(string $email, string $password) {
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
                $user['id'] = (int) $user['id'];
                session_start();
                $_SESSION['user']['id'] = $user['id'];
                session_write_close();
                $response['status'] = true;
                $response['redirect'] = $user['isAdmin'] ? '/admin' : '/';
                $response['info'][] = 'Вы успешно авторизовались!';
                // обнуляем хэш для восстановления пароля
                $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `recoveryHash` = :recoveryHash, `recoveryHashDate` = :recoveryHashDate WHERE `id` = :userId");
                $query->execute(['recoveryHash' => null, 'recoveryHashDate' => null, 'userId' => $user['id']]);
            } else {
                $response['info'][] = 'Неверная почта или пароль';
            }
        } else {
            $response['info'][] = 'Неверная почта или пароль';
        }
        return $response;
    }
    
    public function RemoveRecoveryHash(int $userId) {
        $response = [
            'status' => false,
            'info' => []
        ];

        // проверяем, есть ли пользователь
        $query = $this->conn->prepare("SELECT `id` FROM `users` WHERE `id` = :userId");
        $query->execute(['userId' => $userId]);
        $query = $query->fetch(PDO::FETCH_ASSOC);
        
        if (isset($query) && !empty($query)) /* пользователь найден */ {
            $query = $this->conn->prepare("UPDATE `{$this->GetTable()}` SET `recoveryHash` = :recoveryHash, `recoveryHashDate` = :recoveryHashDate WHERE `id` = :userId");
            $query->execute(['recoveryHash' => null, 'recoveryHashDate' => null, 'userId' => $userId]);
            $response['status'] = true;
            $response['info'][] = "Хэш восстановления обнулён";
        } else /* пользователь не найден */ {
            $response['info'][] = "Пользователь не найден";
        }
    }

    public function ChangePassword(int $userId, string $newPassword) {
        $response = [
            'status' => false,
            'info' => []
        ];

        $passwordHash = password_hash($newPassword,PASSWORD_DEFAULT);

        // проверяем, есть ли пользователь
        $query = $this->conn->prepare("SELECT `id` FROM `users` WHERE `id` = :userId");
        $query->execute(['userId' => $userId]);
        $query = $query->fetch(PDO::FETCH_ASSOC);
        
        if (isset($query) && !empty($query)) /* пользователь найден */ {
            $query = $this->conn->prepare("UPDATE `users` SET `password` = :password, `recoveryHash` = :recoveryHash, `recoveryHashDate` = :recoveryHashDate WHERE `id` = :userId");
            $query->execute(['userId' => $userId, 'password' => $passwordHash, 'recoveryHash' => null, 'recoveryHashDate' => null]);
            $response['status'] = true;
            $response['info'][] = "Пароль обновлён";
        } else /* пользователь не найден */ {
            $response['info'][] = "Пользователь не найден";
        }

        return $response;
    }

    /**
     * Валидация (проверка на корректность) нового пароля
     * @param string $password новый пароль
     * @param string|null $passwordRepeat повтор пароля
     * @return array [bool $status, array(string) $info]
     */
    public function ValidatePassword(string $password, string|null $passwordRepeat = null) {
        $response = [
            'status' => false,
            'info' => []
        ];
        $minPasswordLength = 6;
        $maxPasswordLength = 50;
        $pattern = "/^[0-9A-Za-z%$#@!?&]{".$minPasswordLength.",".$maxPasswordLength."}$/";
        if (strlen($password) >= $minPasswordLength) {
            if (preg_match($pattern, $password)) {
                if (isset($passwordRepeat)) {
                    if ($password === $passwordRepeat) {
                        $response['status'] = true;
                        $response['info'][] = 'Пароль прошёл валидацию';
                    } else {
                        $response['info'][] = 'Пароли должны совпадать';
                    }
                } else {
                    $response['status'] = true;
                    $response['info'][] = 'Пароль прошёл валидацию';
                }
            } else {
                $response['info'][] = 'Пароль может включать в себя только буквы латинского алфавита, цифры и следующие спец. символы: %$#@!?&';
            }
        } else {
            $response['info'][] = 'Пароль должен состоять минимум из 6 символов';
        }
        return $response;
    }

    /**
     * Валидация фамилии/имени/отчества
     * @param string $text строка
     * @return bool
     */
    public function ValidateName(string $text) {
        $pattern = "/^[a-zA-Zа-яА-ЯёЁ\s\-]{1,".$this->GetMaxNameLength()."}$/";
        $text = $this->trimSpaces($text);
        return preg_match($pattern, $text);
    }

    /**
     * Обрезка повторяющихся симвоов
     * @param string $text строка
     * @return string
     */
    private function trimSpaces(string $text) {
        $text = trim($text);
        return preg_replace('/[\s]{2,}/', ' ', $text);
    }

    /**
     * Регистрация нового пользователя
     * @return array
     */
    public function Registration(string $email, string $password, string $passwordRepeat, string $name, string $surname, string $patronymic = null) {
        $response = [
            'status' => false,
            'info' => []
        ];

        $surname = trim($surname);
        if (!$this->ValidateName($surname)) {
            $response['info'][] = "Поле с фамилией заполнено некорректно";
        }

        $surname = trim($name);
        if (!$this->ValidateName($name)) {
            $response['info'][] = "Поле с фамилией заполнено некорректно";
        }

        if (isset($patronymic)) {
            $patronymic = trim($patronymic);
            if (!$this->ValidateName($patronymic)) {
                $response['info'][] = "Поле с отчеством заполнено некорректно";
            }
        }

        if (count($response['info']) > 0) {
            $response['info'][] = "(Может содержать кириллицу, латиницу, пробелы и дефисы)";
            return $response;
        }

        // проверка паролей
        $response = $this->ValidatePassword($password, $passwordRepeat);

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
                $response['info'][] = 'При регистрации возникла ошибка';
                $response['info'][] = 'Перезагрузите страницу и повторите попытку';
            }

        }
        return $response;
    }
}