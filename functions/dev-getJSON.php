<?php
function getJSON($array, bool $isPretty = false) {
    if ($isPretty) {
        echo('<pre>');
        echo(json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo('</pre>');
    } else {
        echo(json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}