<?php
namespace irim\yandex\maps;

class YandexMaps extends \yii\base\Widget{
    
    public $addresses;
    public $cityLat;
    public $cityLon;

    public function init(){
        parent::init();
        $this->addresses = \yii\helpers\ArrayHelper::toArray($this->addresses);
        $this->registerClientScript();
    }

    public function run(){
        return $this->render('view',['widget' => $this]);
    }

    public function registerClientScript(){
        $countPlaces = count($this->addresses);
        $items  = [];
        $i      = 0;
        foreach ($this->addresses as $one) {
            $items[$i]['address']   = $one['address'];
            $items[$i]['latitude']  = $one['latitude'];
            $items[$i]['longitude'] = $one['longitude'];
            $i++;
        }
        $view = $this->getView();

        YandexMapsAsset::register($view);

        $js = '
            var myMap,myPlacemark;
            ymaps.ready(function(){     
                myMap = new ymaps.Map("map", {
                    center: ['.$this->cityLat.','.$this->cityLon.'],
                    zoom: 10,
                    controls: []
                });
                
                myMap.behaviors.disable("scrollZoom");
                myMap.controls.add("zoomControl", { top: 75, left: 5 });
        
                var addresses = "'.json_encode($items).'";
        
                for (var i = 0; i < $countPlaces; i++) {
                    myPlacemark = new ymaps.Placemark([addresses[i]["latitude"], addresses[i]["longitude"]], { 
                        hintContent: "" + addresses[i]["address"] + "", 
                    },
                    {iconColor: "#ff0000"});
                    myMap.geoObjects.add(myPlacemark);
                }
            });
        ';
        $view->registerJs($js);
    }
}