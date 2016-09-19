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
        foreach ($this->_options['map'] as $k=>$v){
            if($k=='controls' and $v===FALSE){$v = [];}
            $map_options[] = $k.':'.(is_array($v)?'['.implode(',',$v).']':'"'.$v.'"');
        }
        
        ob_start();
        ?>
        myMap = new ymaps.Map("<?=$this->_options['id']?>",{<?=implode(',',$map_options)?>})<?if(!empty($this->_options['clusters'])){?>,clusterer = new ymaps.Clusterer(),geoObjects = []<?}else{?>,geoCollection = new ymaps.GeoObjectCollection()<?}?>;
        <?if(!empty($this->_options['placemarks']) OR $this->_options['placemarks']!==FALSE){?>
            <?if(is_array($this->_options['placemarks']) and count($this->_options['placemarks'])>0){?>
                var placemarks = <?=\yii\helpers\Json::encode($this->_options['placemarks'])?>;
                placemarks.forEach(function(item,i){
                    pm = new ymaps.Placemark(
                        (item.coords?(item.coords instanceof Array?item.coords:item.coords.split(",")):myMap.getCenter()),
                        ((item.hint || item.ballon)?{hintContent:item.hint,balloonContent:item.ballon}:{}),
                        (item.icon?item.icon:{})
                    );
                    <?if(!empty($this->_options['clusters'])){?>geoObjects.push(pm);<?}else{?>geoCollection.add(pm);<?}?>
                });
            <?}else{?>
                pm = new ymaps.Placemark(myMap.getCenter());
                <?if(!empty($this->_options['clusters'])){?>geoObjects.push(pm);<?}else{?>geoCollection.add(pm);<?}?>
            <?}}?>
            <?if(!empty($this->_options['clusters'])){?>
                clusterer.add(geoObjects);
                myMap.geoObjects.add(clusterer);
                myMap.setBounds(clusterer.getBounds(),{checkZoomRange: true});
            <?}else{?>
                myMap.geoObjects.add(geoCollection);	
                myMap.setBounds(geoCollection.getBounds());
            <?}?>
        <?
        $js = ob_get_contents();
        ob_end_clean();
        $view->registerJs('ymaps.ready(function(){'.$js.'});',\yii\web\View::POS_END);
    }
}

