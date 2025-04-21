# Entity Design

This bundle contains the following main entities:

## 1. WebhookUrl

- Stores Lark bot webhook configuration.
- Fields:
  - `id`: Primary key, auto-increment
  - `name`: Name
  - `url`: Webhook address
  - `remark`: Remark
  - `valid`: Is valid
  - `createTime`: Created at
  - `updateTime`: Updated at

## 2. AbstractMessage (Base Message)

- Base class for all message entities, with common fields:
  - `id`: Primary key
  - `webhookUrl`: Reference to WebhookUrl
  - `createTime`: Created at
  - `updateTime`: Updated at

## 3. TextMessage

- Text message entity.
- Fields:
  - `content`: Message content

## 4. ImageMessage

- Image message entity.
- Fields:
  - `imageKey`: Image identifier

## 5. InteractiveMessage

- Card message entity.
- Fields:
  - `card`: Card content (JSON)

## 6. PostMessage

- Rich text message entity.
- Fields:
  - `title`: Title
  - `content`: Rich text content (JSON, array)

## 7. ShareChatMessage

- Share chat message entity.
- Fields:
  - `chatId`: Chat group ID

### Design Notes

- All message entities inherit from AbstractMessage for unified webhook and time management.
- Message sending is automated via event listeners after persistence.
- Easy to extend with new message types.
