<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SoapClient,Illuminate\Support\Facades\Cache;


class Synchronization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */


//    触发命令   php artisan webService:synchronization
    protected $signature = 'webService:synchronization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronization to webService';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    /*saveSalesBill*/
    public function handle()
    {
        $BILL_MFALL=DB::table('SZXS_MF')->get();
        set_time_limit(0);
        foreach ($BILL_MFALL as $key=>$BILL_MF){
            $log=self::saveSalesBill($BILL_MF);
//            'SalesBillNo'=>$BILL_MF->SalesBillNo,/*必填*/单号

            DB::table('MF_POS_Z')
                ->where('OS_NO', $BILL_MF->SalesBillNo)
                ->update(['UPLOAD' => 'T']);

            $this->info($log);
        }
    }


    /**
     * Notes:
     * User: huawuque-song
     * Date: 2018/5/24
     * Time: 14:25
     * @return string
     */
    public function get_SalesItem(){

        $XML="<webService>
                     <head>
                     <name>".env('WEBSERVICE_USERNAME','szyt')."</name>
                     <password>".env('WEBSERVICE_PASSWORD','f662b17fd7dbc6ff51598686a784fec3')."</password>
                     </head>
                     <request>
                     <Id>0</Id>
                     <UpdateDate>2017-01-01</UpdateDate>
                     </request>
                </webService>";

        $res=self::get_Soapres($XML,'QuerySalesItem');
        return $res;

    }


    /**
     * Notes:
     * User: huawuque-song
     * Date: 2018/5/23
     * Time: 15:54
     */
    public  static function saveSalesBill($BILL_MF)
    {
        $tokenNo=self::get_TokenNo();
        $CompanyInfo=self::get_Companyinfo();
        $object=$CompanyInfo['0'];
        $arr_head=array(
            'CardIndex'=>$object->CardIndex,
            'marketCode'=>$object->LicenseNo,
            'marketName'=>$object->MarketName,
            'tokenNo'=>$tokenNo,
        );
        $XML_head = "<head>";
        foreach ($arr_head as $key=>$val){
            if(is_array($val)){
                $XML_head.="<".$key.">".arrayToXml($val)."</".$key.">";
            }else{
                $XML_head.="<".$key.">".$val."</".$key.">";
            }
        }
        $XML_head.=" </head>";
//        dump($XML_head);


        $arr_SalesBill=array(
            'SalesBillNo'=>$BILL_MF->SalesBillNo,/*必填*/
            'dealTime'=>$BILL_MF->dealTime,/*必填*/
            'dealTotalWeight'=>$BILL_MF->dealWeight,/*必填*/
//            'BuyerType'=>$BILL_MF->BuyerType,/*必填*/
            'BuyerType'=>object_get($BILL_MF,'BuyerType','9'),/*必填*/
            'dealTotalPrice'=>$BILL_MF->dealTotalPrice,
//            'BuyerCardIndex'=>$BILL_MF->BuyerCardIndex,/*对应客户单位在接口提供方的编码*/
            'BuyerCardIndex'=>"",/*对方要求空了*/
            'MarketLicenseNo'=>$BILL_MF->MarketLicenseNo,
            'BuyerLicenseNo'=>$BILL_MF->BuyerLicenseNo,
//            'BuyerLicenseNo'=>'',/*对方要求空了*/
            'BuyerUnitName'=>$BILL_MF->BuyerUnitName,/*必填*/
            'PayResult'=>'1',
            'Remarks'=>'',
            'CreateDate'=>$BILL_MF->CreateDate/*必填*/
        );
        $XML_SalesBill = "";
        foreach ($arr_SalesBill as $key=>$val){
            if(is_array($val)){
                $XML_SalesBill.="<".$key.">".arrayToXml($val)."</".$key.">";
            }else{
                $XML_SalesBill.="<".$key.">".$val."</".$key.">";
            }
        }
        $XML_SalesBill.=" ";
//        dump($XML_SalesBill);
        $BILL_Detail_MF=DB::table('SZXS_TF')->where('SalesBillNo','=',$BILL_MF->SalesBillNo)->get();
//        dd($BILL_Detail_MF);
        $str1="";
        foreach ($BILL_Detail_MF as $key=>$value) {
            $arr_SalesBill_detail= array(
//                'SalesItemCode' => $value->SalesItemCode,/*必填*/
                'SalesItemCode' => "03",/*必填*/
//                'SubItemCode' => object_get($value,'SubItemCode','03004'),/*必填*/
                'SubItemCode' => "03004",
                'SubItemName' => $value->SubItemName,/*必填*/
                'UnitPrice' => $value->UnitPrice,
                'Weight' => $value->Weight,/*必填*/
                'TotalPrice' => $value->TotalPrice,
                'ProductionFrom' => $value->ProductionFrom
            );
            $XML_SalesBill_detail = "<SalesBillDetail>";
            foreach ($arr_SalesBill_detail as $key=>$val){
                if(is_array($val)){
                    $XML_SalesBill_detail.="<".$key.">".arrayToXml($val)."</".$key.">";
                }else{
                    $XML_SalesBill_detail.="<".$key.">".$val."</".$key.">";
                }
            }
            $XML_SalesBill_detail.="</SalesBillDetail> ";

            $str1=$str1.$XML_SalesBill_detail;


        }
//        dump($str1);
        $XML="<webService>".
            $XML_head.
            "<request><dataList><SalesBill>".
            $XML_SalesBill.
            "<SalesBillDetails>".
            $str1.
            "</SalesBillDetails></SalesBill></dataList></request></webService>";


        $res=self::get_Soapres($XML,'saveSalesBill');
        if($res=="1000"){
            $log = $XML;
            $log = '[' . date('Y-m-d H:i:s') . '] ' .$BILL_MF->SalesBillNo."[STATUS:PASS]". $log . "\r\n";
            $filepath = storage_path('logs\webservice.log');
            file_put_contents($filepath, $log, FILE_APPEND);

            return $log;
        }else{
            $log = $XML;
            $log = '[' . date('Y-m-d H:i:s') . '] ' .$BILL_MF->SalesBillNo."[STATUS:ERROR][ERROE:".$res."]". $log . "\r\n";
            $filepath = storage_path('logs\webservice.log');
            file_put_contents($filepath, $log, FILE_APPEND);
            $result='[' . date('Y-m-d H:i:s') . '] ' .$BILL_MF->SalesBillNo."[STATUS:ERROR][ERROE:".$res."]";
            return $result;
        }


    }





    /**
     * Notes:checkIn 签到获取TokenNo
     * User: huawuque-song
     * Date: 2018/5/22
     * Time: 16:01
     * @return mixed
     */
    protected static function get_TokenNo()
    {
        /**
         * 缓存1440分钟
         */
        $TokenNo =Cache::store('redis')->get('webservice_token');
        if (!$TokenNo)
        {
            /*生成XML参数*/
            $mparam=array('name'=>env('WEBSERVICE_USERNAME','szyt'),'password'=>env('WEBSERVICE_PASSWORD','f662b17fd7dbc6ff51598686a784fec3'));
            $Xml=self::arrayToXml($mparam);
            /*SOAP*/
            $res=self::get_Soapres($Xml,'checkIn');
            $postObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
            $jsonStr = json_encode($postObj);
            $jsonArray = json_decode($jsonStr,true);
            $TokenNo=$jsonArray['response']['tokenNo'];
            Cache::store('redis')->put("webservice_token", $TokenNo, 1440);
        }
        return $TokenNo;
    }





    /**
     * Notes:
     * User: huawuque-song
     * Date: 2018/5/23
     * Time: 8:54
     * @param $TokenNo
     * @return mixed
     */
    protected static function checkOut($TokenNo)
    {

        header("Content-type: text/html; charset=utf-8");
        $soap = new SoapClient("http://fs.52myb.com/ncpcs/sDataInfrace.asmx?wsdl");
        $soap->soap_defencoding = 'utf-8';
        $soap->xml_encoding = 'utf-8';
        $mparam=array('tokenNo'=>$TokenNo);
        $Xml=self::arrayToXml($mparam);
        $param = array('strXML'=>$Xml);
        //调用必须用__soapCall
        $p = $soap->__soapCall('checkOut',array('parameters' => $param));
        $res=$p->checkOutResult;
        $postObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = json_encode($postObj);
        $jsonArray = json_decode($jsonStr,true);
        $res=$jsonArray['response']['ResultCode'];
        return $res;
    }

    /**
     * Notes:LicenseNo\MarketName\CardIndex
     * User: huawuque-song
     * Date: 2018/5/23
     * Time: 8:54
     */
    protected static function get_Companyinfo()
    {
        /**
         * 缓存1440分钟
         */
        $CompanyInfo =Cache::store('redis')->get('webservice_companyInfo');
        if (!$CompanyInfo)
        {
            /*生成XML参数*/
            $mparam=array('name'=>env('WEBSERVICE_USERNAME','szyt'),'password'=>env('WEBSERVICE_PASSWORD','f662b17fd7dbc6ff51598686a784fec3'));
            $Xml=self::arrayToXml($mparam);
            /*SOAP*/
            $res=self::get_Soapres($Xml,'QueryMarket');
            $res=json_decode($res);
            Cache::store('redis')->put("webservice_companyInfo", $res, 1440);
        }
        return $CompanyInfo;
    }



    /**
     * Notes:Soap_res
     * User: huawuque-song
     * Date: 2018/5/23
     * Time: 9:17
     * @param $Xml
     * @param $action
     * @return string
     */
    protected static function get_Soapres($Xml,$action)
    {
        try {

            $soap = new \SoapClient(config('services.webservice.url')."?wsdl");
            $soap->__construct =config('services.webservice.url')."?wsdl";
            $soap->xml_encoding = 'utf-8';
            $param = array('strXML'=>$Xml);
            //调用必须用__soapCall
            $p = $soap->__soapCall($action,array('parameters' => $param));
            $resaction=$action.'Result';
            $res=$p->$resaction;
            return $res;
        } catch (Exception $e) {
            print $e->getMessage();
            exit();
        }

    }


    /**
     * Notes:array ->XML
     * User: huawuque-song
     * Date: 2018/5/22
     * Time: 15:57
     * @param $arr
     * @return string
     */
    protected static function arrayToXml($arr)
    {
        $xml = "<webService><request>";
        foreach ($arr as $key=>$val){
            if(is_array($val)){
                $xml.="<".$key.">".arrayToXml($val)."</".$key.">";
            }else{
                $xml.="<".$key.">".$val."</".$key.">";
            }
        }
        $xml.="</request></webService>";
        return $xml;
    }
}
