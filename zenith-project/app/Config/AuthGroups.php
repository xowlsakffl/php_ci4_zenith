<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'guest';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys are
     * the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://github.com/codeigniter4/shield/blob/develop/docs/quickstart.md#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => '최고관리자',
            'description' => '제니스 최고관리자',
        ],
        'admin' => [
            'title'       => '관리자',
            'description' => '제니스 일반관리자',
        ],
        'developer' => [
            'title'       => '제니스 개발자',
            'description' => '제니스 개발자',
        ],
        'user' => [
            'title'       => '제니스 사용자',
            'description' => '제니스 사용자',
            /* [
                'advertiser1' => [
                    'title' => '광고주1',
                    'description' => '테스트 광고주1',
                ],
                'advertiser2' => [
                    'title' => '광고주2',
                    'description' => '테스트 광고주2',
                ]
            ], */
        ],
        'guest' => [
            'title'       => '게스트',
            'description' => '게스트 권한'
        ]
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system. Each system is defined
     * where the key is the
     *
     * If a permission is not listed here it cannot be used.
     */
    //일부 그룹에 특정 관리에 대한 명시적 권한을 부여
    public array $permissions = [
        'admin.access'        => '관리자만 가능한 페이지 접근 가능',
        'admin.settings'      => '관리자만 가능한 설정 접근 가능',
        'users.create'        => '회원 생성',
        'users.edit'          => '회원 수정',
        'users.delete'        => '회원 삭제',
        
        'users.advertiser1'   => '광고주1',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     */
    //그룹과 권한 맵핑
    public array $matrix = [
        'superadmin' => [
            'admin.access',
            'users.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
        ],
        'user' => [],
        'guest' => [
            'guest.access'
        ],
    ];
}
