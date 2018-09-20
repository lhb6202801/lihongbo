<?php
namespace console\controllers;

use backend\models\Admin;
use yii\console\Controller;

/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 16/9/27
 * Time: 下午12:02
 */
class PasswordController extends Controller
{
    public function actionCreate()
    {
        $admin = new Admin();
        $admin->username = 'vadmin';
        $admin->password = '123456';
        $admin->setPassword($admin->password);
        $admin->auth_key = \Yii::$app->security->generateRandomString();
        $admin->save(false);
    }
}