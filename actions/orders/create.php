<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_ORDER = new Order($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$response = [
    'status' => false,
    'info' => []
];

$userId = $_USER->GetId();
$address = [];
$fullName = [];
$phone = null;
$userComment = null;

// заполнение контактного номера
if (isset($_POST['phone'])) {
    $phone = trim($_POST['phone']);
    if ($phone === "") {
        $phone = null;
        $response['info'][] = "Контактный телефон не может быть пустым";
    }
} else {
    $phone = null;
    $response['info'][] = "Заполните контактный телефон";
}

// заполнение адреса
if (isset($_POST['postcode'])) {
    $_POST['postcode'] = trim($_POST['postcode']);
    if ($_POST['postcode'] === "") {
        $response['info'][] = "Укажите почтовый индекс";
    } else {
        $address[] = $_POST['postcode'];
    }
} else {
    $response['info'][] = "Укажите почтовый индекс";
}
if (isset($_POST['country'])) {
    $_POST['country'] = trim($_POST['country']);
    if ($_POST['country'] === "") {
        $response['info'][] = "Укажите страну";
    } else {
        $address[] = $_POST['country'];
    }
} else {
    $response['info'][] = "Укажите страну";
}
if (isset($_POST['region'])) {
    $_POST['region'] = trim($_POST['region']);
    if ($_POST['region'] === "") {
        $response['info'][] = "Укажите регион";
    } else {
        $address[] = $_POST['region'];
    }
} else {
    $response['info'][] = "Укажите регион";
}
if (isset($_POST['place'])) {
    $_POST['place'] = trim($_POST['place']);
    if ($_POST['place'] === "") {
        $response['info'][] = "Укажите населённый пункт";
    } else {
        $address[] = $_POST['place'];
    }
} else {
    $response['info'][] = "Укажите населённый пункт";
}
if (isset($_POST['street'])) {
    $_POST['street'] = trim($_POST['street']);
    if ($_POST['street'] === "") {
        $response['info'][] = "Укажите улицу";
    } else {
        $address[] = $_POST['street'];
    }
} else {
    $response['info'][] = "Укажите улицу";
}
if (isset($_POST['building'])) {
    $_POST['building'] = trim($_POST['building']);
    if ($_POST['building'] === "") {
        $response['info'][] = "Укажите дом/строение";
    } else {
        $address[] = $_POST['building'];
    }
} else {
    $response['info'][] = "Укажите дом/строение";
}
if (isset($_POST['block'])) {
    $_POST['block'] = trim($_POST['block']);
    if ($_POST['block'] !== "") {
        $address[] = $_POST['block'];
    }
}
if (isset($_POST['cell'])) {
    $_POST['cell'] = trim($_POST['cell']);
    if ($_POST['cell'] !== "") {
        $address[] = $_POST['cell'];
    }
}

// заполнение комментария
if (isset($_POST['userComment'])) {
    $userComment = trim($_POST['userComment']);
    if ($userComment === "") {
        $userComment = null;
    }
}

$fullNameValidationError = false;

// заполнение ФИО
if (isset($_POST['surname'])) {
    $_POST['surname'] = trim($_POST['surname']);
    if ($_POST['surname'] === "") {
        $response['info'][] = "Укажите фамилию получателя";
    } else {
        if ($_USER->ValidateName($_POST['surname'])) {
            $fullName[] = $_POST['surname'];
        } else {
            $fullNameValidationError = true;
            $response['info'][] = "Поле с фамилией заполнено некорректно";
        }
    }
} else {
    $response['info'][] = "Укажите фамилию получателя";
}
if (isset($_POST['name'])) {
    $_POST['name'] = trim($_POST['name']);
    if ($_POST['name'] === "") {
        $response['info'][] = "Укажите имя получателя";
    } else {
        if ($_USER->ValidateName($_POST['name'])) {
            $fullName[] = $_POST['name'];
        } else {
            $fullNameValidationError = true;
            $response['info'][] = "Поле с именем заполнено некорректно";
        }
    }
} else {
    $response['info'][] = "Укажите имя получателя";
}
if (isset($_POST['patronymic'])) {
    $_POST['patronymic'] = trim($_POST['patronymic']);
    if ($_POST['patronymic'] !== "") {
        if ($_USER->ValidateName($_POST['patronymic'])) {
            $fullName[] = $_POST['patronymic'];
        } else {
            $fullNameValidationError = true;
            $response['info'][] = "Поле с отчеством заполнено некорректно";
        }
    }
}

if ($fullNameValidationError) {
    $response['info'][] = "(Может содержать кириллицу, латиницу, пробелы и дефисы)";
}

// если всё хорошо, то создаём заказ
if (count($response['info']) === 0) {
    $response = $_ORDER->Create($userId,$phone,implode(" ", $fullName),implode(", ", $address),$userComment);
    if ($response['status']) {
        // очищаем корзину
        $remove_cart_response = $_CART->RemoveAll($userId);
        $response['info'][] = $remove_cart_response['info'][0];
    }
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return