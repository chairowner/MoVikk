<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_PRODUCT = new Product($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => []
];
$separator = ';';

if (!isset($_POST['action']) || $_POST['action'] !== "edit") {
    $response['status'] = false;
    $response['info'][] = 'Ошибка запроса';
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}
unset($_POST['action']);

$_POST['href'] = null;

if (isset($_POST['categoryId'])) {
    $_POST['categoryId'] =
        trim($_POST['categoryId']) !== "" ?
            (int) $_POST['categoryId'] : null;
        if (!isset($_POST['categoryId'])) {
            $response['info'][] = "Укажите категорию";
            $response['status'] = false;
        } elseif ($_POST['categoryId'] === 0) {
            $response['info'][] = "Неверная категория";
            $response['status'] = false;
        }
} else {
    $_POST['categoryId'] = null;
    $response['info'][] = "Укажите категорию";
    $response['status'] = false;
}

if (isset($_POST['countryId'])) {
    $_POST['countryId'] =
        trim($_POST['countryId']) !== "" ?
            (int) $_POST['countryId'] : null;
} else {
    $_POST['countryId'] = null;
}

if (isset($_POST['instructionId'])) {
    $_POST['instructionId'] =
        trim($_POST['instructionId']) !== "" ?
            (int) $_POST['instructionId'] : null;
} else {
    $_POST['instructionId'] = null;
}

if (isset($_POST['payment_object_id'])) {
    $_POST['payment_object_id'] =
        trim($_POST['payment_object_id']) !== "" ?
            (int) $_POST['payment_object_id'] : null;
    if (isset($_POST['payment_object_id']) && !empty($_POST['payment_object_id'])) {
        if ($_POST['payment_object_id'] < 1) {
            $response['info'][] = "Указан неверный признака предмета расчёта";
            $response['status'] = false;
        }
    } else {
        $response['info'][] = "Укажите признака предмета расчёта";
        $response['status'] = false;
    }
} else {
    $response['info'][] = "Укажите признака предмета расчёта";
    $response['status'] = false;
}

if (isset($_POST['name'])) {
    $_POST['name'] =
        trim($_POST['name']) !== "" ?
            trim($_POST['name']) : null;
    if (isset($_POST['name'])) {
        $_POST['href'] = translitUrl(trim($_POST['name']));
    } else {
        $response['info'][] = "Укажите название товара";
        $response['status'] = false;
    }
} else {
    $_POST['name'] = null;
    $response['info'][] = "Укажите название товара";
    $response['status'] = false;
}

if (isset($_POST['description'])) {
    $_POST['description'] =
        trim($_POST['description']) !== "" ?
            trim($_POST['description']) : null;
    if (!isset($_POST['description'])) {
        $response['info'][] = "Описание не может быть пустым";
        $response['status'] = false;
    }
} else {
    $_POST['description'] = null;
    $response['info'][] = "Описание не может быть пустым";
    $response['status'] = false;
}

if (isset($_POST['height'])) {
    $_POST['height'] =
        trim($_POST['height']) !== "" ?
            (float) $_POST['height'] : null;
    if (isset($_POST['height'])) {
        if ($_POST['height'] <= 0) {
            $response['info'][] = "Значение высоты не может равняться или быть меньше нуля";
            $response['status'] = false;
        }
    }
} else {
    $_POST['height'] = null;
}

if (isset($_POST['width'])) {
    $_POST['width'] =
        trim($_POST['width']) !== "" ?
            (float)$_POST['width'] : null;
    if (isset($_POST['width'])) {
        if ($_POST['width'] <= 0) {
            $response['info'][] = "Значение ширины не может равняться или быть меньше нуля";
            $response['status'] = false;
        }
    }
} else {
    $_POST['width'] = null;
}

