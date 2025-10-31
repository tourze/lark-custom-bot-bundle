# Lark Custom Bot Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/lark-custom-bot-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lark-custom-bot-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/lark-custom-bot-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lark-custom-bot-bundle)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/lark-custom-bot-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lark-custom-bot-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

## Introduction

This Symfony bundle enables integration and management of Lark (Feishu) custom bots, 
supporting multiple message types (text, image, share chat, post, interactive card) 
with unified entity management and automated message delivery.

## Features

- Supports various Lark message types: text, image, share chat, post (rich text), interactive card
- Decoupled webhook configuration, allowing management of multiple bots
- Persistent message entities for traceability
- Automated message sending (entity persistence triggers push)
- Extensible entity design for easy customization

## Requirements

- PHP >= 8.1
- Symfony >= 7.3
- Doctrine ORM >= 3.0

## Installation

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

## Advanced Usage

### Custom Message Types

You can extend the base `AbstractMessage` class to create custom message types:

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
                // Your custom message content
            ],
        ];
    }
}
```

### Webhook Management

Manage multiple webhook configurations for different scenarios:

```php
// Development bot
$devWebhook = new WebhookUrl();
$devWebhook->setName('Dev Notifications');
$devWebhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/dev-xxx');

// Production bot
$prodWebhook = new WebhookUrl();
$prodWebhook->setName('Prod Alerts');
$prodWebhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/prod-xxx');
```

### Event Listener Customization

The bundle includes an event listener that automatically sends messages. 
You can customize or disable this behavior in your services configuration.

### Message Validation

All message entities include built-in validation constraints. 
Ensure your data passes validation before persisting:

```php
use Symfony\Component\Validator\Validator\ValidatorInterface;

$violations = $validator->validate($textMessage);
if (count($violations) > 0) {
    // Handle validation errors
}
```

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
