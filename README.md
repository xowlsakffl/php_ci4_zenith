# 제니스 프로젝트

제니스 웹솔루션에서 광고 운영과 고객 데이터 관리를 통합 처리하기 위한 CodeIgniter4 기반 PHP 운영 소스입니다. 광고 플랫폼 상태 조회, 고객 데이터 처리, 이벤트 운영, 자동화 조건 실행 등 실무 운영 흐름을 한 시스템에서 처리합니다.

관리자 시스템이 이벤트, 광고주, 매체, 계정, 자동화 조건을 관리하고 이 운영 소스는 실제 사용자 요청을 받아 화면 렌더링과 데이터 처리 로직을 수행하는 구조입니다. 다양한 외부 API 연동과 운영성 기능을 통해 마케팅 캠페인 운영 시 필요한 업무를 빠르게 처리하도록 설계되었습니다.

대부분의 코드는 보안상의 이유로 공개하지 않으며 일부 코드파일만 공개합니다.

## 주요 기능

- Facebook, Google, Kakao 광고 상태 조회 및 관리
- 고객 신청/운영 데이터 저장 및 조회
- 조건 기반 자동 광고 조정(예산, CPC, CPA, 일정)
- 이벤트, 광고주, 회원, 블랙리스트 관리
- 엑셀 업로드 기반 대량 데이터 처리
- JIRA, Slack API 연동 알림

## 처리 흐름

1. 운영자가 로그인 후 광고/이벤트/고객 관리 메뉴에 접근합니다.
2. 각 컨트롤러가 요청 파라미터를 검증하고 서비스 데이터를 조회합니다.
3. 외부 광고 API 또는 내부 DB 조회 결과를 화면/응답 데이터로 가공합니다.
4. 자동화 조건 충족 시 광고 상태 변경 또는 후속 처리 로직을 수행합니다.
5. 처리 결과는 운영 화면, 로그, 외부 알림 연동으로 전달됩니다.

## 기술 스택

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![CodeIgniter4](https://img.shields.io/badge/CodeIgniter4-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 화면 예시

![1111 (1)](https://github.com/user-attachments/assets/8036d236-f2e3-4f7d-8153-5a18ab03fee3)
![local vrzenith](https://github.com/user-attachments/assets/8655dce2-334a-4258-b783-edaceccfdfff)
![1](https://github.com/user-attachments/assets/4fc2d973-1ecf-4e64-877e-56ed0fd97fee)
![123123](https://github.com/user-attachments/assets/ac62d0c2-a963-4830-aa96-3bc48fedc1ca)

## 소스 구성

| 파일/디렉터리 | 설명 |
| --- | --- |
| `zenith-project/app/Controllers/` | 광고, 이벤트, 사용자, API 연동 컨트롤러 |
| `zenith-project/app/Views/` | 운영 화면 템플릿 |
| `zenith-project/public/static/` | 프론트 정적 리소스(CSS/JS/폰트/이미지) |
| `zenith-project/public/health.php` | 운영 점검용 헬스체크 응답 |
| `zenith-project/composer.json` | PHP 의존성 및 프로젝트 메타 정보 |
| `zenith-project/spark` | CodeIgniter CLI 실행 엔트리 |
| `.github/workflows/php-lint.yml` | 최소 PHP 문법 점검 CI |

위처럼 일부 소스파일만 공개

## 실행 환경

```bash
cd zenith-project
composer install
php spark serve
```

PowerShell:

```powershell
Set-Location .\zenith-project
composer install
php .\spark serve
```

필수 런타임 구성은 다음과 같습니다.

- PHP 웹 서버 환경
- MySQL 접근 권한
- `curl`, `mbstring`, `json`, `openssl` 확장
- 정적 리소스 경로 `public/static/`

## 개발 정보

- 개발 기간: 1년
- 개발 인원: 2명
