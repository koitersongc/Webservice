<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Webservice

     PHP：7.0
     SQL server：2008R2
     
     
     
     XML对接
     使用到soap
     
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
