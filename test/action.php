<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

function dd($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

$csrf = new \Hadi\Csrf();

if(isset($_POST['submit'])) 
{
    if($csrf->validRequest()) {
        dd('<span style="color: green">Token is valid. Here is token - '.$csrf->getToken().'</span>');

        echo '<p>To check CSRF working try POST request with name "submit" or even try reload it will show you token invalid! So you can prevent multiple form submission!</p>';
    }
    else {
        dd('<span style="color: red">Token is not valid!</span>');
    }
}

$csrf->reset(); // or $csrf->deleteToken();
