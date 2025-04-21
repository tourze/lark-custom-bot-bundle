# Lark Custom Bot Bundle

[![version](https://img.shields.io/badge/version-0.1.0-blue.svg)]() [![license: MIT](https://img.shields.io/badge/license-MIT-green.svg)]()

## Introduction

This Symfony bundle enables integration and management of Lark (Feishu) custom bots, supporting multiple message types (text, image, share chat, post, interactive card) with unified entity management and automated message delivery.

## Features

- Supports various Lark message types: text, image, share chat, post (rich text), interactive card
- Decoupled webhook configuration, allowing management of multiple bots
- Persistent message entities for traceability
- Automated message sending (entity persistence triggers push)
- Extensible entity design for easy customization

## Installation

### Requirements

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 2.20

### Install

```bash
composer require tourze/lark-custom-bot-bundle
```

### Configuration

1. Configure your database and run migrations.
2. Register the bundle in `config/bundles.php`:

```php
LarkCustomBotBundle\LarkCustomBotBundle::class => ['all' => true],
```

3. Optionally customize `services.yaml` as needed.

## Quick Start

### Create a Webhook Configuration

```php
$webhook = new WebhookUrl();
$webhook->setName('Notify Bot');
$webhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/xxx');
$webhook->setRemark('Main notification bot');
$webhook->setValid(true);
```

### Send a Text Message

```php
$text = new TextMessage();
$text->setWebhookUrl($webhook);
$text->setContent('Hello, Lark bot!');
$entityManager->persist($text);
$entityManager->flush();
// Message will be sent automatically, no manual send required
```

### For image, post, and card messages, refer to the entity design documentation

## Documentation

- [Entity Design](./ENTITY.md)
- [Workflow & Architecture Diagram](./WORKFLOW.md)

## Contributing

- Issues and PRs are welcome
- Follow PSR-12 coding style
- Ensure tests pass before submitting

## License

MIT License

## Changelog

- 0.1.0: Initial release
