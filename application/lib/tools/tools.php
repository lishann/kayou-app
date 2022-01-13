<?php

namespace app\lib\tools;


class tools
{

    public static $dlog_filename = __DIR__ . '/debug.log'; //日志文件
    public static $dlog_recordsize = 8192; //单条记录大小
    public static $dlog_outmode = 'file'; //show-屏幕输出 file-文件输出 all-全部输出



    public static function setPath()
    {
        self::$dlog_filename = './public/debug.log';
    }
    public static function getPath()
    {
        return self::$dlog_filename;
    }


    public static function build_res($code, $msg, $data = '')
    {
        return json_Encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public static function getNonceStr()
    { //取随机10位字符串
        $strs = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $name = substr(str_shuffle($strs), mt_rand(0, strlen($strs) - 11), 20);
        return $name;
    }
    //数组转xml
    public static function ArrToXml($arr)
    {
        $xml = "<root>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . self::ArrToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</root>";
        return $xml;
    }
    public static function setAccessAllow()
    {
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
    }
    public static function dlog($content, $title = "提示信息", $filename = null, $recordsize = null, $outmode = null)
    {
        date_default_timezone_set('PRC');

        if ($filename == null) {
            $filename = self::$dlog_filename;
        }
        if ($recordsize == null) {
            $recordsize = self::$dlog_recordsize;
        }
        if ($outmode == null) {
            $outmode = self::$dlog_outmode;
        }
        $mark = 'str';
        if (is_array($content)) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
            $mark = 'arr';
        }

        $len = strlen($content);

        $data = debug_backtrace();
        $func  = "\e[36m"; //
        for ($i = count($data) - 1; $i > 0; $i--) {
            if (!in_array($data[$i]['function'], ['runAll', 'forkWorkers', 'forkWorkersForLinux', 'forkOneWorkerForLinux', 'run', 'loop', 'call_user_func_array', 'baseRead', 'call_user_func', 'close', 'destroy'])) {
                $func .=   (" " . $data[$i]['function']) . " " ?? "";
            }
        }
        $func .= "\e[37m";
        $data = $data[0];
        $dt = date("Y/m/d H:i:s");
        $dtext = date("YmdHis");

        $file = basename($data['file']);
        $line = $data['line'];

        if (isset($recordsize) && $len > $recordsize) {
            $tmp_content = substr($content, 0, $recordsize - 1) . PHP_EOL . "...省略(数据长度大于配置长度: {$len} > {" . $recordsize . "} )...";
        } else {
            $tmp_content = $content;
        }
        $record = "$dt file:$file func:$func line:$line pid:" . getmypid() . PHP_EOL . "=====$title($len)[$mark]=====" . PHP_EOL . $tmp_content . PHP_EOL . PHP_EOL;
        if (file_exists($filename)) {
            clearstatcache(); /*清缓存，否则常驻php程序的filesize得到的结果不会有变化*/
            $filesize = abs(filesize($filename));
            if ($filesize > 30720000) { /*大于30M*/
                $flag = strpos($filename, './');
                if ($flag === false) {
                    $refilename = '.' . $filename . '.' . $dtext;
                } else {
                    $refilename = str_replace('./', './.', $filename) . '.' . $dtext;
                }
                rename($filename, $refilename); /*文件更名*/
            }
        }

        if ($outmode == 'file') {
            file_put_contents($filename, $record, FILE_APPEND);
        } elseif ($outmode == 'show') {
            echo $record;
        } elseif ($outmode == 'all') {
            echo $record;
            file_put_contents($filename, $record, FILE_APPEND);
        }
    }


    public static function getToken()
    { //获取全局唯一token
        $Token = date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        return $Token;
    }

    public static function get_show_hex($content)
    {
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',', '.', ';', ':', '/', '?', '|', ' ',
        );

