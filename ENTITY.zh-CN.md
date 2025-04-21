# 数据库实体设计说明

本模块包含如下主要实体：

## 1. WebhookUrl

- 用于存储飞书机器人的 webhook 配置。
- 字段：
  - `id`：主键，自增
  - `name`：名称
  - `url`：Webhook 地址
  - `remark`：备注
  - `valid`：是否有效
  - `createTime`：创建时间
  - `updateTime`：更新时间

## 2. AbstractMessage（抽象消息基类）

- 所有消息实体的基类，包含通用字段：
  - `id`：主键
  - `webhookUrl`：关联 WebhookUrl
  - `createTime`：创建时间
  - `updateTime`：更新时间

## 3. TextMessage

- 文本消息实体。
- 字段：
  - `content`：消息内容

## 4. ImageMessage

- 图片消息实体。
- 字段：
  - `imageKey`：图片标识

## 5. InteractiveMessage

- 卡片消息实体。
- 字段：
  - `card`：卡片内容（JSON）

## 6. PostMessage

- 富文本消息实体。
- 字段：
  - `title`：标题
  - `content`：富文本内容（JSON，数组）

## 7. ShareChatMessage

- 群分享消息实体。
- 字段：
  - `chatId`：群ID

### 设计说明

- 所有消息实体均继承自 AbstractMessage，统一管理 webhook 及时间字段。
- 消息发送采用事件监听机制，持久化后自动推送。
- 便于扩展新的消息类型。
