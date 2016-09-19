<?php
namespace irim\yandex\maps;

use yii\helpers\ArrayHelper;

class YandexMaps extends \yii\base\Widget{
    
    const TYPE_DEFAULT = 'yandex#map',
        TYPE_SATELLITE = 'yandex#satellite',
        TYPE_HYBRID = 'yandex#hybrid';


    public $placemarks = [],$id = 'map',$style = 'width:100%;height:400px',$map = [],$clusters = NULL;
    private $_options = [
        'map'=>[
            'center'=>[55.75396,37.620393],
            'zoom'=>10,
            'type'=>self::TYPE_DEFAULT,
            'controls'=>[],
        ],
        'placemarks'=>[],
        'clusters'=>[]
    ];

    public function init(){
        parent::init();
        foreach (ArrayHelper::toArray($this) as $k=>$v){
            $this->_options[$k] = is_array($v)?ArrayHelper::merge($this->_options[$k],$v):$v;
        }
        $this->registerClientScript();
    }

    public function run(){
        return $this->render('view',['widget'=>$this->_options]);
    }

    public function registerClientScript(){
        $js = NULL;
        $view = $this->getView();
        YandexMapsAsset::register($view);
        
        $map_options = [];
        foreach ($this->_options['map'] as $k=>$v){if(!empty($v)){$map_options[] = $k.':'.(is_array($v)?'['.implode(',',$v).']':'"'.$v.'"');}}
        $js .= 'myMap = new ymaps.Map("'.$this->_options['id'].'",{'.implode(',',$map_options).'})';
        if(!empty($this->_options['clusters'])){
            $js .= ',clusterer = new ymaps.Clusterer({preset:"islands#invertedVioletClusterIcons",groupByCoordinates: false,clusterDisableClickZoom: true,clusterHideIconOnBalloonOpen: false,geoObjectHideIconOnBalloonOpen: false}),geoObjects = []';
        }
        $js .= ';';
        if(!empty($this->_options['placemarks']) OR $this->_options['placemarks']!==FALSE){
            $placemark = 'new ymaps.Placemark(%s,%s,%s);';
            if(is_array($this->_options['placemarks']) and count($this->_options['placemarks'])>0){
                $placemark = sprintf($placemark,'(item.coords?(item.coords instanceof Array?item.coords:item.coords.split(",")):myMap.getCenter())','((item.hint || item.ballon)?{hintContent:item.hint,balloonContent:item.ballon}:{})','item.hint','item.ballon','item.icon');
                $js .= 'var placemarks = '.\yii\helpers\Json::encode($this->placemarks).',geo = myMap.geoObjects;
                    placemarks.forEach(function(item,i){'.(!empty($this->_options['clusters'])?'geoObjects[i] = '.$placemark:'geo.add('.$placemark.')').';});';
            }
            else{
                $placemark = sprintf($placemark,'myMap.getCenter()','""','""','""');
                $js .= !empty($this->_options['clusters'])?'geoObjects[] = '.$placemark:'myMap.geoObjects.add('.$placemark.')';
            }
        }
        $js .= 'console.log(geoObjects);';
        if(!empty($this->_options['clusters'])){
            $js .= 'clusterer.options.set({gridSize: 80,clusterDisableClickZoom: true});
                clusterer.add(geoObjects);
                myMap.geoObjects.add(geoObjects);';
        }
        $js .= 'myMap.setBounds(clusterer.getBounds(), {
            checkZoomRange: true
        });';
        $view->registerJs('ymaps.ready(function(){'.$js.'});',\yii\web\View::POS_END);
    }
}

