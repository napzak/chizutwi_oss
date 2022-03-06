# 位置情報からツイッターのツイートを検索するツール

https://oss.chizutwi.jp/

グーグルマップ上で場所と範囲を指定すると、そのエリア内にある直近の位置情報つきツイートを検索します。ツイッターのハッシュタグやキーワードを指定することで、ツイートをさらに絞り込むこともできます。範囲を指定せずに、特定のアカウントやキーワードで絞り込むこともできます。

## 導入方法

PHPフレームワークにCakePHP4.3を使って構築しています。データベースは使っていません。Twitter APIキーとGoogleMaps APIキーが必要です。

CakePHP4.3が動作する環境を用意し、ファイル一式を設置してください。
Twitter APIキーとGoogleMaps APIキーを /config/app_local.php に記載してください。

```bash
// Twitter API Key
define('TWITTER_CONSUMER_KEY', '');
define('TWITTER_CONSUMER_KEY_SERCRET', '');
define('TWITTER_ACCESS_TOKEN', '');
define('TWITTER_ACCESS_TOKEN_SECRET', '');

// Google Map API Key
define('GOOGLEMAP_APIKEY', '');
```
