<?php
namespace api\dispatches\vadmin\menu;

use api\base\Dispatch;
use common\models\Menu;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\menu\Menu_initDispatch
 * Alias: menu_init.dispatch
 * Classpath: menu\Menu_initDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 初始化前端菜单
 * Parmas: menulist-jsonarray-菜单列表, token-stirng-用户token
 * Return: model-jsonarray-菜单列表
 * Returnerr: null
 * Detail: 初始化前端菜单
 * Type: 初始化前端菜单
 */
class Menu_initDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['menulist'])){
            return $this->paramsErrorReturn();
        }
        $menulist = $params['menulist'];
        $columnarray =['name','icon','label','code','parent'];
        $valuearray = [];
        foreach ($menulist as $key => $value){
            $infoarray[] = $value['name'];
            $infoarray[] = $value['icon'];
            $infoarray[] = $value['label'];
            $infoarray[] = $value['id'];
            $infoarray[] = '0';
            if($value['children']){
                foreach ($value['children'] as $keys => $values){
                    $infoarrays[] = $values['name'];
                    $infoarrays[] = $values['icon'];
                    $infoarrays[] = $values['label'];
                    $infoarrays[] = $values['id'];
                    $infoarrays[] = $value['id'];
                    if($values['children']){
                        foreach ($values['children'] as $keyss => $valuess){
                            $infoarrayss[] = $valuess['name'];
                            $infoarrayss[] = $valuess['icon'];
                            $infoarrayss[] = $valuess['label'];
                            $infoarrayss[] = $valuess['id'];
                            $infoarrayss[] = $values['id'];
                            $valuearray[] = $infoarrayss;
                            unset($infoarrayss);
                        }
                    }
                    $valuearray[] = $infoarrays;
                    unset($infoarrays);
                }
            }
            $valuearray[] = $infoarray;
            unset($infoarray);
        }
        Menu::deleteAll();
        $saveapi = Yii::$app->db->createCommand()->batchInsert(Menu::tableName(), $columnarray, $valuearray)->execute();
        if(!$saveapi){
            return $this->successReturn([
                'msg' => '初始化失败'
            ]);
        }
        return $this->successReturn([
            'model' => $valuearray
        ]);
    }
}