<?php
namespace common\helpers;

use common\models\Bills;
use common\models\Count;
use common\models\Dictionary;
use common\models\redis\SalerToken;
use common\models\Reply;
use common\models\Reward;
use common\models\Saler;
use common\models\ThreeSetting;
use common\models\AgencyWechat;
use api\wxhelpers\WechatOAuth;
use common\models\Agency;
use yii;

/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/2/27
 * Time: 下午2:09
 */
class CommonSaler
{
    /*
     *  //处理上级数据
            /*
             * 2017-04-21有与功能需求变更,对现有的提现方式进行修改,原有的上级提现改为根据级别,净保费减少
             * *
     * 出单成功后的数据处理   后期优化数据库的访问
     * */
    public static function issueSuccess($count_id)
    {
//        $sql = 'select a.jqx,a.ccsys,a.sy from  t_reply a join t_count b on (a.count_id = b.id) where b.id=' . $count_id;
//        $connection = Yii::$app->db;
//        $command = $connection->createCommand($sql);
//        $result = $command->queryOne();
//        $amount = floatval($result['jqx']) + floatval($result['sy']) + floatval($result['ccsys']);
        //所有的金钱数据
        $count = Count::find()->where(['id' => $count_id])->one();
        $saler = Saler::find()->where(['id' => $count->saler_id])->one();
        //上级不空   且当前 不是有效业务员的时候
        if (!is_null($saler->parent) && $saler->issue_count < 1) {
            self::issueParentLevel($saler->parent);
        }
        //上级团队奖励
        if (!is_null($saler->parent)) {
            self::issueParentReward($saler->parent, $count_id);
        }
        //累计有效单数
        $saler->issue_count += 1;
        //2017-07-13 增加字段issue_date 计算有效期
        $saler->issue_date = time();
//        if (!is_null($saler->parent)) {
        //处理上级数据
        /*
         * 2017-04-21有与功能需求变更,对现有的提现方式进行修改,原有的上级提现改为根据级别,净保费减少
         * */
        //self::issueOne($saler->parent, $amount);
//        }
        $saler->save();
    }

    /*
     * 给上级累加有效性
     * */
    private static function issueParentLevel($parent)
    {
        $saler = Saler::find()->where(['id' => $parent])->one();
        if (!is_null($saler)) {
            if($saler->level < 3){
                $saler->level += 1;
                $saler->save();
            }
        }
    }

    /*团队管理奖项*/
    private static function issueParentReward($saler_id, $count_id)
    {
        $saler = Saler::find()->where(['id' => $saler_id])->one();
        $dictionary = Dictionary::find()->where(['type' => 1004])->one();
        //级别大于有效的个数
        if (!is_object($saler)) return;
        if (is_null($saler)) return;
        if ($saler->parent == -1) return;
        if ($saler->level >= $dictionary->value) {
            $reply = Reply::find()->where(['count_id' => $count_id])->one();
            $bills = Bills::find()->where(['count_id' => $count_id])->one();
            $dictionary2 = Dictionary::find()->where(['type' => 1006])->one();
            $reward = new Reward();
            $reward->saler_id = $saler_id;
            $reward->created_at = time();
            $reward->bills_id = $bills->id;
            $reward->reward_where_id = -1;  //-1代表是团队奖励来
            $value = floatval($dictionary2->value);
            $reward->money = ((floatval($reply->jqx) + floatval($reply->sy)) / 100) * $value;
            $reward->agency_id = $saler->agency_id;
            $reward->save();
        }
    }

    /*
     * 给第一上级
     * */
    private static function issueOne($parent, $amount)
    {
        $saler = Saler::find()->where(['id' => $parent])->one();
        if (is_null($saler)) {
            $saler->one = $amount;
            $saler->save();
            //级别处理 先去掉 应用
            //self::levelCount($parent);
            //判断一下上一级的级别
            if (!is_null($saler->parent)) {
                self::issueTwo($saler->parent, $amount);
            }
        }
    }

    /*
     * 判断上一级的级别
     * */
    private static function levelCount($salerID)
    {
        $sql = 'select count(*) as issue from t_saler where parent = ' . $salerID . ' and issue_count >0';
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $result = $command->queryOne();
        $count = intval($result['issue']);
        $saler = Saler::findOne($salerID);
        if ($count > 3 && $count <= 10) {
            $saler->level = 1;
        } elseif ($count > 10 && $count <= 50) {
            $saler->level = 2;
        } elseif ($count >= 50) {
            $saler->level = 3;
        }
        $saler->save();
    }

