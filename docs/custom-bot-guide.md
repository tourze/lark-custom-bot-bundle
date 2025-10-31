# 自定义机器人使用指南
最后更新于 2025-03-26

> **官方文档：** [自定义机器人使用指南 - 飞书开放平台](https://open.feishu.cn/document/client-docs/bot-v3/add-custom-bot)

本文介绍如何在飞书群组中创建和使用自定义机器人，通过 Webhook 实现消息推送。

## 什么是自定义机器人

自定义机器人是一种无需管理员审核即可在群聊中使用的消息推送工具。它具有以下特点：

- **无需审核**：可以直接在群组设置中添加，无需经过企业管理员审核
- **简单配置**：只需要获取 Webhook 地址即可开始使用
- **单向推送**：仅支持向群组推送消息，不支持接收和处理用户消息
- **群组限制**：只能在被添加的群组中使用，不支持跨群组或单聊

## 使用场景

自定义机器人适用于以下场景：

- **临时通知**：临时性的群组消息推送
- **系统监控**：服务器监控告警、系统状态通知
- **数据推送**：定期的数据报表、统计信息推送
- **自动化脚本**：脚本执行结果通知
- **第三方集成**：简单的第三方系统消息推送

## 创建自定义机器人

### 步骤一：进入群组设置

1. 打开需要添加机器人的飞书群组
2. 点击群组右上角的设置图标
3. 选择"群设置"

### 步骤二：添加机器人

1. 在群设置页面，找到"机器人"选项
2. 点击"添加机器人"
3. 选择"自定义机器人"
4. 填写机器人信息：
   - **机器人名称**：显示在群组中的名称
   - **描述**：机器人的功能描述（可选）

### 步骤三：安全设置

为了保证消息推送的安全性，可以配置以下安全策略：

#### 1. 签名校验
```bash
# 设置签名密钥
SECRET="your_secret_key"

# 生成签名（示例）
timestamp=$(date +%s)
string_to_sign="${timestamp}\n${SECRET}"
sign=$(echo -ne "${string_to_sign}" | openssl dgst -sha256 -hmac "${SECRET}" -binary | base64)
```

#### 2. 关键词过滤
- 设置必须包含的关键词
- 只有包含指定关键词的消息才会被发送

#### 3. IP 白名单
- 限制只有特定 IP 地址才能发送消息
- 提高安全性

### 步骤四：获取 Webhook 地址

完成配置后，系统会生成一个 Webhook 地址，格式如下：
```
https://open.feishu.cn/open-apis/bot/v2/hook/your_webhook_token
```

## 发送消息

### 基本消息格式

向 Webhook 地址发送 POST 请求即可推送消息：

```bash
curl -X POST \
  https://open.feishu.cn/open-apis/bot/v2/hook/your_webhook_token \
  -H 'Content-Type: application/json' \
  -d '{
    "msg_type": "text",
    "content": {
      "text": "这是一条测试消息"
    }
  }'
```

### 支持的消息类型

#### 1. 文本消息

```json
{
  "msg_type": "text",
  "content": {
    "text": "这是一条文本消息"
  }
}
```

#### 2. 富文本消息

```json
{
  "msg_type": "post",
  "content": {
    "post": {
      "zh_cn": {
        "title": "项目更新通知",
        "content": [
          [
            {
              "tag": "text",
              "text": "项目进度：",
              "style": ["bold"]
            },
            {
              "tag": "text",
              "text": "已完成 80%"
            }
          ],
          [
            {
              "tag": "a",
              "text": "查看详情",
              "href": "https://example.com/project"
            }
          ]
        ]
      }
    }
  }
}
```

#### 3. 图片消息

```json
{
  "msg_type": "image",
  "content": {
    "image_key": "img_v2_041b28e3-5680-48c2-9af2-497ace79333g"
  }
}
```

#### 4. 卡片消息

```json
{
  "msg_type": "interactive",
  "card": {
    "elements": [
      {
        "tag": "div",
        "text": {
          "content": "**监控告警**\n服务器 CPU 使用率过高",
          "tag": "lark_md"
        }
      },
      {
        "actions": [
          {
            "tag": "button",
            "text": {
              "content": "查看详情",
              "tag": "lark_md"
            },
            "url": "https://example.com/alert",
            "type": "default",
            "value": {}
          }
        ],
        "tag": "action"
      }
    ],
    "header": {
      "title": {
        "content": "系统告警",
        "tag": "plain_text"
      }
    }
  }
}
```

### @用户或@所有人

```json
{
  "msg_type": "text",
  "content": {
    "text": "<at user_id=\"all\">所有人</at> 请注意系统维护通知"
  }
}
```

## 使用 Symfony Bundle

本项目提供了 `lark-custom-bot-bundle` 来简化自定义机器人的使用：

### 创建 Webhook 配置

```php
use LarkCustomBotBundle\Entity\WebhookUrl;

$webhook = new WebhookUrl();
$webhook->setName('监控告警机器人');
$webhook->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/your_webhook_token');
$webhook->setRemark('服务器监控告警通知');
$webhook->setValid(true);

$entityManager->persist($webhook);
$entityManager->flush();
```

### 发送文本消息

```php
use LarkCustomBotBundle\Entity\TextMessage;

$textMessage = new TextMessage();
$textMessage->setWebhookUrl($webhook);
$textMessage->setContent('服务器 CPU 使用率异常，当前使用率: 95%');

$entityManager->persist($textMessage);
$entityManager->flush();
// 消息会自动发送，无需手动调用发送方法
```

### 发送富文本消息

```php
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\ValueObject\PostParagraph;
use LarkCustomBotBundle\ValueObject\PostNode;

$postMessage = new PostMessage();
$postMessage->setWebhookUrl($webhook);
$postMessage->setTitle('系统监控报告');

// 创建段落和节点
$paragraph1 = new PostParagraph();
$paragraph1->addNode(new PostNode('text', '服务器状态: ', ['bold']));
$paragraph1->addNode(new PostNode('text', '正常'));

$paragraph2 = new PostParagraph();
$paragraph2->addNode(new PostNode('text', 'CPU使用率: ', ['bold']));
$paragraph2->addNode(new PostNode('text', '45%'));

$postMessage->addParagraph($paragraph1);
$postMessage->addParagraph($paragraph2);

$entityManager->persist($postMessage);
$entityManager->flush();
```

### 发送图片消息

```php
use LarkCustomBotBundle\Entity\ImageMessage;

$imageMessage = new ImageMessage();
$imageMessage->setWebhookUrl($webhook);
$imageMessage->setImageKey('img_v2_041b28e3-5680-48c2-9af2-497ace79333g');

$entityManager->persist($imageMessage);
$entityManager->flush();
```

## 安全配置

### 使用签名校验

```php
use LarkCustomBotBundle\Service\LarkRequestService;

// 在发送消息时自动添加签名
$requestService = new LarkRequestService();
$requestService->sendMessageWithSign($webhook, $messageData, $secret);
```

### 配置安全策略

```php
// 在 webhook 配置中设置安全选项
$webhook->setSecretKey('your_secret_key');
$webhook->setRequiredKeywords(['监控', '告警']); // 设置必须包含的关键词
$webhook->setAllowedIps(['192.168.1.100', '192.168.1.101']); // IP 白名单
```

## 最佳实践

### 1. 消息格式规范

建议为不同类型的通知制定统一的消息格式：

```php
class AlertMessageBuilder
{
    public function buildServerAlert(string $serverName, string $alertType, string $value): PostMessage
    {
        $message = new PostMessage();
        $message->setTitle("【服务器告警】{$alertType}");
        
        $timeParagraph = new PostParagraph();
        $timeParagraph->addNode(new PostNode('text', '时间: ', ['bold']));
        $timeParagraph->addNode(new PostNode('text', date('Y-m-d H:i:s')));
        
        $serverParagraph = new PostParagraph();
        $serverParagraph->addNode(new PostNode('text', '服务器: ', ['bold']));
        $serverParagraph->addNode(new PostNode('text', $serverName));
        
        $valueParagraph = new PostParagraph();
        $valueParagraph->addNode(new PostNode('text', '当前值: ', ['bold']));
        $valueParagraph->addNode(new PostNode('text', $value, ['bold']));
        
        $message->addParagraph($timeParagraph);
        $message->addParagraph($serverParagraph);
        $message->addParagraph($valueParagraph);
        
        return $message;
    }
}
```

### 2. 错误处理和重试

```php
use LarkCustomBotBundle\EventSubscriber\MessageListener;

class CustomMessageListener extends MessageListener
{
    protected function handleSendFailure(\Exception $exception, $message): void
    {
        // 记录失败日志
        $this->logger->error('消息发送失败', [
            'message_id' => $message->getId(),
            'error' => $exception->getMessage()
        ]);
        
        // 标记消息状态
        $message->setStatus('failed');
        $message->setFailureReason($exception->getMessage());
        
        // 如果是网络错误，可以考虑重试
        if ($this->isRetryableError($exception)) {
            $this->scheduleRetry($message);
        }
    }
}
```

### 3. 频率控制

```php
class RateLimitedWebhook extends WebhookUrl
{
    private array $lastSentTimes = [];
    
    public function canSend(): bool
    {
        $now = time();
        $lastSent = $this->lastSentTimes[$this->getId()] ?? 0;
        
        // 限制每分钟最多发送 10 条消息
        if ($now - $lastSent < 6) {
            return false;
        }
        
        $this->lastSentTimes[$this->getId()] = $now;
        return true;
    }
}
```

## 常见问题

### Q: 自定义机器人和应用机器人有什么区别？

A: 主要区别：
- **审核流程**：自定义机器人无需审核，应用机器人需要管理员审核
- **功能范围**：自定义机器人只能推送消息，应用机器人支持双向交互
- **使用范围**：自定义机器人仅限当前群组，应用机器人可跨群组使用
- **权限要求**：自定义机器人无需额外权限，应用机器人需要申请相应权限

### Q: 为什么消息发送失败？

A: 常见原因：
- Webhook 地址错误或过期
- 消息格式不正确
- 触发了安全策略（签名校验、关键词过滤等）
- 群组已删除或机器人被移除

### Q: 如何在多个群组中使用相同的机器人？

A: 自定义机器人无法跨群组使用，需要在每个群组中分别创建。如果需要跨群组功能，建议使用应用机器人。

### Q: 可以获取群组成员信息吗？

A: 自定义机器人无法获取群组成员信息，也无法接收用户消息。如需这些功能，请使用应用机器人。

## 相关链接

- [Bundle 实体设计文档](../ENTITY.md)
- [工作流程说明](../WORKFLOW.md)
- [飞书开放平台文档](https://open.feishu.cn/document)