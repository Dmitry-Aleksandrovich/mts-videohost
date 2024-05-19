<?php

    static $con;
    
    if(!$con){
    $config = include __DIR__.'/config.php';
    $con = mysqli_connect($config['host'], $config['username'], $config['password'], $config['database'])
    or die("Database error!");
    }
    return $con;

?>