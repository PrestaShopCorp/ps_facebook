![PrestaShop Facebook logo](views/img/logo-wordmark.png)

# PrestaShop Facebook (ps_facebook)

![PHP tests](https://github.com/PrestaShopCorp/ps_facebook/workflows/PHP%20tests/badge.svg)
![JS tests](https://github.com/PrestaShopCorp/ps_facebook/workflows/JS%20tests/badge.svg)

## Installation

Use `make build` to install dependencies (or `make docker-build` to run it within docker).

Check other commands with `make help`.

## Requirements

This module is compliant with PHP 5.6+ and PrestaShop 1.7, (tested with PS 1.7.2+ before each release).

You need a facebook developper account added to the PrestaShop Social Media app (ID:726899634800479) to manage its settings.

## Usage

Install module and connect to FBE in module BO

## Delivery

### Automatic

This package should be automatically delivered by the CI/CD, see the [github workflows](./github/workflows).
Zips should be available for each [releases](./releases).

### Manual

1. Fill up `.env.inte` and `.env.prod` files
2. Use `make bundle` to build up deliverable zips for integration and production purpose.
3. Find zips within the `./dist` directory

## About

### Compliancy with PrestaShop 1.6

This module is not compliant with PS 1.6 as some Pixel events could not be implemented properly on this version (i.e `CustomizeProduct`).
This avoids potential misunderstanding about mismatching behavior of the module with different versions of PrestaShop.

### Links

- [List of standard Pixel events](https://developers.facebook.com/docs/facebook-pixel/reference/)

### Development

The main branch receiving all kind of contributions (bug fixes, improvements & new features) is the `master` branch.

In the future we may maintain several branches at the same time, for instance to fix the current version in production while we prepare new features for the next release.
These branches would be defined as `[1-9]*.[1-9]*.x` (for instance `1.4.x`).

* **Running with dev dependencies**

```
composer install --dev
```

* **Working with VueJS app**

To set the build of the VueJS app in development mode and watching your changes:

```bash
cd _dev
npm run dev
```

* **Replacing config values of the project**

Some values of the Config class can be overwriten by having your own environment variables.

You can for instance have your own `.env` at the root of this project to replace the Facebook App ID
or switch the API URLs to another domain.


* **Using Pixel event in other modules**

You can call custom Pixel event by using hook: actionFacebookCallPixel

You also need to add some params in hook call

*Required:* 
+ eventName
+ module

*Optional:*
+ id_product
+ id_product_attribute

Example:    
```
Hook::exec('actionFacebookCallPixel' ,['eventName' => 'AddToWishlist', 'module' => 'wishlist', 'id_product' => $productId, 'id_product_attribute' => $idProductAttribute]);
```


### Tests

This module follows [recommandations of the PrestaShop devdocs](https://devdocs.prestashop.com/1.7/modules/testing/) and is checked by PHP-CS-Fixer, PHPStan and PHPUnit before each release.

Two sets of tests have been implemented in this module:

- **Unit tests**

```
vendor/bin/phpunit tests/unit/
```

- **Integration tests**

These tests run the calls to Facebook API to make sure the data the module relies on is still valid.
It requires preliminary configuration, by setting your FBE configuration in a JSON config file.

```bash
cp tests/integration/config.json.dist tests/integration/config.json
# Edit your config.json, then
vendor/bin/phpunit tests/integration/ -v
```
