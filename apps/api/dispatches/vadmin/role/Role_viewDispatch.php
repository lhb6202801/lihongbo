<?php
namespace api\dispatches\vadmin\role;

use api\base\Dispatch;

use common\models\Role;
use common\models\Menu;


/**
 * Note: api
 * Package: api\dispatches\vadmin\role\Role_viewDispatch
 * Alias: role_view.dispatch
 * Classpath: role\Role_viewDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 角色获取
 * Parmas: roleid-string-角色id
 * Return: menus-stirng-权限列表
 * Returnerr: null
 * Detail: 角色获取
 * Type: 角色获取
 */
class Role_viewDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['roleid'])){
            return $this->paramsErrorReturn();
        }
        $roleid = $params['roleid'];
        $role = Role::find()->where(['id'=>$roleid])->one();
        if(is_null($role)){
            return $this->errorReturn(1008);
        }
        $menuarray = explode(',',$role->menus);
        //$menus = Menu::find()->where(['in','code',$menuarray])->all();
        if($role->save(false)){
            return $this->successReturn([
                'menus' => $menuarray,
                'name'=> $role->name,
                'id'=>$role->id
              //  'menusdata'=>$menus
            ]);
        }
        return $this->errorReturn(1007);
    }
}