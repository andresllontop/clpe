<?php 
if (!empty($_POST)) {
    // session_start();
    // $SID=session_id();
    // print_r($SID);
    // print_r($_POST);
    echo(json_encode($_POST));
    
} else {
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}


?>