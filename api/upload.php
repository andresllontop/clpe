<?php 

if( isset($_FILES['avatar']) ){
    echo("holaaaa");
    $original=$_FILES['avatar'];
    move_uploaded_file($original['tmp_name'],"../adjuntos/imagen/hola.webm");
} else {
    echo("no hay");
}
