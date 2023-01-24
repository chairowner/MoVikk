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
    public string $keywords;
    private string $mainTable = 'pages';

    public function __construct(PDO $conn = null) {
        $this->current = basename($_SERVER['PHP_SELF'], '.php');
        if (isset($conn)) {
            $pages = $conn->prepare("SELECT * FROM `{$this->mainTable}`");
            $pages->execute();
            $pages = $pages->fetchAll(PDO::FETCH_ASSOC);
            $this->all = $pages;

            $page = $conn->prepare("SELECT * FROM `{$this->mainTable}` WHERE `fileName` = :page");
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
     * @param bool $isGuest проверка, является ли пользователь гостем (no default)
     * @param string $title заголовок страницы (default: null)
     * @param string $description описание страницы (default: null)
     * @param string $keywords ключевые слова (default: null)
     * @param array $pages данные о страницах (['currentPageNumber', 'next', 'prev']) (default: [])
     */
    public function GetHead(bool $isGuest, string $title = null, string $description = null, string $keywords = null, array $pages = []) {
		$response =
            '<!-- Yandex.Metrika counter -->'.
            '<script async type="text/javascript" >'.
                '(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};'.
                'm[i].l=1*new Date();'.
                'for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}'.
                'k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})'.
                '(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");'.
                'ym(90815090, "init", {'.
                    'clickmap:true,'.
                    'trackLinks:true,'.
                    'accurateTrackBounce:true'.
                '});'.
            '</script>'.
            '<noscript><div><img src="https://mc.yandex.ru/watch/90815090" style="position:absolute; left:-9999px;" alt="" /></div></noscript>'.
            '<!-- /Yandex.Metrika counter -->';
        $response .=
            '<meta charset="UTF-8">'.
            '<meta http-equiv="X-UA-Compatible" content="IE=edge">'.
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">'.
            '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">'.
            '<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">'.
            '<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">'.
            // css
            '<link rel="stylesheet" href="/assets/libs/fancybox/css/fancybox.min.css">'.
            '<link rel="stylesheet" href="/assets/common/css/theme-settings.css">'.
            '<link rel="stylesheet" href="/assets/common/css/login-form.css">'.
            '<link rel="stylesheet" href="/assets/common/css/loaders.css">'.
            '<link rel="stylesheet" href="/assets/common/css/main.css">'.
            // js
            '<script defer src="/assets/libs/fancybox/js/fancybox.min.js"></script>'.
            '<script defer src="/assets/libs/jquery/js/jquery.min.js"></script>'.
            '<script defer src="/assets/common/js/Message.js"></script>'.
            '<script defer src="/assets/common/js/main.js"></script>'.
            '<script defer src="/assets/common/js/overlayAct.js"></script>';
        if ($isGuest) {
            $response .=
                '<script defer src="https://www.google.com/recaptcha/api.js?render='.reCAPTCHA_SITE_KEY.'"></script>'.
                '<script defer src="/assets/common/js/loginForm.js"></script>';
        }

        $currentPageNumber = 0;

        if (isset($pages['currentPageNumber'])) {
            $currentPageNumber = (int) $pages['currentPageNumber'];
        }

        if (isset($title) && !empty($title)) {
            if ($currentPageNumber > 1) {
                $response .= '<title>'.$title." - страница ".$pages['currentPageNumber'].'</title>';
            } else {
                $response .= '<title>'.$title.'</title>';
            }
        } else {
            if (isset($this->title) && !empty($this->title)) {
                if ($currentPageNumber > 1) {
                    $response .= '<title>'.$this->title." - страница ".$pages['currentPageNumber'].'</title>';
                } else {
                    $response .= '<title>'.$this->title.'</title>';
                }
            }
        }

        if (isset($description) && !empty($description)) {
            if ($currentPageNumber > 1) {
                $response .= '<meta name="description" content="'.$description.";\nСтраница ".$pages['currentPageNumber'].'">';
            } else {
                $response .= '<meta name="description" content="'.$description.'">';
            }
        } else {
            if (isset($this->description) && !empty($this->description)) {
                if ($currentPageNumber > 1) {
                    $response .= '<meta name="description" content="'.$this->description.";\nСтраница ".$pages['currentPageNumber'].'">';
                } else {
                    $response .= '<meta name="description" content="'.$this->description.'">';
                }
            }
        }

        if (isset($keywords) && !empty($keywords)) {
            $response .= '<meta name="keywords" content="'.$keywords.'">';
        } else {
            if (isset($this->keywords) && !empty($this->keywords)) {
                $response .= '<meta name="keywords" content="'.$this->keywords.'">';
            }
        }

        if (!empty($pages)) {
            if (isset($pages['next'])) {
                $response .= '<link href="'.$pages['next'].'" rel="next">';
            }
            if (isset($pages['prev'])) {
                $response .= '<link href="'.$pages['prev'].'" rel="prev">';
            }
        }

        return $response;
    }
    
    /**
     * @param string $url ссылка для редиректа
     * @param bool $outdoor редирект во вне
     */
    function Redirect(string $url = "", bool $outdoor = false) {
        if ($outdoor) {
            header("Location: {$url}");
        }else {
            header("Location: /{$url}");
        }
        exit;
    }
}