<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LarkCustomBotBundle\Entity\WebhookUrl;

/**
 * @extends AbstractCrudController<WebhookUrl>
 */
#[AdminCrud(routePath: '/lark-bot/webhook-url', routeName: 'lark_bot_webhook_url')]
final class WebhookUrlCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WebhookUrl::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Webhook地址')
            ->setEntityLabelInPlural('Webhook地址管理')
            ->setSearchFields(['name', 'url', 'remark'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setHelp('index', '管理飞书机器人Webhook地址配置')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield TextField::new('name', '名称')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('Webhook地址的名称，便于识别')
        ;

        yield UrlField::new('url', 'Webhook地址')
            ->setRequired(true)
            ->setHelp('飞书机器人的Webhook推送地址')
        ;

        yield TextField::new('remark', '备注')
            ->setMaxLength(255)
            ->setHelp('可选的备注信息')
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用该Webhook地址')
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
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('url', 'Webhook地址'))
            ->add(BooleanFilter::new('valid', '有效状态'))
        ;
    }
}
