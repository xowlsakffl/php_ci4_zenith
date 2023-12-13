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
        ],
        'agency' => [
            'title'       => '광고대행사',
            'description' => '광고대행사 사용자',
        ],
        'advertiser' => [
            'title'       => '광고주',
            'description' => '광고주 사용자',
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

        'agency.access'       => '대행사 목록 페이지',
        'agency.advertisers'  => '대행사 하위 광고주 관리',
        'agency.create'       => '대행사 생성',
        'agency.edit'         => '대행사 수정',
        'agency.delete'       => '대행사 삭제',
        
        'advertiser.access'   => '광고주 목록 페이지',
        'advertiser.create'   => '광고주 생성',
        'advertiser.edit'     => '광고주 수정',
        'advertiser.delete'   => '광고주 삭제',

        'integrate.status' => '인정기준 변경 권한'
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
            'agency.*',
            'advertiser.*',
            'integrate.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'agency.*',
            'advertiser.*',
            'integrate.*',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'integrate.*',
        ],
        'agency' => [
            'agency.advertisers',
        ],
        'advertiser' => [],
        'user' => [
            'agency.*',
            'advertiser.*',
        ],
        'guest' => [
            'guest.access'
        ],
    ];
}
