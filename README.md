# today-hot-top

获取各大网站热搜榜

## 支持类型

| 主类型          |次类型| 解释     |
| ------------- | -------- |------|
| baidu         | |百度     |
| bilibili      | search|B 站搜索 |
| bilibili | video|B 站热门视频 |
| weibo         | |微博     |
| sougou        | |搜狗搜索 |
| txnews        | |腾讯新闻 |
| toutiao       | |今日头条 |
| thepaper      | |澎湃新闻 |
| 360so         | |360 搜索 |
| pearvideo     | |梨视频   |
| haokan        | search|好看搜索 |
| haokan   | video|好看热门视频 |
| bjnews        | |新京报   |
| 52pojie       | |吾爱破解 |
|ithome||IT之家|
| acfun        | search|A站搜索 |
| acfun   | video|A站热门视频 |

## 请求参数
| 类型          | 解释     |
| ------------- | -------- |
|type|主要类型|
|typeA|次要类型|

## 请求示例

> **?type=bilibili&typeA=video**

### 返回示例

```json
{
    "code": 200,
    "msg": "解析成功",
    "data": [
        {
            "index": 1,
            "video": {
                "title": "爆肝一个月！4w枚【订书钉】编制银鳞软甲",
                "BV": "BV1LA41117Vr",
                "url": "https://www.bilibili.com/video/BV1LA41117Vr",
                "play": "231.2万",
                "up": "玩钉子的钉子",
                "danmu": 5249
            }
        },
        {
            "index": 2,
            "video": {
                "title": "阳光开朗，但是硬核“大男孩”🔥",
                "BV": "BV1Vs4y1b7Um",
                "url": "https://www.bilibili.com/video/BV1Vs4y1b7Um",
                "play": "452.5万",
                "up": "共青团中央",
                "danmu": 3258
            }
        },
        {
            "index": 3,
            "video": {
                "title": "《原神》迪希雅角色PV——「沙际晨光」",
                "BV": "BV1vs4y1b7rU",
                "url": "https://www.bilibili.com/video/BV1vs4y1b7rU",
                "play": "244.3万",
                "up": "原神",
                "danmu": 6230
            }
        },
        {
            "index": 4,
            "video": {
                "title": "00后玩B站 VS 10后玩B站",
                "BV": "BV1cy4y1f7Xt",
                "url": "https://www.bilibili.com/video/BV1cy4y1f7Xt",
                "play": "274.2万",
                "up": "进击的金厂长",
                "danmu": 3959
            }
        },
        {.....}
    ]
}
```

## 开发日志

22.11.5 开始项目开发

22.11.6 Support 百度/B 站/微博/B 站热门视频/搜狗/腾讯新闻/今日头条/澎湃新闻/360 搜索/梨视频/好看视频热搜/好看视频热榜/新京报

22.11.20 Support 吾爱破解

23.01.15 Fix B 站热门视频

23.02.25 Support IT之家 Change 代码结构

| 返回值 | 解释         |
| ------ | ------------ |
| code   | 程序返回码   |
| msg    | 程序返回消息 |
| data   | 数据内容     |
