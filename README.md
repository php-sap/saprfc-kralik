# PHP/SAP implementation for Gregor Kraliks sapnwrfc module

[![License: MIT][license-mit]](LICENSE)
[![Build Status][travis-badge]][travis-ci]
[![Maintainability][maintainability-badge]][maintainability]
[![Test Coverage][coverage-badge]][coverage]

This repository implements the [PHP/SAP][phpsap] interface for [Gregor Kraliks `sapnwrfc` PHP module][kralik].

## Usage

```sh
composer require php-sap/saprfc-kralik:^2.0
```

```php
<?php
use phpsap\saprfc\SapRfcConfigA;
use phpsap\saprfc\SapRfcConnection;

$result = (new SapRfcConnection(new SapRfcConfigA([
  'ashost' => 'sap.example.com',
  'sysnr' => '001',
  'client' => '002',
  'user' => 'username',
  'passwd' => 'password'
])))
    ->prepareFunction('MY_COOL_SAP_REMOTE_FUNCTION')
    ->setParam('INPUT_PARAM', 'some input value')
    ->invoke();
```

For further documentation, please read the documentation on [PHP/SAP][phpsap]!

[phpsap]: https://php-sap.github.io
[kralik]: https://github.com/gkralik/php7-sapnwrfc "SAP NW RFC SDK extension for PHP7"
[license-mit]: https://img.shields.io/badge/license-MIT-blue.svg
[travis-badge]: https://travis-ci.org/php-sap/saprfc-kralik.svg?branch=master
[travis-ci]: https://travis-ci.org/php-sap/saprfc-kralik
[maintainability-badge]: https://api.codeclimate.com/v1/badges/d94f95bad2040c993c65/maintainability
[maintainability]: https://codeclimate.com/github/php-sap/saprfc-kralik/maintainability
[coverage-badge]: https://api.codeclimate.com/v1/badges/d94f95bad2040c993c65/test_coverage
[coverage]: https://codeclimate.com/github/php-sap/saprfc-kralik/test_coverage
