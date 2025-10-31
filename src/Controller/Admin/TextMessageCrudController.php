<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\WebhookUrlRepository;

/**
 * @extends AbstractCrudController<TextMessage>
 */
#[AdminCrud(routePath: '/lark-bot/text-message', routeName: 'lark_bot_text_message')]
final class TextMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TextMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('文本消息')
            ->setEntityLabelInPlural('文本消息管理')
            ->setSearchFields(['content'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setHelp('index', '管理飞书机器人发送的文本消息')
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

        yield TextareaField::new('content', '消息内容')
            ->setRequired(true)
            ->setMaxLength(65535)
            ->setNumOfRows(8)
            ->setHelp('要发送的文本消息内容')
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
            ->add(TextFilter::new('content', '消息内容'))
            ->add(EntityFilter::new('webhookUrl', 'Webhook地址'))
        ;
    }
}
