<?php
# zloy.org
        function get_download($url){

                $ret = false;

                if( function_exists('curl_init') ){
                        if( $curl = curl_init() ){

                                if( !curl_setopt($curl,CURLOPT_URL,$url) ) return $ret;
                                if( !curl_setopt($curl,CURLOPT_RETURNTRANSFER,true) ) return $ret;
                                if( !curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,30) ) return $ret;
                                if( !curl_setopt($curl,CURLOPT_HEADER,false) ) return $ret;
                                if( !curl_setopt($curl,CURLOPT_ENCODING,"gzip,deflate") ) return $ret;

                                $ret = curl_exec($curl);

                                curl_close($curl);
                        }
                }
                else{
                        $u = parse_url($url);

                        if( $fp = @fsockopen($u['host'],!empty($u['port']) ? $u['port'] : 80 ) ){

                            $headers = 'GET '.  $u['path'] . '?' . $u['query'] .' HTTP/1.0'. "\r\n";
                            $headers .= 'Host: '. $u['host'] ."\r\n";
                            $headers .= 'Connection: Close' . "\r\n\r\n";

                            fwrite($fp, $headers);
                            $ret = '';

                                while( !feof($fp) ){
                                        $ret .= fgets($fp,1024);
                                }

                                $ret = substr($ret,strpos($ret,"\r\n\r\n") + 4);

                                fclose($fp);
                        }
                }

                return $ret;
        }

function get_yandex($url){
        $ret = 'N/A';
        if( substr($url,0,7) != 'http://' )
                $url = 'http://' . $url;
        if( $content = get_download('http://bar-navig.yandex.ru/u?ver=2&url='. urlencode($url) .'&show=1&post=0') ){

                if( class_exists('SimpleXMLElement') ){
                        if( $xmldoc = new SimpleXMLElement($content) ){
                                $tcy = $xmldoc->tcy;
                                if( !empty($tcy) ){
                                        $ret = $tcy['value'];
                                }
                        }
                }
                else{
                        preg_match("/value=\"(.\d*)\"/",$content,$tic);
                        if( !empty($tic[1]) ) $ret = $tic[1];
                }
        }

        return $ret;
}

function GetPageRank($q,$host='toolbarqueries.google.com',$context=NULL) {
        $seed = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
        $result = 0x01020345;
        $len = strlen($q);
        for ($i=0; $i<$len; $i++) {
                $result ^= ord($seed{$i%strlen($seed)}) ^ ord($q{$i});
                $result = (($result >> 23) & 0x1ff) | $result << 9;
        }
    if (PHP_INT_MAX != 2147483647) { $result = -(~($result & 0xFFFFFFFF) + 1); }
        $ch=sprintf('8%x', $result);
        $url='http://%s/tbr?client=navclient-auto&ch=%s&features=Rank&q=info:%s';
        $url=sprintf($url,$host,$ch,$q);
        @$pr=file_get_contents($url,false,$context);
        return $pr?substr(strrchr($pr, ':'), 1):false;
}

?>
