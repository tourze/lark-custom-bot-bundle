# 飞书自定义机器人（Lark Custom Bot Bundle）

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/lark-custom-bot-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lark-custom-bot-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/lark-custom-bot-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lark-custom-bot-bundle)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/lark-custom-bot-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lark-custom-bot-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

## 简介

本 Bundle 用于在 Symfony 项目中集成和管理飞书自定义机器人，支持多种消息类型（文本、图片、群分享、富文本、卡片），
并通过数据库实体进行统一管理和消息发送。

## 功能特性

- 支持多种飞书消息类型：文本、图片、群分享、富文本（post）、卡片（interactive）
- 消息与 Webhook 配置分离，支持多机器人管理
- 消息实体持久化，支持消息历史追溯
- 消息发送自动化（监听消息实体持久化事件自动推送）
- 丰富的实体设计，便于扩展和二次开发

## 依赖要求

- PHP >= 8.1
- Symfony >= 7.3
- Doctrine ORM >= 3.0

## 安装说明

### 安装步骤

```bash
composer require tourze/lark-custom-bot-bundle
```

### 配置

1. 配置数据库连接，执行实体迁移。
2. 在 `config/bundles.php` 注册 Bundle：

```php
LarkCustomBotBundle\LarkCustomBotBundle::class => ['all' => true],
```

3. 按需自定义 `services.yaml`。

## 快速开始

### 创建 Webhook 配置

```php
$webhook = new WebhookUrl();
$webhook->setName('通知机器人');
$webhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/xxx');
$webhook->setRemark('主通知机器人');
$webhook->setValid(true);
```

### 发送文本消息

```php
$text = new TextMessage();
$text->setWebhookUrl($webhook);
$text->setContent('Hello, 飞书机器人！');
$entityManager->persist($text);
$entityManager->flush();
// 消息自动推送，无需手动调用发送接口
```

### 发送图片、富文本、卡片消息等请参考实体设计文档

## 高级用法

### 自定义消息类型

您可以扩展基础的 `AbstractMessage` 类来创建自定义消息类型：

```php
use LarkCustomBotBundle\Entity\AbstractMessage;

class CustomMessage extends AbstractMessage
{
    public function getType(): string
    {
        return 'custom';
    }

    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                // 您的自定义消息内容
            ],
        ];
    }
}
```

### Webhook 管理

管理多个 Webhook 配置以适应不同场景：

```php
// 开发环境机器人
$devWebhook = new WebhookUrl();
$devWebhook->setName('开发通知');
$devWebhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/dev-xxx');

// 生产环境机器人
$prodWebhook = new WebhookUrl();
$prodWebhook->setName('生产告警');
$prodWebhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/prod-xxx');
```

### 事件监听器自定义

Bundle 包含一个自动发送消息的事件监听器。
您可以在服务配置中自定义或禁用此行为。

### 消息验证

所有消息实体都包含内置的验证约束。
在持久化之前确保您的数据通过验证：

```php
use Symfony\Component\Validator\Validator\ValidatorInterface;

$violations = $validator->validate($textMessage);
if (count($violations) > 0) {
    // 处理验证错误
}
```

## 详细文档

- [实体设计说明](./ENTITY.zh-CN.md)
- [工作流程与架构图](./WORKFLOW.zh-CN.md)

## 贡献指南

- 欢迎提交 Issue 与 PR
- 遵循 PSR-12 代码风格
- 提交前请确保通过测试

## 版权和许可

MIT License

## 更新日志

- 0.1.0: 初始版本发布
