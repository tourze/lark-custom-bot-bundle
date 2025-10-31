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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\WebhookUrlRepository;

/**
 * @extends AbstractCrudController<ImageMessage>
 */
#[AdminCrud(routePath: '/lark-bot/image-message', routeName: 'lark_bot_image_message')]
final class ImageMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImageMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('图片消息')
            ->setEntityLabelInPlural('图片消息管理')
            ->setSearchFields(['imageKey'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setHelp('index', '管理飞书机器人发送的图片消息')
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

        yield TextField::new('imageKey', '图片Key')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('飞书图片的image_key，用于标识图片')
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
            ->add(TextFilter::new('imageKey', '图片Key'))
            ->add(EntityFilter::new('webhookUrl', 'Webhook地址'))
        ;
    }
}
