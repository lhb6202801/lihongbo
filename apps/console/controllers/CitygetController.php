<?php
namespace console\controllers;



use yii\console\Controller;
use common\models\Area;
use common\models\City;
use common\models\Province;
use yii\helpers\ArrayHelper;
use yii;
/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/4/19
 * Time: 下午2:11
 */
class CitygetController extends Controller
{
    public function actionGetcitystojson()
    {
        $list = [];
        $Province = Province::find()->all();
        foreach ($Province as $key => $value){
            $arrayinfo['value'] = $value['provinceID'];
            $arrayinfo['label'] = $value['province'];
            $City = City::find()->where(['father' => $value['provinceID']])->all();
            $Cityarray = [];
            foreach ($City as $keys => $values){
                $info = [];
                $info['value'] = $values['cityID'];
                $info['label'] = $values['city'];
                $Cityarray[] = $info;
            }
            $arrayinfo['children'] = $Cityarray;
            $list[] = $arrayinfo;
        }
       // $Provincearray = ArrayHelper::map($Province, 'provinceID', 'province');
      //  $json_stringProvince = json_encode($Provincearray,JSON_UNESCAPED_UNICODE);
        // 写入文件
//        $data = [];
//        foreach ($Province as $key => $value){
//            $info = ArrayHelper::map($Province, 'provinceID', 'province');
//            $City = City::find()->where(['father' => $value['provinceID']])->all();
//            if(!is_null($City)){
//                $Cityarray = ArrayHelper::map($City, 'cityID', 'city');
//                $info['children'] = $Cityarray;
//            }
//           // array_push($info,$Cityarray,'children');
//           // array_push($data,$info);
//        }

       // $data[]= $Provincearray;
      //  $City = City::find()->all();
     //   $Cityarray = ArrayHelper::map($City, 'cityID', 'city');
        //$json_stringCity = json_encode($Cityarray,JSON_UNESCAPED_UNICODE);
        
        //$json = json_encode($data,JSON_UNESCAPED_UNICODE);
        //$data[]= $Cityarray;
      //  $Area = Area::find()->all();
     //   $Areaarray = ArrayHelper::map($Area, 'areaID', 'area');
       // $json_Area = json_encode($Areaarray,JSON_UNESCAPED_UNICODE);

        $json_Area = json_encode($list,JSON_UNESCAPED_UNICODE);
        $str = preg_replace('/"(\w+)"(\s*:\s*)/is', '$1$2', $json_Area);
        echo var_dump(json_decode($json_Area,true));
        file_put_contents('citylist.json',$str);
    }
}