    /*
    * 给第二级
    * */
    private static function issueTwo($parent, $amount)
    {
        $saler = Saler::find()->where(['id' => $parent])->one();
        $saler->two += floatval($amount);    //注意
        $saler->save();
        if (!is_null($saler->parent)) {
            self::issueThree($saler->parent, $amount);
        }
    }

    /*
     * 第三级别
     * */
    private static function issueThree($parent, $amount)
    {
        $saler = Saler::find()->where(['id' => $parent])->one();
        $saler->three = $amount;
        $saler->save();
    }

    /*显示头像专用*/
    public static function showImg($url, $w, $h)
    {
        if (trim($url) == '') {
            return '无';
        } else {
            return yii\bootstrap\Html::img($url, ['width' => $w, 'height' => $h]);
        }
    }

    /*
     * 显示级别
     * 2017-04-21 需求变更更改
     *
     * */
    public static function showLevel($level = 0)
    {
        if (trim($level) == '' || $level < 3) {
            return 0;
        } elseif ($level >= 3 && $level < 6) {
            return 1;
        } elseif ($level >= 6) {
            return 2;
        } else {
            return 0;
        }
//        if (trim($level) == '' || $level < 3) {
//            return 0;
//        } elseif ($level > 3 && $level <= 10) {
//            return 1;
//        } elseif ($level > 10 && $level <= 50) {
//            return 2;
//        } elseif ($level > 50) {
//            return 3;
//        } else {
//            return 0;
//        }
    }

    /*
     * 显示可以提现的金额
     * level  是本身所属级别
     * */
    public static function showMoney($level, $moneyOne, $moneyTwo, $moneyThree)
    {
        $level = intval($level);
        $setting = ThreeSetting::find()->andWhere(['<=', 'level', $level])->asArray()->all();
        if (empty($setting)) {
            return 0;
        }
        $ratio = yii\helpers\ArrayHelper::map($setting, 'level', 'ratio');
        switch ($level) {
            case 0:
                return 0;
                break;
            case 1:
                $ratioOne = floatval($ratio[1]);
                $moneyOne = floatval($moneyOne);
                $ret = $ratioOne * $moneyOne;
                return $ret;
                break;
            case 2:
                $ratioOne = floatval($ratio[1]);
                $moneyOne = floatval($moneyOne);
                $ratioTwo = floatval($ratio[2]);
                $moneyTwo = floatval($moneyTwo);
                $ret = $ratioOne * $moneyOne;
                $ret = $ret + $ratioTwo * $moneyTwo;
                return $ret;
                break;
            case 3:
                $ratioOne = floatval($ratio[1]);
                $moneyOne = floatval($moneyOne);
                $ratioTwo = floatval($ratio[2]);
                $moneyTwo = floatval($moneyTwo);
                $ratioThree = floatval($ratio[3]);
                $moneyThree = floatval($moneyThree);
                $ret = $ratioOne * $moneyOne;
                $ret = $ret + $ratioTwo * $moneyTwo;
                $ret = $ret + $ratioThree * ($moneyOne + $moneyTwo + $moneyThree);
                return $ret;
                break;
            default:
                return 0;
                break;
        }
    }

