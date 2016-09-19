Yandex Maps
===========
Работа с картами

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist irim/yii2-yandex-maps "*"
```

or add

```
"irim/yii2-yandex-maps": "*"
```

to the require section of your `composer.json` file.


Usage
-----

```php
<?= \irim\yandex\maps\YandexMaps::widget(); ?>```