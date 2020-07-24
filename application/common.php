<?php

 function curl_request($url,$method = "GET",$params = '',$header=[],$auth = '',$cookie = '',$referer= '',$isStatus=1){
        //初始化CURL句柄
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);//设置请求的URL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        //SSL验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求时要设置为false 不验证证书和hosts  FALSE 禁止 cURL 验证对等证书（peer's certificate）, 自cURL 7.10开始默认为 TRUE。从 cURL 7.10开始默认绑定安装。
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);//检查服务器SSL证书中是否存在一个公用名(common name)。
        if(!empty($header)){
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );//设置 HTTP 头字段的数组。格式： array('Content-type: text/plain', 'Content-length: 100')
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt ($curl, CURLOPT_REFERER, $referer);
        //请求时间
        $timeout = 10;
        curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);//设置连接等待时间
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");  //gizp 解压缩
        //不同请求方法的数据提交
        switch ($method){
            case "GET" :
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                //curl_setopt($curl, CURLOPT_HTTPGET, true);//TRUE 时会设置 HTTP 的 method 为 GET，由于默认是 GET，所以只有 method 被修改时才需要这个选项。
                curl_setopt($curl, CURLOPT_POSTFIELDS,$params);
                break;
            case "POST":
                if($params){
                    if(is_array($params)){
                            $params = json_encode($params,JSON_UNESCAPED_UNICODE);
                            $params = str_replace("\\/", "/", $params);
                    }
                    //curl_setopt($curl, CURLOPT_POST,true);//TRUE 时会发送 POST 请求，类型为：application/x-www-form-urlencoded，是 HTML 表单提交时最常见的一种。
                    //curl_setopt($curl, CURLOPT_NOBODY, true);//TRUE 时将不输出 BODY 部分。同时 Mehtod 变成了 HEAD。修改为 FALSE 时不会变成 GET。
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");//HTTP 请求时，使用自定义的 Method 来代替"GET"或"HEAD"。对 "DELETE" 或者其他更隐蔽的 HTTP 请求有用。 有效值如 "GET"，"POST"，"CONNECT"等等；
                    //设置提交的信息
                    curl_setopt($curl, CURLOPT_POSTFIELDS,$params);//全部数据使用HTTP协议中的 "POST" 操作来发送。
                }
                break;
            case "OPTIONS" :
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
                break;
            case "DELETE":
                curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                if($params) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                }
                break;
        }
        //var_dump($params);die;
        //传递一个连接中需要的用户名和密码，格式为："[username]:[password]"。
        if (!empty($auth) && isset($auth['username']) && isset($auth['password'])) {
            curl_setopt($curl, CURLOPT_USERPWD, "{$auth['username']}:{$auth['password']}");
        }
        $data = curl_exec($curl);//执行预定义的CURL
        if ($data === FALSE) {
            echo "cURL Error: " . curl_error($curl);
        }
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);//获取http返回值,最后一个收到的HTTP代码
        curl_close($curl);//关闭cURL会话
        if($isStatus == 1){
            $res = json_decode($data,true);
        }else{
            $res = [
              'status'  => $status,
              'data'  => $data
            ];
        }
        return $res;
    }


//数组转xml
 function arrayToXml($arr){
    $xml = "<xml>";
    foreach ($arr as $key=>$val){
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
    }
    $xml.="</xml>";
    return $xml;
}

 //将xml转为array
 function xmlToArray($xml){
    //将XML转为array
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}

//post  请求，data不转json
function doPost($url,$data){
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res  = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function doGet($url,$header){
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    //curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($header)){
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );//设置 HTTP 头字段的数组。格式： array('Content-type: text/plain', 'Content-length: 100')
    }
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");  //gizp 解压缩
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    return $data;
}

function serverIP(){
    return gethostbyname($_SERVER['SERVER_NAME']);
}


//发送http POST raw数据
function http_post_raw($url,$cookie, $data_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-AjaxPro-Method:ShowList',
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string),
            'Cookie:'.$cookie)
    );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data,TRUE);
}

//获取系统时间
function systemTime(){
     return date("Y-m-d H:i:s");
}

//当前域名
function serverName(){
     return 'http://'.$_SERVER["SERVER_NAME"];
}