    /*
     * redis 获取业务员信息
     * */
    public static function getSalerInfo($wx_id)
    {
        $salerToken = SalerToken::find()->where(['wx_id' => $wx_id])->asArray()->one();
        if (is_null($salerToken)) {
            $saler = Saler::find()->where(['wx_id' => $wx_id])->one();
            $salerToken = new SalerToken();
            $salerToken->id = $saler->id;
            $salerToken->wx_id = $saler->wx_id;
            $salerToken->sex = $saler->sex;
            $salerToken->nickname = $saler->nickname;
            $salerToken->state = $saler->state;
            $salerToken->avatar = $saler->avatar;
            $salerToken->insert();
        }
        return $salerToken;
    }
    /*
     * redis 业务员登陆信息添加
     * */
    public static function setSalerInfoforbying($wx_id,$expires_in,$refresh_token,$accesstoken)
    {
        $saler = Saler::find()->select('nickname,state,parent,wx_id,agency_id,avatar,sex,age,id,phone')->where(['wx_id'=>$wx_id])->one();
        $isagenid = '';
        $isparent = '';
        $phone = '';
        if($saler['parent']!=null && $saler['parent']!=""){
            $isparent = $saler['parent'];
        }else{
            $isparent = 'false';
        }
        if($saler['phone']!=null && $saler['phone']!=""){
            $phone = $saler['phone'];
        }else{
            $phone = 'false';
        }
        if($saler['agency_id']!=null && $saler['agency_id']!=""){
            $isagenid = $saler['agency_id'];
        }else{
            $isagenid = 'false';
        }
        if(!is_null($saler)){
            $salerToken = SalerToken::find()->where(['saler_id'=>$saler->id])->one();
             if(!is_null($salerToken)){
                SalerToken::deleteAll(['saler_id' => $saler->id]);
            }
            $salerToken = new SalerToken();
            $salerToken->saler_id = $saler->id;
            $salerToken->wx_id = $wx_id;
            $salerToken->sex = $saler->sex;
            $salerToken->state = $saler->state;
            $salerToken->avatar = $saler->avatar;
            $salerToken->nickname = $saler->nickname;
            $salerToken->agency_id = $saler->agency_id;
            $salerToken->level = $saler->level;
            $salerToken->access_token = $accesstoken;
            $salerToken->expires_in = $expires_in;
            $salerToken->refresh_token = $refresh_token;
            $salerToken->create_at = time();
            $salerToken->insert();
            return array(
                'nickname'=> $saler['nickname'],
                'state'=> $saler['state'],
                'parent'=> $isparent,
                'agency_id'=> $isagenid,
                'access_token'=> $accesstoken,
                'headimage'=> $saler['avatar'],
                'phone'=> $phone
            );
        }else{
            return false;
        }
        return $salerToken;
    }
    /*
     * redis 业务员登陆信息更新
     * */
    public static function updateSalerInfoforbying($accesstoken)
    {
        $salerToken = SalerToken::find()->where(['access_token'=>$accesstoken])->one();
        if (!is_null($salerToken)) {
            $msginfo = WechatOAuth::checkAccessToken($accesstoken,$salerToken['wx_id']);
            //是否过期
            if($msginfo['errmsg'] != 'ok'){
                //过期 
                //调用信息更新
                $saler = Saler::find()->where(['id'=>$salerToken->saler_id])->one();
                if($saler!=null){
                        $appid = $saler['appid'];
                        $url = WechatOAuth::refreshTokenforbying($salerToken->refresh_token,$appid);
                        if(isset($url['access_token'])){
                            $accesstoken = $url['access_token'];
                        }else{
                            return array('err' => '1','errcode' => '1006');
                        }
                        //更新
                        $salerTokennew  = new SalerToken();
                        $salerTokennew->saler_id = $salerToken->saler_id;
                        $salerTokennew->wx_id = $salerToken->wx_id;
                        $salerTokennew->sex = $salerToken->sex;
                        $salerTokennew->state = $salerToken->state;
                        $salerTokennew->avatar = $salerToken->avatar;
                        $salerTokennew->nickname = $salerToken->nickname;
                        $salerTokennew->agency_id = $salerToken->agency_id;
                        $salerTokennew->level = $salerToken->level;
                        $salerTokennew->access_token = $accesstoken;
                        $salerTokennew->expires_in = $url['expires_in'];
                        $salerTokennew->refresh_token = $url['refresh_token'];
                        $salerTokennew->create_at = time();
                        SalerToken::deleteAll(['saler_id' => $saler->id]);
                        $salerTokennew->insert();
                    
                }else{
                    return array('err' => '1','errcode' => '1005');
                }
            }
        }else{
            return array('err' => '1','errcode' => '1007');
        }
        return array('err' => '0','accesstoken' => $accesstoken,'salerid' =>$salerToken->saler_id,'agency_id'=>$salerToken->agency_id,'level'=>$salerToken->level);
    }


    /*
     * redis 更新业务员信息
     * */
    public static function updateSalerInfo($id)
    {
        SalerToken::deleteAll(['id' => $id]);
        $saler = Saler::find()->where(['id' => $id])->one();
        $salerToken = new SalerToken();
        $salerToken->id = $saler->id;
        $salerToken->wx_id = $saler->wx_id;
        $salerToken->sex = $saler->sex;
        $salerToken->nickname = $saler->nickname;
        $salerToken->state = $saler->state;
        $salerToken->agency_id = $saler->agency_id;
        $salerToken->level = $saler->level;
        $salerToken->avatar = $saler->avatar;
        $salerToken->insert();
    }


    /*
     * 判断手机号公众账号下唯一性
     * */
    public static function verificationphone($phone,$salerid)
    {
        $saler = Saler::find()->where(['id'=>$salerid])->one();
        if(!is_null($saler)){
            $salercount = Saler::find()->where(['appid'=>$saler['appid'],'phone'=>$phone])->all();
            if($salercount!=null){
                return false;
            }
        }
        return true;
    }