        $arr = str_split($content, 1);
        $tmpstr = '0000h: ';
        $i = 1;
        $j = 0;
        $tmp2 = '; ';
        foreach ($arr as $key => $value) {
            if (in_array($value, $chars)) {
                $tmp2 = $tmp2 . $value;
            } else {
                $tmp2 = $tmp2 . '.';
            }
            $tmpstr = $tmpstr . bin2hex($value) . ' ';
            $i++;
            $j++;
            if ($i > 16) {
                $tmp1 = sprintf("%04xh: ", $j);

                $tmpstr = $tmpstr . $tmp2 . PHP_EOL . $tmp1;
                $i = 1;

                $tmp1 = '';
                $tmp2 = '; ';
            }
        }
        $len = strlen($tmp2) - 2;
        if ($len) {
            $tmpstr = $tmpstr . str_repeat(' ', (16 - $len) * 3) . $tmp2;
        } else {
            $tmpstr = substr($tmpstr, 0, strlen($tmpstr) - 8);
        }
        $buflen = count($arr);
        $bufleninfo = sprintf('长度:%d字节(%xh)', $buflen, $buflen);
        return $bufleninfo . PHP_EOL . $tmpstr;
    }

    public static function getshowdata($content) //获取可显示字符串，如果是乱码则转为十六进制字符串

    {
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',', '.', ';', ':', '/', '?', '|', ' ',
        );

        $strArr = str_split($content, 1);
        $flag = false;
        foreach ($strArr as $key => $value) {
            if (!in_array($value, $chars)) {
                $flag = true;
                break;
            }
        }

        if ($flag) {
            $string = '<HEX>' . bin2hex($content);
        } else {
            $string = $content;
        }
        return $string;
    }

    public static function asc_to_bcd($asc)
    {
        if (preg_match('/^[a-z]+$/', $asc)) { //如果全是小写
            $bcd = hex2bin($asc);
        } else {
            $bcd = pack("H*", $asc);
        }
        return $bcd;
    }

    public static function bcd_to_asc($bcd)
    {
        $arr = str_split($bcd, 1);
        $asc = bin2hex(implode('', $arr));
        return $asc;
    }

    public static function getArrBcdValue($arr)
    {
        $value = 0;
        $count = count($arr);
        $tmpstr = '';
        for ($i = 0; $i < $count; $i++) {
            $tmpstr = $tmpstr . bin2hex($arr[$i]);
        }
        $value = intval($tmpstr);
        return $value;
    }

    public static function getArrBcdStr($arr)
    {
        return bin2hex(implode('', $arr));
    }

    public static function getArrAscStr($arr)
    {
        return implode('', $arr);
    }

    /**
     * 生成心跳包
     * @param string $msg   心跳包标识
     * @param int $len      心跳包长度
     * @return array        心跳包数组
     */
    public static function getHeartbeatPacket($msg = '0000', $len = null)
    {
        $heartbeat = array(
            'ORDER' => 'HEARTBEAT',
            'METHOD' => 'BASE64',
        );
        if ($len == null) {
            $len = strlen($msg);
        }
        $msg = substr($msg, 0, $len);
        $lenstr = sprintf("%04d", $len);
        $str = hex2bin($lenstr) . $msg;
        $heartbeat['PACKET'] = base64_encode($str);
        //return json_encode($heartbeat);
        return $heartbeat;
    }

    /**
     * 判断心跳包
     * @param string $buffer    心跳包数据
     * @param string $msg       心跳包标识
     * @param int $len          心跳包长度
     * @return bool             是否心跳包
     */
    public static function isHeartbeatPacket($buffer, $msg = '0000', $len = null)
    {
        if ($len == null) {
            $len = strlen($msg);
        }
        $msg = substr($msg, 0, $len);
        $lenstr = sprintf("%04d", $len);
        $str = hex2bin($lenstr) . $msg;
        $data1 = base64_encode($str);
        $data2 = base64_encode($buffer);
        if ($data1 == $data2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成指令包
     * @param string $order 指令
     * @param array $paras  参数
     * @return array        指令包数组
     */
    public static function getOrderPacket($order, $paras)
    {
        $message = array();
        $message['ORDER'] = $order;
        foreach ($paras as $key => $value) {
            $message[$key] = $value;
        }
        $len = strlen($order);
        $lenstr = sprintf("%04d", $len);
        $msg = hex2bin($lenstr) . $order;
        $message['METHOD'] = 'BASE64';
        $message['PACKET'] = base64_encode($msg);
        //return json_encode($message);
        return $message;
    }

    /* PHP CURL HTTPS GET */
    public static function curl_get_https($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl); //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo; //返回json对象
    }

    /* PHP CURL HTTPS POST */
    public static function curl_post_https($url, $data = [])
    { // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            self::dlog($url . ' Errno ' . curl_error($curl)); //捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

    /* PHP CURL HTTPS POST JSON*/
    public static function curl_post_json_https($url, $data)
    { // 模拟提交数据函数
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        );
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLINFO_HEADER_OUT, true); // 在执行curl_execl后通过curl_getinfo函数可获取到请求报文的头信息

        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            self::dlog('Errno' . curl_error($curl)); //捕抓异常
        }

        //$postinfo = curl_getinfo($curl, CURLINFO_HEADER_OUT);
        //echo $postinfo . $data . PHP_EOL . PHP_EOL;

        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

    /**
     * utf8字符转换成Unicode字符
     * @param [type] $utf8_str Utf-8字符
     * @return [type] Unicode字符
     */
    public static function utf8_str_to_unicode($utf8_str)
    {
        $unicode = 0;
        $unicode = (ord($utf8_str[0]) & 0x1F) << 12;
        $unicode |= (ord($utf8_str[1]) & 0x3F) << 6;
        $unicode |= (ord($utf8_str[2]) & 0x3F);
        return dechex($unicode);
    }

    /**
     * Unicode字符转换成utf8字符
     * @param [type] $unicode_str Unicode字符
     * @return [type] Utf-8字符
     */
    public static function unicode_to_utf8($unicode_str)
    {
        $utf8_str = '';
        $code = intval(hexdec($unicode_str));
        //这里注意转换出来的code一定得是整形，这样才会正确的按位操作
        $ord_1 = decbin(0xe0 | ($code >> 12));
        $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
        $ord_3 = decbin(0x80 | ($code & 0x3f));
        $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
        return $utf8_str;
    }

    public static function soap_to_xmlobj($xmlStr, $flag_before = '<Service>', $flag_after = '</Service>')
    {
        if (strpos($xmlStr, $flag_before) === false || strpos($xmlStr, $flag_after) === false) {
            return false;
        }
        $xmlStr = substr($xmlStr, strripos($xmlStr, $flag_before));
        $xmlStr = substr($xmlStr, 0, strrpos($xmlStr, $flag_after) + strlen($flag_after));
        $xmlObj = simplexml_load_string($xmlStr);
        return $xmlObj;
    }

    //判断数据不是JSON格式:
    public static function is_not_json($str)
    {
        return is_null(json_decode($str));
    }

    //判断数据是合法的json数据: (PHP版本大于5.3)
    public static function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    //生成随机字符串
    public static function randomkeys($length)
    {
        $pattern = '0123456789ABCDEF';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{
                mt_rand(0, 15)}; //生成php随机数
        }
        return $key;
    }

    public static function oci_query($sql)
    {
        // 2）。直接使用 oci 方式 读取oracle数据：

        $dbstr = "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST =192.168.1.109)(PORT = 1521))
        (CONNECT_DATA =
        (SERVER = DEDICATED)
        (SERVICE_NAME = orcl)
        (INSTANCE_NAME = orcl)))";
        $dbstr = " (DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.129.159)(PORT = 1521))
        (CONNECT_DATA =
          (SERVER = DEDICATED)
          (SID = ORCL)
        )
      )";
        $conn = oci_connect('membersys', 'membersys', $dbstr, 'utf8');

        if (!$conn) {
            $e = oci_error();
            print htmlentities($e['message']);
            exit;
        }

        $statement = oci_parse($conn, $sql);
        oci_execute($statement);

        $res = [];
        while ($row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $res[] = $row;
        }

        oci_free_statement($statement);
        oci_close($conn);

        return $res;
    }
    public static function procedure()
    {
        //入参
        $in1 = '2017-01-01'; //必填
        $in2 = '2017-08-01'; //必填
        $in3 = ''; //选填

        $tns = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 0.0.0.0)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = dbname) (SID = dbname)))';
        $conn = oci_connect('user', 'password', $tns, 'utf8');
        // if (! $conn ) {
        // 　　$e = oci_error ();
        // 　　trigger_error ( htmlentities ( $e [ 'message' ]), E_USER_ERROR );
        // }
        $curs = oci_new_cursor($conn);
        $stmt = oci_parse($conn, "begin PACKAGE_NAME.PROCEDURE_NAME(:IN1,:IN2,:IN3,:OUT1); end;");
        oci_bind_by_name($stmt, ':IN1', $in1);
        oci_bind_by_name($stmt, ':IN2', $in2);
        oci_bind_by_name($stmt, ':IN3', $in2);
        oci_bind_by_name($stmt, ":OUT1", $curs, -1, SQLT_RSET);
        oci_execute($stmt);
        oci_execute($curs, OCI_DEFAULT);

        while (($row = oci_fetch_row($curs)) != false) {
            // 　　var_dump($row);
        }
        oci_free_statement($stmt);
        oci_free_statement($curs);
        oci_close($conn);
    }
}
