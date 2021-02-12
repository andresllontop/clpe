<?php

$beanResource = $routes->getResourceForContainerApi();
//INCLUIMOS LA API
$array_resource = $beanResource->path_resource;
if ($array_resource != "") {
    foreach ($array_resource as $path_resources) {
        include $path_resources;
    }}