    /*
    * 显示级别
    * */
    public static function getLevelBySalerID($saelr_id)
    {
        $saler = Saler::findOne($saelr_id);
        if (!is_object($saler)) return 0;
        //代理点无级别
        if (is_null($saler)) return 0;
        if ($saler->parent == -1) return 0;
        $level = $saler->level;
        if (trim($level) == '' || $level == null) {
            return 0;
        } elseif ($level > 0 && $level < 3) {
            return 0;
        } elseif ($level >= 3 && $level < 6) {
            return 1;
        } elseif ($level >= 6) {
            return 2;
        } else {
            return 0;
        }
    }

    /*
     * 显示某个业务员的询价数量
     * */
    public static function getCountSumBySalerID($saler_id)
    {
        Count::find()->select('count(*)')->where(['saler_id' => $saler_id])->one();
    }

    /*
     * 限制业务员的出单数量
     * */
    public static function limitSalerCount($saler_id)
    {
        $level = self::getLevelBySalerID($saler_id);
        if ($level < 2) {
            return false;    //只处理二级 和 团队经理
        }
        $monthStart = strtotime(date('Y-m-01 0:00:00', strtotime(date("Y-m-d"))));
        $monthEnd = strtotime(date('Y-m-01 0:00:00', strtotime('+1 month')));
        $bills = Bills::find()->where(['saler_id' => $saler_id, 'state' => 3])->andWhere(['>', 'confirmd_at', $monthStart])->andWhere(['<', 'confirmd_at', $monthEnd])->asArray()->all();
        $count = count($bills);
        $dictionary = Dictionary::find()->where(['type' => 1005])->one();
        if (is_null($dictionary)) return -1;
        if ($dictionary->value < $count) return true;
        return false;
    }

    //生成token
    public static function generateToken($saler_id, $state)
    {
        $token = Yii::$app->security->generateRandomString(64);

        self::removeToken($saler_id);

        //写入redis
        $tokenModel = new SalerToken();
        $tokenModel->token = $token;
        $tokenModel->user_id = $saler_id;
        $tokenModel->created_at = time();
        $tokenModel->state = $state;
        $tokenModel->insert();


        return $token;
    }

    //删除Token
    public static function removeToken($saler_id)
    {
        SalerToken::deleteAll(['user_id' => $saler_id]);
    }

    //获取代理点绑定的微信平台
    public static function getagencypd($agencyid)
    {
        $agenwx = AgencyWechat::find()->where(['agency_id'=>$agencyid])->one();
        if($agenwx!=null){
            //当前代理下有公众账号 绑定公众账号
            $data['token'] = $agenwx->token;
            $data['agency_id'] = $agenwx->agency_id;
            $data['appid'] = $agenwx->app_id;
            $data['appsecret'] = $agenwx->app_secret;
            $data['name'] = $agenwx->name;
        }else{
            //当前代理
            $pagency = Agency::find()->select('partent_id')->where(['id'=>$agencyid])->one();
            //当前代理是否存在 ？
            if($pagency!=null){
                //查找上级代理 是否有公众平台
                $agenwx = AgencyWechat::find()->where(['agency_id'=>$pagency->partent_id])->one();
                if($agenwx!=null){
                    //如果有绑定上级平台
                    $data['token'] = $agenwx->token;
                    $data['agency_id'] = $agenwx->agency_id;
                    $data['appid'] = $agenwx->app_id;
                    $data['appsecret'] = $agenwx->app_secret;
                    $data['name'] = $agenwx->name;
                }else{
                    //如果没有 查询上级代理
                    $pagency = Agency::find()->select('partent_id')->where(['id'=>$pagency->partent_id])->one();
                    //上级代理是否存在？
                    if($pagency!=null){
                        //查询上上级代理是否有公众平台
                        $agenwx = AgencyWechat::find()->where(['agency_id'=>$pagency->partent_id])->one();
                        if($agenwx!=null){
                            //如果有绑定上上级平台
                            $data['token'] = $agenwx->token;
                            $data['agency_id'] = $agenwx->agency_id;
                            $data['appid'] = $agenwx->app_id;
                            $data['appsecret'] = $agenwx->app_secret;
                            $data['name'] = $agenwx->name;
                        }else{
                            //上上级公众平台不存在
                            return false;
                        }
                    }else{
                        //上级代理不存在
                        return false;
                    }
                }
            }else{
                //当前代理不存在
                return false;
            }
        }
        return $data;
    }
}