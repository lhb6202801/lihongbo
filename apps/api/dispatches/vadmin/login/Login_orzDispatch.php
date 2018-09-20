<?php
namespace api\dispatches\vadmin\login;

use api\base\Dispatch;

use common\models\Admin;
use common\models\Role;
use common\models\Menu;


/**
 * Note: api
 * Package: api\dispatches\vadmin\login\Login_orzDispatch
 * Alias: login_orz.dispatch
 * Classpath: login\Login_orzDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 菜单获取
 * Parmas: token-string-用户token
 * Return: menus-stirng-权限列表
 * Returnerr: null
 * Detail: 角色获取
 * Type: 角色获取
 */
class Login_orzDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;

        $user_id = $params['user_id'];
        $admin = Admin::find()->where(['id'=>$user_id])->one();
        if(is_null($admin)){
            return $this->errorReturn(1008);
        }
        $role = Role::find()->where(['id'=>$admin->roleids])->one();
        if(is_null($role)){
            return $this->errorReturn(1008);
        }
        $menuarray = explode(',',$role->menus);
        $menus = Menu::find()->where(['in','code',$menuarray])->all();
        if($role->save(false)){
            return $this->successReturn([
                'menus' => $menus
            ]);
        }
        return $this->errorReturn(1007);
    }
}