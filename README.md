# 2021年 システム開発 前期課題 掲示板サービス 構築手順書

## 依存ソフトウェア

この掲示板サービスを構築・実行するには，以下のソフトウェアが必要です。各環境にあわせて予め導入しておいてください。

- git
- Docker
- Docker Compose

## 構築手順

### 1. ソースコードの設置

まずソースコードの設置を行います。

この掲示板サービスのソースコードはGitHub上で公開しているリポジトリ `wumoye/sys_development_php` にあります。

```sh
git clone github.com/wumoye/sys_development_php.git
cd sys_development_php
```

### 2. ビルドと起動

Docker Composeで管理するDockerコンテナ上で実行します。

```sh
docker-compose build
docker-compose up
```

### 3. テーブルの作成

データベース(MySQL)にテーブルを作成します。

起動中に，以下のコマンドでMySQLのCLIクライアントを起動してください。

```sh
docker exec -it mysql mysql techc
```

テーブルを作成するSQLは以下の通りです。

```sql
CREATE TABLE `bbs_entries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `body` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `bbs_entries` ADD COLUMN image_filename TEXT DEFAULT NULL;
```

### 4. 動作確認

掲示板のページは `/bbs.php` です。ブラウザから `http://サーバーのアドレス/bbs.php` にアクセスし，動作を確認してください。

構築手順は以上です。
