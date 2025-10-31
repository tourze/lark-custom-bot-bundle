<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\WebhookUrlRepository;

/**
 * @extends AbstractCrudController<InteractiveMessage>
 */
#[AdminCrud(routePath: '/lark-bot/interactive-message', routeName: 'lark_bot_interactive_message')]
final class InteractiveMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InteractiveMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('交互消息')
            ->setEntityLabelInPlural('交互消息管理')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setHelp('index', '管理飞书机器人发送的卡片交互消息')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('webhookUrl', 'Webhook地址')
            ->setRequired(true)
            ->setFormTypeOptions([
                'choice_label' => function (WebhookUrl $webhookUrl) {
                    return $webhookUrl->getName();
                },
                'query_builder' => function (WebhookUrlRepository $repository) {
                    return $repository->createQueryBuilder('w')
                        ->where('w.valid = true')
                        ->orderBy('w.name', 'ASC')
                    ;
                },
            ])
            ->formatValue(function ($value, $entity) {
                if (!$value instanceof WebhookUrl) {
                    return '无';
                }

                return $value->getName();
            })
            ->setHelp('选择要发送消息的Webhook地址')
        ;

        yield CodeEditorField::new('card', '卡片内容')
            ->setRequired(true)
            ->setLanguage('javascript')
            ->setHelp('交互卡片的JSON内容，定义卡片的布局和交互元素')
            ->formatValue(function ($value, $entity) {
                if (is_array($value)) {
                    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }

                return $value;
            })
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('webhookUrl', 'Webhook地址'))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleCardData($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleCardData($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleCardData(mixed $entityInstance): void
    {
        if (!$entityInstance instanceof InteractiveMessage) {
            return;
        }

        $context = $this->getContext();
        if (null === $context) {
            return;
        }
        $request = $context->getRequest();
        $cardData = $request->request->get('InteractiveMessage')['card'] ?? '';

        if (is_string($cardData) && '' !== $cardData) {
            try {
                $cardArray = json_decode($cardData, true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($cardArray)) {
                    throw new \InvalidArgumentException('卡片内容必须是有效的JSON数组格式');
                }
                // 确保数组键都是字符串类型
                /** @var array<string, mixed> $validatedArray */
                $validatedArray = [];
                foreach ($cardArray as $key => $value) {
                    $validatedArray[(string) $key] = $value;
                }
                $entityInstance->setCard($validatedArray);
            } catch (\JsonException $e) {
                throw new \InvalidArgumentException('卡片内容必须是有效的JSON格式: ' . $e->getMessage());
            }
        }
    }
}
