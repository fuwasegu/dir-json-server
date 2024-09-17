# Directory JSON Server

静的なディレクトリ構造を JSON Server として公開するためのツールです．
返却したいレスポンスを JSON ファイルとして配置することで，
その JSON ファイルを返却する API を簡単に公開することができます．

## 仕様

`contents/` に配置した JSON ファイルを API として公開します．ただし，ルートディレクトリは後述の yaml ファイルで変更可能です．
当該ディレクトリ直下が API のベースパスとなり，その直下に配置した JSON ファイルがそのままレスポンスとなります．

JSON ファイルは，必ず `response.json` という名前で配置してください．

本ツールは，GET のみをサポートしています．

## 利用例

コンテンツディレクトリを以下のように配置したとします．

```: dir
contents/
  users/
    1/
      profile/
        response.json
```

また，`contents/users/1/profile/response.json` に以下のような JSON データが配置されていたとします．

```: json
{
  "id": 1,
  "name": "John Doe"
}
```

このとき，`GET http://localhost/users/1/profile` にアクセスすると，

```: json
{
  "id": 1,
  "name": "John Doe"
}
```

というレスポンスが返却されます．

つまり，

- contents/ をデフォルトのルートとしてその直下が URL のパスとなる
- レスポンスにしたいデータを response.json として配置する
- パスパラメータやクエリパラメータは利用できない
  - パスパラメータは，ディレクトリ名として配置することで実現できる

### レスポンス

レスポンスの ContentType について，

- デフォルトでは application/json となる
- ルートパスのみ，HTML が返されるため，この場合は text/html となる

## サーバーの起動方法

```: sh
./dir-json serve
```

### 利用可能なルートの確認

localhost:8000 にブラウザからアクセスすると，利用可能なルートの一覧が表示されます．
こちらは，HTML で表示されます．

ルートは箇条書きに表示され，クリックすることでそのルートにアクセスすることができます．

### API レスポンス

基本的に，存在するディレクトリの末端に配置された JSON ファイルがそのままレスポンスとして返却されます．
もし，ディレクトリの末端に配置された JSON ファイルが存在しない場合は，ステータスコード 404 が返却されます．
また，このとき以下のような json が返却されます．

```: json
{
  "status": "error",
  "message": "File not found",
  "path": "contents/users/2/profile/response.json"
}
```

### デフォルトルートディレクトリの設定変更

デフォルトのルートディレクトリは，`dir-json.yaml` ファイルで変更可能です．

```: yaml
root_path: 'contents'
```

## 設計

### 採用技術

- PHP 8.3
- Composer

### ディレクトリ構造

このプロジェクトは，以下のようなディレクトリ構造を持っています（一部省略）

```: dir
root/
  contents/
  public/
    index.php
  src/
  composer.json
```

- contents/: コンテンツを配置するディレクトリ
- public/: 公開するディレクトリ. この中に index.php があり，エントリポイントとなります
- src/: サーバーのコードを配置するディレクトリ
- composer.json: 依存関係を管理するためのファイル

### 名前空間

composer.json にて定義されている通り，src/ 配下のファイルは App\ 名前空間配下に配置されます．

### アーキテクチャ

このプロジェクトは基本的にクリーンアーキテクチャを採用しています．

本アプリケーションは，DB を使わない代わりに静的なディレクトリ構造を JSON Server として公開するため，インフラ層ではファイルアクセスを行います．
