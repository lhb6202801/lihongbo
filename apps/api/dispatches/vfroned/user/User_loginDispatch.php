<?php
namespace api\dispatches\vfroned\user;

use api\base\Dispatch;
use abei2017\wx\Application;
use common\models\Auction;
use common\models\Teacher;
use common\models\Wx;
use api\helpers\ApiHelper;
use common\models\AuctionInviter;
use common\models\CompanyWx;

use Yii;



/**
 * Note: api
 * Package: api\dispatches\vfroned\user\User_loginDispatch
 * Alias: user_login.dispatch
 * Classpath: user\User_loginDispatch
 * Version: froned
 * Allowed: true
 * Istoken: false
 * Describe: 用户登陆
 * Parmas: code-string-用户注册
 * Return: model-json-用户信息, token-string-用户token
 * Returnerr: null
 * Detail: 用户登陆
 * Type: 用户登陆
 */
class User_loginDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['code'])){
            return $this->paramsErrorReturn();
        }
        $nickname = $params['nickname'];
        $avatarurl = $params['avatarurl'];
        $gender = $params['gender'];
        $city = $params['city'];
        $province = $params['province'];
        $app = new Application(['conf'=>Yii::$app->params['wx']['mini']]);
        $user = $app->driver("mini.user");
        $result = $user->codeToSession($params['code']);
        $openid = $result['openid'];
        $session_key = $result['session_key'];
        $userinfo = Wx::find()->where(['wx_id'=>$openid])->one();
        if(is_null($userinfo)){
            //新用户
            $identity = $params['iy'];
            $s = $params['s'];
            if(isset($identity)){
                $qr = QrCodeStr::find()->where(['str'=>$s])->one();
                if(is_null($qr)){
                    return $this->errorReturn(1015);
                }
            }else{
                return $this->errorReturn(1018);
            }
            $userinfo = new Wx();
            $userinfo->nickname = $nickname;
            $userinfo->avatar = $avatarurl;
            $userinfo->sex = $gender;
            $userinfo->address = $province.'-'.$city;
            $userinfo->created_at = time();
            $userinfo->wx_id = $openid;
            $userinfo->save(false);
        }
        if(is_null($userinfo)){
            return $this->errorReturn(1018);
        }
        //缓存信息
        $tokenClass = ApiHelper::getTokenClass($this->version);
        if (empty($tokenClass)) {
            return $this->errorReturn(1005);
        }
        $salertoken = $tokenClass::find()->where(['user_id'=>$userinfo->id])->one();
        if(!is_null($salertoken)){
            $salertoken->delete();
        }
        $salertoken =  new $tokenClass;
        $salertoken->user_id = $userinfo->id;
        $salertoken->nickname = $userinfo->nickname;
        $salertoken->sex = $userinfo->sex;
        $salertoken->session_key = $session_key;
        $salertoken->created_at = time();
        $salertoken->state = time();
        $salertoken->avatar = $userinfo->avatar = $avatarurl;
        $salertoken->token = Yii::$app->security->generateRandomString();
        $salertoken->insert();




        //身份处理
        //竞拍者 - 拍卖师 - 围观人 - 委托人
        $identity = $params['iy'];
        $identityid = $params['yid'];
        $s = $params['s'];
        if(isset($identity)){
            //校验二维码是否有效
            //$qr = QrCodeStr::find()->where(['str'=>$s])->one();
            //if(is_null($qr)){
            //  if(is_null($userinfo)){
            //     return $this->errorReturn(1015);
            //  }
            //}
            if($identity == 'tr'){
                //拍卖师  teacher
                //拍卖师ID $identityid
                //关联拍卖师
                $teacher = Teacher::find()->where(['id'=>$identityid])->one();
                if(is_null($teacher)){
                    return $this->errorReturn(1012);
                }
                $teacher->wx_id = $userinfo->id;
                $teacher->save(false);
            }else if($identity == 'br'){
                //竞拍者 bidders
                //拍卖会ID $identityid
                //查询 企业ID
                $auction = Auction::find()->where(['id'=>$identityid])->one();
                if(is_null($auction)){
                    return $this->errorReturn(1011);
                }
                // 1 绑定 企业邀请微信关系表
                $companywx = CompanyWx::find()->where(['companyId'=>$auction->companyId,'wxId'=>$userinfo->id])->one();
                if(is_null($companywx)){
                    $companywx = new CompanyWx();
                    $companywx->companyId = $auction->companyId;
                    $companywx->wxId = $userinfo->id;
                    $companywx->created_at = time();
                    $companywx->save(false);
                }
                // 2 绑定 拍卖会邀请表
                $auctioninviter = AuctionInviter::find()->where(['companyId'=>$auction->companyId,'auctionId'=>$auction->id,'wxId'=>$userinfo->id])->one();
                if(is_null($auctioninviter)){
                    $auctioninviter = new AuctionInviter();
                    $auctioninviter->companyId = $auction->companyId;
                    $auctioninviter->wxId = $userinfo->id;
                    $auctioninviter->auctionId = $auction->id;
                    $auctioninviter->type = 0;
                    $auctioninviter->auctionNumber = intval($userinfo->id)+intval($auction->id)+10000;
                }
            }else if($identity == 'or'){
                //围观者 onlookers
                //拍卖会ID $identityid
                //查询 企业ID
                $auction = Auction::find()->where(['id'=>$identityid])->one();
                if(is_null($auction)){
                    return $this->errorReturn(1011);
                }
                // 1 绑定 企业邀请微信关系表
                $companywx = CompanyWx::find()->where(['companyId'=>$auction->companyId])->one();
                if(is_null($companywx)){
                    $companywx = new CompanyWx();
                    $companywx->companyId = $auction->companyId;
                    $companywx->wxId = $userinfo->id;
                    $companywx->created_at = time();
                    $companywx->save(false);
                }
                // 2 绑定 拍卖会邀请表
                $auctioninviter = AuctionInviter::find()->where(['companyId'=>$auction->companyId,'auctionId'=>$auction->id,'wxId'=>$userinfo->id])->one();
                if(is_null($auctioninviter)){
                    $auctioninviter = new AuctionInviter();
                    $auctioninviter->companyId = $auction->companyId;
                    $auctioninviter->wxId = $userinfo->id;
                    $auctioninviter->auctionId = $auction->id;
                    $auctioninviter->type = 0;
                    $auctioninviter->auctionNumber = intval($userinfo->id)+intval($auction->id)+10000;
                }
            }else if($identity == 'ct'){
                //委托人 client
                //拍卖会ID $identityid
                //查询 企业ID
                $auction = Auction::find()->where(['id'=>$identityid])->one();
                if(is_null($auction)){
                    return $this->errorReturn(1011);
                }
                // 1 绑定 企业邀请微信关系表
                $companywx = CompanyWx::find()->where(['companyId'=>$auction->companyId])->one();
                if(is_null($companywx)){
                    $companywx = new CompanyWx();
                    $companywx->companyId = $auction->companyId;
                    $companywx->wxId = $userinfo->id;
                    $companywx->created_at = time();
                    $companywx->save(false);
                }
                // 2 绑定 拍卖会邀请表
                $auctioninviter = AuctionInviter::find()->where(['companyId'=>$auction->companyId,'auctionId'=>$auction->id,'wxId'=>$userinfo->id])->one();
                if(is_null($auctioninviter)){
                    $auctioninviter = new AuctionInviter();
                    $auctioninviter->companyId = $auction->companyId;
                    $auctioninviter->wxId = $userinfo->id;
                    $auctioninviter->auctionId = $auction->id;
                    $auctioninviter->type = 0;
                    $auctioninviter->auctionNumber = intval($userinfo->id)+intval($auction->id)+10000;
                }
            }
            //设置二维码失效
            //$qr->delete();
        }
        //查询身份
        $isiv = false;
        $auction = AuctionInviter::find(['wxId'=>$userinfo->id])->one();
        if(!is_null($auction)){
            $isiv = true;
        }else{
            $teacher = Teacher::find()->where(['wx_id'=>$userinfo->id])->one();
            if(!is_null($teacher)){
                $isiv = true;
            }
        }
        //查询是否绑定
        $isv = false;
        $wxuserinfo = Wx::find()->where(['id'=>$userinfo->id])->one();
        if(!is_null($wxuserinfo)){
            if($wxuserinfo->phone !="" && $wxuserinfo->phone!=null){
                $isv = true;
            }
        }
        return $this->successReturn([
            'token' =>$salertoken->token,
            'isiv'=>$isiv,
            'isv'=>$isv
        ]);
    }
}