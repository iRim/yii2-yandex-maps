<?php
namespace irim\yandex\maps;

class YandexMaps extends \yii\base\Widget{
    
    public $datas = [],
        $map_id = 'map',
        $coords = '55.75396,37.620393',
        $zoom = 10,
        $controls = [];

    public function init(){
        parent::init();
        $this->datas = \yii\helpers\ArrayHelper::toArray($this->datas);
        $this->registerClientScript();
    }

    public function run(){
        return $this->render('view',['widget' => $this]);
    }

    public function registerClientScript(){
        $view = $this->getView();
        YandexMapsAsset::register($view);
        $js = '
            ymaps.ready(function(){
                myMap = new ymaps.Map("'.$this->map_id.'",{
                    center: ['.$this->coords.'],
                    zoom:'.$this->zoom.',
                    controls:['.implode(',',$this->controls).']
                });
                
                myMap.behaviors.disable("scrollZoom");
                myMap.controls.add("zoomControl", { top: 75, left: 5 });
        
                var datas = '.\yii\helpers\Json::encode($this->datas).';
                datas.forEach(function(item){
                    myMap.geoObjects.add(new ymaps.Placemark(item.coords.split(","),{hintContent:item.hint},{iconColor: "#ff0000"}));
                });
            });';
        $view->registerJs($js);
    }
}