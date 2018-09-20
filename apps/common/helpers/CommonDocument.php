<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8 15:43
 * Time: 下午 3:43
 */
namespace common\helpers;
use common\models\DocumentType;
use common\models\Document;
use common\models\DocumentCompany;
use common\models\Saler;
use yii\helpers\ArrayHelper;
class CommonDocument
{
    //获取单证类型列表
    //return map 
    public static function GetDocumentTypelist()
    {
        $doctype = DocumentType::find()->all();
        $doctype = ArrayHelper::map($doctype, 'id', 'doc_name');
        return $doctype;
    }
    //获取保险公司类型列表
    //return map 
    public static function GetDocumentCompanyTypelist()
    {
        $doctype = DocumentCompany::find()->all();
        $doctype = ArrayHelper::map($doctype, 'id', 'company_name');
        return $doctype;
    }
    //根据单证类型返回相应的名称
    //return string
    public static function GetDocumentTypeNameForById($id)
    {
        $doctype = DocumentType::find()->where(['id'=>$id])->all();
        return $doctype['doc_name'];
    }
    //根据保险公司类型返回相应的名称
    //return string
    public static function GetDocumentCompanyTypeNameForById($id)
    {
        $doctype = DocumentCompany::find()->where(['id'=>$id])->all();
        return $doctype['company_name'];
    }

    //返回用户列表
    //return array
    public static function GetSalerList($id)
    {
        $Saler = Saler::find()->where(['agency_id'=>$id])->all();
        $Saler = ArrayHelper::map($Saler, 'id', 'nickname');
        return $Saler;
    }
    //返回用户列表
    //return string
    public static function GetSalerNameForById($id)
    {
        $Saler = Saler::find()->where(['id'=>$id])->one();
        return $Saler['nickname'];
    }
    //检测是否可以正常下发
    //return string
    public static function GetIsGrant($start,$end,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'grant_state'=>0,'state'=>0])->andwhere(['<>','operat_state',2])->andWhere(['between','(doc_number+0)',$start,$end])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以正常下发
    //return string
    public static function GetIsGrantSan($content,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'grant_state'=>0,'state'=>0])->andwhere(['<>','operat_state',2])->andWhere(['in','doc_number',$content])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以作废订单
    //return string
    public static function GetIsAbolish($start,$end,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'operat_state'=>0,'state'=>0])->andWhere(['between','(doc_number+0)',$start,$end])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以作废订单
    //return string
    public static function GetIsAbolishSan($content,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'operat_state'=>0,'state'=>0])->andWhere(['in','doc_number',$content])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }

    //检测是否可以回收订单
    //return string
    public static function GetIsRecovery($start,$end,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'operat_state'=>0,'grant_state'=>1])->andWhere(['between','(doc_number+0)',$start,$end])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }

    //检测是否可以回收订单
    //return string
    public static function GetIsRecoverySan($content,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'operat_state'=>0,'grant_state'=>1])->andWhere(['in','doc_number',$content])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以回销
    //return string
    public static function GetIsSell($start,$end,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id])->andWhere(['between','(doc_number+0)',$start,$end])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以回销
    //return string
    public static function GetIsSellSan($content,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id])->andWhere(['in','doc_number',$content])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以使用
    //return string
    public static function GetIsUse($start,$end,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'grant_state'=>1,'state'=>0])->andwhere(['<>','operat_state',2])->andWhere(['between','(doc_number+0)',$start,$end])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //检测是否可以使用
    //return string
    public static function GetIsUseSan($content,$num,$agency_id)
    {
        $doc = Document::find()->where(['agency_id'=>$agency_id,'grant_state'=>1,'state'=>0])->andwhere(['<>','operat_state',2])->andWhere(['in','doc_number',$content])->all();
        if(count($doc) == intval($num)){
            return ArrayHelper::getColumn($doc,'id');
        }
        return false;
    }
    //返回回销订单状态
    //return string
    public static function GetSellState($state)
    {
        if($state == "0"){
            return "未使用";
        }else if($state == "1"){
            return "已使用";
        }elseif($state == "2"){
            return "已作废";
        }
    }
    //返回回销订单状态
    //return string
    public static function GetSellGrantState($grant_state)
    {
        if($grant_state == "0"){
            return "未下发";
        }else if($grant_state == "1"){
            return "已下发";
        }
    }
    //返回回销订单状态
    //return string
    public static function GetSellOperatState($operat_state)
    {
        if($operat_state == "0"){
            return "未回收";
        }else if($operat_state == "1"){
            return "已回收";
        }else if($operat_state == "2"){ 
            return "已回销";
        }
    }
}