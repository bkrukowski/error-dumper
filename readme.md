# Pretty error dumper for PHP

## Requirements

* PHP >= 5.3 (also 7.0 is supported)
* no dependencies
* for nicer dump of variables you can add **symfony/var-dumper** to project

## Installation

### If you use composer

Add **bkrukowski/error-dumper** to dependencies.

### In other cases

Add below code to project:

```php
$pathToLib = 'here put path to error-dumper library';
include $pathToLib . 'src' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
```

## Usage

**[Unsafe]** Below code is enough:

```php
\ErrorDumper\Magic::registerErrorDumper();
```

**[Safe]** But you should write something like this (because **all variables like credentials are visible**, when exception or error occur)

```php
if ($isInTestEnvironment)
{
    \ErrorDumper\Magic::registerErrorDumper();
}
else
{
    \ErrorDumper\Magic::registerErrorCallback(function ($e) {
        /** @var \Exception|\Throwable $e */
        // save error somewhere
        exit(1);
    });
}
```

## Editors

Numbers of lines and names of files are clickable, but you have to set proper editor. Default editor is PhpStorm. If you use something else, put edtor object as argument in `registerErrorDumper` method.

```php
\ErrorDumper\Magic::registerErrorDumper(new \ErrorDumper\Editors\MacVim());
```

### Supported editors

* MacVim
* PhpStorm
* TextMate

## Preview

### Error

![Preview exception](resources/img/preview-exception.png)

### Arguments

![Preview arguments](resources/img/preview-arguments.png)