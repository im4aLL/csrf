## CSRF token validator
CSRF protection - PHP security classes to avoid vulnerabilities

### Installation

```ssh
composer require hadi/csrf
```

### Usage
Add CSRF token to form

```php
<?php
session_start();
require_once __DIR__ . '/PATH_TO_YOUR_AUTOLOAD/vendor/autoload.php';

$csrf = new \Hadi\Csrf();
?>
<form action="" method="post">
    <label for="name">Name</label>
    <input type="text" name="name" id="name">

    <label for="age">Age</label>
    <input type="text" name="age" id="age">
    
    <input type="hidden" name="_token" value="<?= $csrf->token() ?>">
    <button type="submit" name="submit">Submit</button>
</form>
```

Then check CSRF token in your form submission area - 

```php
session_start();
require_once __DIR__ . '/PATH_TO_YOUR_AUTOLOAD/vendor/autoload.php';

$csrf = new \Hadi\Csrf();

if(isset($_POST['submit'])) {
    if($csrf->validRequest()) {
        // Valid request
    }
    else {
        // invalid request
    }
}

$csrf->reset(); // or $csrf->deleteToken();
```

Have fun!
