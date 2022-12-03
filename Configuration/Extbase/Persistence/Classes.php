<?php
declare(strict_types = 1);
use Cylancer\Usertools\Domain\Model\FrontendUser;
use Cylancer\Usertools\Domain\Model\FrontendUserGroup;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users'
    ],
    FrontendUserGroup::class => [
        'tableName' => 'fe_groups'
    ]
];
