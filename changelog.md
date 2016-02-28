# Changelog

## 2.3

You can put on error page under title whatever you want using function `str_replace` and constant `ErrorDumper\Dumpers\Html::TAG_UNDER_TITLE`.

## 2.4

Can choose type of errors which will be caught.

### New constants

* `ErrorDumper\Handlers\RegisterErrorHandler::TYPE_ERRORS`
* `ErrorDumper\Handlers\RegisterErrorHandler::TYPE_EXCEPTIONS`
* `ErrorDumper\Handlers\RegisterErrorHandler::TYPE_SHUTDOWN_ERRORS`
* `ErrorDumper\Handlers\RegisterErrorHandler::TYPE_ALL`

### New argument in methods

* `ErrorDumper\Handlers\RegisterErrorHandler::register()`
* `ErrorDumper\Handlers\RegisterErrorHandlerInterface::register()`
* `ErrorDumper\Magic->registerErrorDumper()`
* `ErrorDumper\Magic->registerErrorCallback()`

New arguments have default value for backward compatibility.