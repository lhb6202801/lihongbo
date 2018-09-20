<?php
namespace api\dispatches\vadmin\role;

use api\base\Dispatch;

use common\models\Role;


/**
 * Note: api
 * Package: api\dispatches\vadmin\role\Role_addDispatch
 * Alias: role_add.dispatch
 * Classpath: role\Role_addDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 角色添加
 * Parmas: rolename-string-角色名称, menus-stirng-权限列表
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 角色添加
 * Type: 角色添加
 */
class Role_addDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['rolename'],$params['menus'])){
            return $this->paramsErrorReturn();
        }
        $menulist = $params['menus'];
        $rolename = $params['rolename'];
        //组建数组
//        $valuearray = [];
//        foreach ($menulist as $key => $value){
//            $valuearray[] = $value['code'];
//            if($value.children){
//                foreach ($value.children as $keys => $values){
//                    $valuearray[] = $values['code'];
//                    if($values.children){
//                        foreach ($values.children as $keyss => $valuess){
//                            $valuearray[] = $valuess['code'];
//                        }
//                    }
//                }
//            }
//        }
        $role = new Role();
        $role->name = $rolename;
        $role->menus = implode(',',$menulist);
        $role->created_at = time();
        $role->updated_at = time();
        if($role->save(false)){
            return $this->successReturn([
                'msg' => '添加成功'
            ]);
        }
        return $this->errorReturn(1007);
    }
}