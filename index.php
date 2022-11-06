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
function unicode2Chinese($str)
{
    return preg_replace_callback("#\\\u([0-9a-f]{4})#i",
        function ($r) {return iconv('UCS-2BE', 'UTF-8', pack('H4', $r[1]));},
        $str);
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
function sougou(){
    $data = c("https://hotlist.imtt.qq.com/Fetch","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['main'][$i]['title'] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $data['main'][$i]['title'],
                'heat_score' => $data['main'][$i]['score'],
                'url' => $data['main'][$i]['url']
            );
        }
    }
    return $result;
}
function txnews(){
    $data = c("https://hotlist.imtt.qq.com/Fetch","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['tencent'][$i]['title'] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $data['tencent'][$i]['title'],
                'heat_score' => $data['tencent'][$i]['score'],
                'url' => $data['tencent'][$i]['url']
            );
        }
    }
    return $result;
}
function toutiao(){
    $data = c("https://www.toutiao.com/hot-event/hot-board/?origin=toutiao_pc","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['data'][$i]['Title'] != null){
            $url = str_replace("\u0026","&",$data['data'][$i]['Url']);
            $result[$i] = array(
                'index' => $i+1,
                'title' => $data['data'][$i]['Title'],
                'heat_score' => $data['data'][$i]['HotValue'],
                'url' => $url
            );
        }
    }
    return $result;
}
function thepaper(){
    $data = c("https://cache.thepaper.cn/contentapi/wwwIndex/rightSidebar","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['data']['hotNews'][$i]['name'] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $data['data']['hotNews'][$i]['name'],
                'url' => 'https://www.thepaper.cn/newsDetail_forward_'.$data['data']['hotNews'][$i]['contId']
            );
        }
    }
    return $result;
}
function so360(){
    $data = c("https://news.so.com/hotnews?src=hotnews","PC");
    $hot = zhengze($data, '/<span class="title">(.*)<\/span>/m');
    $hot_score = zhengze($data, '/<span class="hot">(.*)<\/span>/m');
    for($i=0;$i<=100;$i++){
        if($hot[$i][1] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $hot[$i][1],
                'heat_score' => $hot_score[$i][1],
                'url' => "http://www.so.com/s?q=".$hot[$i][1]
            );
        }
    }
    return $result;
}
function pearvideo(){
    $data = c("https://www.pearvideo.com/popular","PC");
    $hot = zhengze($data, '/<h2 class="popularem-title">(.*)<\/h2>/m');
    $id = zhengze($data, '/<a href="(.*)" class="popularembd actplay"/m');
    for($i=0;$i<=100;$i++){
        if($hot[$i][1] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $hot[$i][1],
                'url' => "https://www.pearvideo.com/".$id[$i][1]
            );
        }
    }
    return $result;
}
function haokan(){
    $data = c("https://haokan.baidu.com/videoui/api/hotwords?sfrom=pc","PC");
    $data = json_decode($data,true);
    for($i=0;$i<=100;$i++){
        if($data['data']['response']['hotwords'][$i]['title'] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $data['data']['response']['hotwords'][$i]['title'],
                'heat_score' => $data['data']['response']['hotwords'][$i]['hot_num'],
                'url' => 'https://haokan.baidu.com/web/search/page?query='.$data['data']['response']['hotwords'][$i]['title']
            );
        }
    }
    return $result;
}
function haokanvideo(){
    $data = c("https://haokan.baidu.com/videoui/page/pc/toplist?type=hotvideo&sfrom=","PC");
    $hot = zhengze($data, '/"vid":"\w+","title":"(.*?)"/m');
    $url = zhengze($data, '/"pageUrl":"(.*?)"/m');
    $video_url = zhengze($data, '/"videoUrl":"(.*?)"/m');
    $hot_score = zhengze($data, '/"hot":"(.*?)"/m');
    for($i=0;$i<=100;$i++){
        if($hot[$i][1] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => unicode2Chinese($hot[$i][1]),
                'heat_score' => $hot_score[$i][1],
                'url' => $url[$i][1],
                'video_url' => $video_url[$i][1]
            );
        }
    }
    return $result;
}
function bjnews(){
    $data = c("https://www.bjnews.com.cn/","PC");
    $hot = zhengze($data, '/<span class="num">\w+<\/span>[\W\w](.*?) <\/a>/m');
    $url = zhengze($data, '/<a class="link" href="(.*)" target=\'_blank\'>[\w\W]<span class="num">\w+<\/span>[\w\W].+ <\/a>/m');
    $category = zhengze($data, '/<span class="source">(.*)<\/span>/m');
    $hot_score = zhengze($data, '/<span class="com"><i class="pai"><\/i>(.*)<\/span>/m');
    for($i=0;$i<=9;$i++){
        if($hot[$i][1] != null){
            $result[$i] = array(
                'index' => $i+1,
                'title' => $hot[$i][1],
                'category' => $category[$i][1],
                'heat_score' => $hot_score[$i][1],
                'url' => $url[$i][1],
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
        "msg" => '没有填写类型'
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
else if($type == "sougou"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => sougou()
    );
}
else if($type == "txnews"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => txnews()
    );
}
else if($type == "toutiao"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => toutiao()
    );
}
else if($type == "thepaper"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => thepaper()
    );
}
else if($type == "360so"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => so360()
    );
}
else if($type == "pearvideo"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => pearvideo()
    );
}
else if($type == "haokan"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => haokan()
    );
}
else if($type == "haokanvideo"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => haokanvideo()
    );
}
else if($type == "bjnews"){
    $json = array(
        "code" => 200, 
        "msg" => '解析成功',
        "data" => bjnews()
    );
}
else{
    $json = array(
        "code" => 202, 
        "msg" => '填写类型错误',
    );
}
echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