if (isset($_POST['length'])) {
    $_POST['length'] =
        trim($_POST['length']) !== "" ?
            (float)$_POST['length'] : null;
    if (isset($_POST['length'])) {
        if ($_POST['length'] <= 0) {
            $response['info'][] = "Значение длины не может равняться или быть меньше нуля";
            $response['status'] = false;
        }
    }
} else {
    $_POST['length'] = null;
}

if (isset($_POST['features'])) {
    $_POST['features'] =
        trim($_POST['features']) !== "" ?
            trim($_POST['features']) : null;
    if (isset($_POST['features']) && strpos($_POST['features'], $separator) !== false) {
        $_POST['features'] = explode($separator,$_POST['features']);
        for ($i = 0; $i < count($_POST['features']); $i++) {
            $_POST['features'][$i] = trim($_POST['features'][$i]);
            if ($_POST['features'][$i] === "") unset($_POST['features'][$i]); 
        }
        $_POST['features'] = array_values($_POST['features']);
        $_POST['features'] = implode($separator,$_POST['features']);
    }
} else {
    $_POST['features'] = null;
}

if (isset($_POST['techSpec'])) {
    $_POST['techSpec'] =
        trim($_POST['techSpec']) !== "" ?
            trim($_POST['techSpec']) : null;
    if (isset($_POST['techSpec']) && strpos($_POST['techSpec'], $separator) !== false) {
        $_POST['techSpec'] = explode($separator,$_POST['techSpec']);
        for ($i = 0; $i < count($_POST['techSpec']); $i++) {
            if (strpos($_POST['techSpec'][$i],':')!==false) {
                $_POST['techSpec'][$i] = trim($_POST['techSpec'][$i]);
            } else {
                unset($_POST['techSpec'][$i]);
            }
        }
        $_POST['techSpec'] = array_values($_POST['techSpec']);
        $_POST['techSpec'] = implode($separator,$_POST['techSpec']);
    }
} else {
    $_POST['techSpec'] = null;
}

if (isset($_POST['count'])) {
    $_POST['count'] =
        trim($_POST['count']) !== "" ?
            (int)$_POST['count'] : 0;
    if ($_POST['count'] < 0) $_POST['count'] = 0;
} else {
    $_POST['count'] = 0;
}

if (isset($_POST['price'])) {
    if (trim($_POST['price']) !== "") {
        $_POST['price'] = (float)$_POST['price'];
        $_POST['price'] = number_format($_POST['price'], 2, '.', '');
        $_POST['price'] = (float)$_POST['price'];
    } else {
        $_POST['price'] = null;
    }
    if (!isset($_POST['price'])) {
        $response['info'][] = "Укажите цену товара";
        $response['status'] = false;
    } elseif ($_POST['price'] <= 0) {
        $response['info'][] = "Цена товара не может равняться или быть меньше нуля";
        $response['status'] = false;
    }
} else {
    $_POST['price'] = null;
    $response['info'][] = "Укажите цену товара";
    $response['status'] = false;
}

if (isset($_POST['sale'])) {
    $_POST['sale'] =
        trim($_POST['sale']) !== "" ?
            (int)$_POST['sale'] : 0;
    if ($_POST['sale'] < 0) $_POST['sale'] = 0;
} else {
    $_POST['sale'] = 0;
}

if (isset($_FILES) && !empty($_FILES)) {
    $_POST['files'] = $_FILES['files'];
    for ($i = 0; $i < count($_POST['files']['error']); $i++) {
        if ($_POST['files']['error'][$i] !== 0 || !in_array($_POST['files']['type'][$i], $_PRODUCT->fileTypes)) {
            unset($_POST['files']['error'][$i]);
            unset($_POST['files']['name'][$i]);
            unset($_POST['files']['size'][$i]);
            unset($_POST['files']['tmp_name'][$i]);
            unset($_POST['files']['type'][$i]);
        }
    }
} else {
    $_POST['files'] = [];
}

if ($response['status']) $response = $_PRODUCT->Edit($_POST);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return