# Workflow & Architecture Diagram

The automated message sending workflow of this bundle is as follows:

```mermaid
flowchart TD
    A[Create message entity<br>(e.g. TextMessage)] --> B[Persist to database]
    B --> C[Doctrine event listener]
    C --> D[MessageListener catches postPersist]
    D --> E[Build FeishuRobotRequest]
    E --> F[LarkRequestService sends HTTP request]
    F --> G[Lark platform receives & processes]
```

## Notes

- All message types (text, image, card, post, etc.) are sent automatically via entity persistence.
- Event listeners handle message delivery, making the system extensible and maintainable.
- Multiple webhook configurations are supported for flexible bot management.
