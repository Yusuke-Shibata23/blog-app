# Dockerで開発環境を構築する方法

## はじめに

Dockerを使用することで、一貫性のある開発環境を簡単に構築できます。この記事では、Dockerを使用して開発環境を構築する方法について、具体的な手順とベストプラクティスを解説します。

![Docker Composeの設定例](/storage/posts/4/docker-compose.png)
*開発環境のDocker Compose設定例*

## Dockerの基本概念

1. コンテナ
2. イメージ
3. Dockerfile
4. Docker Compose

## 開発環境の構築手順

### 1. Dockerのインストール

```bash
# Windowsの場合
# Docker Desktop for Windowsをインストール

# Linuxの場合
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
```

### 2. Dockerfileの作成

```dockerfile
FROM php:8.2-fpm

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# PHPの拡張機能のインストール
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
```

### 3. Docker Composeの設定

```yaml
version: '3'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - .:/var/www/html
    networks:
      - app-network

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: laravel
      MYSQL_ROOT_PASSWORD: secret
    networks:
      - app-network

  nginx:
    image: nginx:alpine
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    networks:
      - app-network

networks:
  app-network:
    driver: bridge
```

## 開発環境の管理

### 1. コンテナの起動と停止

```bash
# 開発環境の起動
docker-compose up -d

# 開発環境の停止
docker-compose down
```

### 2. ログの確認

```bash
# 全てのコンテナのログを表示
docker-compose logs

# 特定のサービスのログを表示
docker-compose logs app
```

### 3. コンテナ内でのコマンド実行

```bash
# PHPコンテナでComposerを実行
docker-compose exec app composer install

# データベースの操作
docker-compose exec db mysql -u root -p
```

## トラブルシューティング

1. ポート競合の解決
2. パーミッションの設定
3. ネットワークの問題
4. ボリュームの管理

## ベストプラクティス

1. 環境変数の活用
2. マルチステージビルド
3. キャッシュの最適化
4. セキュリティ対策

## まとめ

Dockerを使用することで、一貫性のある開発環境を簡単に構築・管理できます。このガイドで紹介した方法とベストプラクティスを参考に、効率的な開発環境を構築してください。

## 参考リンク

- [Docker公式ドキュメント](https://docs.docker.com/)
- [Docker Compose公式ドキュメント](https://docs.docker.com/compose/)
- [Dockerセキュリティガイド](https://docs.docker.com/engine/security/) 