<?php
error_reporting(0);
header("Content-Type: text/html;charset=utf-8");
function c($url, $ua){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    $headers = array(
        "accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if ($ua == "Mobile") {
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Android 4.4; Mobile; rv:70.0) Gecko/70.0 Firefox/70.0");
    } else if($ua == "PC"){
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36 Edg/107.0.1418.35");
    } else if($ua == "Spider"){
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
    } else {
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function zhengze($text , $regex){
    preg_match_all($regex, $text, $somatches , PREG_SET_ORDER, 0);
    return $somatches;
}
function baidu(){
    $data = c("https://www.baidu.com","PC");
    $hot = zhengze($data, '/card_title": "(.*?)"/m');
    $hot_score = zhengze($data, '/heat_score": "(.*?)"/m');
    $index = zhengze($data, '/index": "(.*?)"/m');
    for($i=0;$i<=100;$i++){
        if($hot[$i][1] != null){
            $result[$i] = array(
                'index' => $index[$i][1],
                'title' => $hot[$i][1],
                'heat_score' => $hot_score[$i][1],
                'url' => "https://www.baidu.com/s?wd=".$hot[$i][1]
            );
        }
    }
    return $result;
}
function weibo(){
    $data = c("https://weibo.com/ajax/statuses/hot_band","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['data']['band_list'][$i]['num'] != null){
            $result[$i] = array(
                'index' => $data['data']['band_list'][$i]['realpos'],
                'title' => $data['data']['band_list'][$i]['word'],
                'desc' => $data['data']['band_list'][$i]['icon_desc'],
                'category' => isset($data['data']['band_list'][$i]['category']) ? $data['data']['band_list'][$i]['category'] : $data['data']['band_list'][$i]['ad_type'],
                'heat_score' => isset($data['data']['band_list'][$i]['raw_hot']) ? $data['data']['band_list'][$i]['raw_hot'] : 0,
                'url' => "https://s.weibo.com/weibo?q=%23".$data['data']['band_list'][$i]['word'].'%23'
            );
        }
    }
    return $result;
}
function bilibili(){
    $data = c("https://api.bilibili.com/x/web-interface/search/square?limit=50&platform=web","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['data']['trending']['list'][$i]['keyword'] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $data['data']['trending']['list'][$i]['keyword'],
                'url' => 'https://search.bilibili.com/all?keyword='.$data['data']['trending']['list'][$i]['keyword']
            );
        }
    }
    return $result;
}
function bilibilivideo(){
    $data = c("https://www.bilibili.com/v/popular/rank/all","PC");
    $hot = zhengze($data, '/class="title">(.*)<\/a>/m');
    $hot_score = zhengze($data, '/alt="play">\n\s+([\w\W]*?)\n\s+<\/span>/m');
    $BV = zhengze($data, '/<a href="\/\/www.bilibili.com\/video\/(.*?)" target="_blank">/m');
    $up = zhengze($data, '/alt="up">\n\s+[\w\W]*?(.*)\n\s+<\/span>/m');
    $danmu = zhengze($data, '/alt="like">\n\s+[\w\W]*?(.*)\n\s+<\/span>/m');
    for($i=0;$i<=100;$i++){
        if($hot[$i][1] != null){
            $result[$i] = array(
                'index' => $i+1,
                'video' => array(
                    'title' => $hot[$i][1],
                    'BV' => $BV[$i][1],
                    'url' => "https://www.bilibili.com/video/".$BV[$i][1],
                    'play' => $hot_score[$i][1],
                    'up' => $up[$i][1],
                    'danmu' => $danmu[$i][1]
                )
            );
        }
    }
    return $result;
}
// Main
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
$type = isset($_GET['type']) ? $_GET['type'] : NULL;
if($type == NULL){
    $json = array(
        "code" => 201, 
        "msg" => '没有填写爬取类型'
    );
}
else if($type == "baidu"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => baidu()
    );
}
else if($type == "weibo"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => weibo()
    );
}
else if($type == "bilibili"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => bilibili()
    );
}
else if($type == "bilibilivideo"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => bilibilivideo()
    );
}
echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
