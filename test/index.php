<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

function dd($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

$csrf = new \Hadi\Csrf();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CSRF Token test ground</title>
</head>
<body>
    
    <h3>CSRF test ground</h3>
    
    <form action="action.php" method="post">
        <label for="name">Name</label>
        <input type="text" name="name" id="name">

        <label for="age">Age</label>
        <input type="text" name="age" id="age">
        
        <input type="hidden" name="_token" value="<?= $csrf->token() ?>">
        <button type="submit" name="submit">Submit</button>
    </form>
    
</body>
</html>
