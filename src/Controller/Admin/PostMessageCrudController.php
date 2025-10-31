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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\WebhookUrlRepository;

/**
 * @extends AbstractCrudController<PostMessage>
 */
#[AdminCrud(routePath: '/lark-bot/post-message', routeName: 'lark_bot_post_message')]
final class PostMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('富文本消息')
            ->setEntityLabelInPlural('富文本消息管理')
            ->setSearchFields(['title'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setHelp('index', '管理飞书机器人发送的富文本消息')
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

        yield TextField::new('title', '标题')
            ->setRequired(true)
            ->setMaxLength(65535)
            ->setHelp('富文本消息的标题')
        ;

        yield TextareaField::new('content', '内容')
            ->setRequired(true)
            ->setNumOfRows(10)
            ->setHelp('富文本消息的段落内容，JSON格式')
            ->formatValue(function ($value, $entity) {
                if (is_array($value)) {
                    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }

                return $value ?? '';
            })
            ->hideOnIndex()
            ->hideOnForm() // 隐藏表单，避免数组类型字段在表单中的转换问题
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
            ->add(TextFilter::new('title', '标题'))
            ->add(EntityFilter::new('webhookUrl', 'Webhook地址'))
        ;
    }
}
