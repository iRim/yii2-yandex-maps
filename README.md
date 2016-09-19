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
<?= \irim\yandex\maps\YandexMaps::widget([
    'id'=>'myNewMap',
    'style'=>'width:400px;heigth:200px',
    'map'=>[
        'type'=>\irim\yandex\maps\YandexMaps::TYPE_HYBRID,
        'controls'=>FALSE,
        'zoom'=>16,
        'center'=>[50.450418,30.523541]
    ],
    'placemarks' => [
        [
            'hint'=>'проспект Ленина, 1',
            'icon'=>[
                'iconLayout'=>'default#image',
                'iconImageHref'=>'https://sandbox.api.maps.yandex.net/examples/ru/2.1/icon_customImage/images/myIcon.gif',
                'iconImageSize'=>[30, 42],
                'iconImageOffset'=>[-3, -42]
            ],
            'ballon'=>'Какойто балун :)'
        ],
        ['coords'=>[55.858585, 37.48498]],
        ['coords'=>[55.723123, 37.406067]],
        ['coords'=>[55.844708, 37.74887]],
        ['coords'=>[55.781329, 37.442781]],
        ['coords'=>[55.616448, 37.452759]],
        ['coords'=>[55.803972, 37.65961]],
        ['coords'=>[55.691046, 37.711026]],
        ['coords'=>[55.803972, 37.65961]],
        ['coords'=>[55.691046, 37.711026]],
        ['coords'=>[55.691046, 37.711026]],
    ],
    'clusters'=>TRUE
]););
?>```