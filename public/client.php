<?php
//服务器支持soap扩展:
/*Example 1:
$client = new SoapClient("http://fy.webxml.com.cn/webservices/EnglishChinese.asmx?wsdl");
$parameters = array("wordKey"=>"test");
//中英文双向翻译返回数据：数组
$result = $client->TranslatorString($parameters);
 echo "<pre>";
print_r($result->TranslatorStringResult)."<br />";
 echo "</pre>";
//中英文双向翻译返回数组含句子例子：
$result1 = $client->Translator($parameters);
echo "<pre>";
print_r($result1->TranslatorResult)."<br />";
echo "</pre>";
//获得候选词：
$result2 = $client->SuggestWord($parameters);
echo "<pre>";
print_r($result2->SuggestWordResult)."<br />";
echo "</pre>";
//获得朗读MP3字节流,返回数据：字节数组 Byte[]
$result3 = $client->GetMp3($parameters);
echo "<pre>";
print_r($result3)."<br />";
echo "</pre>";
*/
/*Example2:
$client = new SoapClient("http://webservice.webxml.com.cn/WebServices/IpAddressSearchWebService.asmx?wsdl");
$param = array('theIpAddress'=>'202.96.134.33');
$result = $client->getCountryCityByIp($param);
echo "<pre>";
print_r($result->getCountryCityByIpResult);
echo "</pre>";

$result1 = $client->getGeoIPContext($param);
echo "<pre>";
print_r($result1);
echo "</pre>";

$result2 = $client->getVersionTime(
);
echo "<pre>";
print_r($result2);
echo "</pre>";
*/
//Example3:
$client = new SoapClient("http://webservice.webxml.com.cn/WebServices/MobileCodeWS.asmx?wsdl");
//获得国内手机号码归属地省份、地区和手机卡类型信息
$parm=array('mobileCode'=>'1367007','userID'=>'');
$result=$client->getMobileCodeInfo($parm);
echo ($result->getMobileCodeInfoResult)."<br>";
//获得国内手机号码归属地数据库信息
$result1 = $client->getDatabaseInfo($parm);
print_r($result1)."<br>";

// 获取SOAP类型列表(Returns list of SOAP types )
echo '<pre>';
print_r($client->__getTypes ()) ;
echo '</pre>';

// 获取webservice提供的函数
echo '<pre>';
print_r($client->__getFunctions ()) ;
echo '</pre>';
//服务器不支持soap扩展的情况下，可引入网上开源的类库
?>