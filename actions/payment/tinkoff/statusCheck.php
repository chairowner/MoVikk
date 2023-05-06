<?php
set_include_path(__DIR__."/../../../");
// require_once(get_include_path()."includes/autoload.php");
// $_ORDER = new Order($conn);
try {
    // $response = $_ORDER->CheckStatesFromTinkoff();
} catch (Exception $th) {
    // $response = ['error' => $th->getMessage()];
}

file_put_contents(get_include_path()."text.log", "[".date("Y-m-d H:i:s")."]\r\n".get_include_path()."includes/autoload.php"."\r\n\r\n", FILE_APPEND);

exit(json_encode($response,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));