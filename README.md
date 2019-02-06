WakeOnWeb Errors Extra Library
=============================

- Error dictionary

## Installation

composer.json

```json
    "require": {
        "wakeonweb/errors-extra-library": "~1.0"
    }
```


AppKernel

```php
$bundles[] = new WakeOnWeb\ErrorsExtraLibrary\App\Bundle\WakeonwebErrorsExtraLibraryBundle();
```

## Configuration

Override http status code, show error messages on exceptions:

```
wakeonweb_errors_extra_library:
    force_format: json
    exception:
        http_status_codes:
            Pagerfanta\Exception\OutOfRangeCurrentPageException: 400
        show_messages:
            - Pagerfanta\Exception\OutOfRangeCurrentPageException
        log_levels:
            Pagerfanta\Exception\OutOfRangeCurrentPageException: notice
            Pagerfanta\Exception\NotValidCurrentPageException: error

```

Log level values as defined by [PSR-3](http://www.php-fig.org/psr/psr-3/#basics) (from [RFC 5424](https://tools.ietf.org/html/rfc5424)).

## Exception listener

The bundle adds an exception listener to format the response when the requested response format is `application/json`.

### Default JSON response

 - `code` : HTTP status code
 - `message` : HTTP reason phrase

#### Example

```
{
    "code": 403,
    "message": "Forbidden",
}
```
