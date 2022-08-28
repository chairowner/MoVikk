<?php
/**
 * @param string $current открытая страница
 * @param string $title заголовок страницы
 * @param string $description описание страницы
 */
class Page {
    public array $all;
    public string $current;
    public string $title;
    public string $description;

    public function __construct(PDO $conn = null) {
        $this->current = basename($_SERVER['PHP_SELF'], '.php');
        if (isset($conn)) {
            $pages = $conn->prepare("SELECT * FROM `pages`");
            $pages->execute();
            $pages = $pages->fetchAll(PDO::FETCH_ASSOC);
            $this->all = $pages;

            $page = $conn->prepare("SELECT * FROM `pages` WHERE `fileName` = :page");
            $page->execute(['page' => $this->current]);
            $page = $page->fetch(PDO::FETCH_ASSOC);
            if (isset($page) && !empty($page)) {
                if (isset($page['title']) &&
                    !empty($page['title'])) {
                    $this->title = trim($page['title']);
                }
                if (isset($page['description']) &&
                    !empty($page['description'])) {
                    $this->description = trim($page['description']);
                }
                if (isset($page['keywords']) &&
                    !empty($page['keywords'])) {
                    $this->keywords = trim($page['keywords']);
                }
            }
        }
    }

    /**
     * Функция формирования внутренностей тега <head>
     */
    public function getHead(string $title = null, string $description = null, string $keywords = null) {
		$response =
        '<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="stylesheet" href="/assets/css/fontawesome.all.min.css">
        <link rel="stylesheet" href="/assets/css/fancybox.min.css">
        <link rel="stylesheet" href="/assets/css/main.css">
        <link rel="stylesheet" href="/assets/css/adaptive.css">';

        if (isset($title) && !empty($title)) {
            $response .= '<title>'.$title.'</title>';
        } else {
            if (isset($this->title) && !empty($this->title)) {
                $response .= '<title>'.$this->title.'</title>';
            }
        }

        if (isset($description) && !empty($description)) {
            $response .=
            '<meta name="description" content="'.$description.'">';
        } else {
            if (isset($this->description) && !empty($this->description)) {
                $response .=
                '<meta name="description" content="'.$this->description.'">';
            }
        }

        if (isset($keywords) && !empty($keywords)) {
            $response .=
            '<meta name="keywords" content="'.$keywords.'">';
        } else {
            if (isset($this->keywords) && !empty($this->keywords)) {
                $response .=
                '<meta name="keywords" content="'.$this->keywords.'">';
            }
        }

        return $response;
    }
    
    /**
     * @param string $url ссылка для редиректа
     */
    function redirect(string $url) {
        Header("Location: /{$url}");
        exit;
    }
}

$_PAGE = isset($conn) ? new Page($conn) : new Page();