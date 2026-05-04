# 제니스 프로젝트

광고 운영, 이벤트 관리, 내부 협업 업무를 하나의 백오피스에서 처리하기 위한 CodeIgniter 4 기반 사내 운영 도구입니다.

이 저장소는 공개 가능한 범위만 정리한 스냅샷입니다. 민감한 SQL, 외부 연동 키, 일부 설정 파일, 모델/라이브러리의 세부 구현은 제외되어 있습니다.

## 프로젝트 소개
### 화면 예시

![1111 (1)](https://github.com/user-attachments/assets/8036d236-f2e3-4f7d-8153-5a18ab03fee3)
![local vrzenith](https://github.com/user-attachments/assets/8655dce2-334a-4258-b783-edaceccfdfff)
![1](https://github.com/user-attachments/assets/4fc2d973-1ecf-4e64-877e-56ed0fd97fee)
![123123](https://github.com/user-attachments/assets/ac62d0c2-a963-4830-aa96-3bc48fedc1ca)

제니스 프로젝트는 광고 운영, 이벤트 관리, 사용자 관리, 내부 협업 업무를 하나의 관리자 시스템에서 처리할 수 있도록 구성한 CodeIgniter 4 기반 백오피스입니다. 여러 광고 매체의 운영 현황 확인, 조건 기반 자동화, 이벤트 실무 처리, 협업 알림 기능을 한곳에 모아 운영 효율을 높이는 데 초점을 두었습니다.

서비스 구조는 광고 매체 관리 기능만 따로 분리하지 않고, 운영자가 실제로 함께 사용하는 업무 흐름을 기준으로 구성했습니다. 광고 리포트 확인, 상태 변경, 자동화 실행 결과 조회, 이벤트 업무, 사용자 인증, 외부 협업 도구 연동이 하나의 서비스 안에서 이어지도록 설계되어 있습니다.

이 저장소는 공개 가능한 범위만 정리한 버전으로, 민감한 연동 설정과 일부 내부 구현은 제외되어 있습니다. 다만 현재 포함된 컨트롤러, 뷰, 정적 자산 구조만으로도 프로젝트의 전반적인 기능 구성과 운영 방향을 확인할 수 있습니다.

## 프로젝트 성격

- 목적: 광고 매체 운영, 이벤트 업무, 내부 관리 기능을 한곳에서 통합 운영
- 성격: 사내 운영용 관리자 시스템
- 기반 프레임워크: PHP + CodeIgniter 4
- 인증 계층: CodeIgniter Shield
- 데이터/외부 연동: MySQL, Slack, Jira, 광고 매체 API

## 공개본 기능 범위

코드 기준으로 현재 확인되는 영역은 아래와 같습니다.

- 광고 운영 관리
  - Facebook, Google, Kakao, Naver, 기타 매체용 컨트롤러 구성
  - 홈 화면에서 매체별 리포트 수집
  - 광고 상태/예산 조정 자동화 로직 존재
- 자동화 운영
  - 조건 기반 실행 스케줄 관리
  - 실행 결과/로그 조회
  - 매체별 상태 변경 및 예산 제어 처리
- 이벤트/운영 업무
  - 광고주 관리
  - 블랙리스트 관리
  - 변경 이력 관리
  - 엑셀 업로드 처리
  - 매체 관리 화면
- 내부 협업 연동
  - Jira 이슈 상태 변화 연동
  - Slack 사용자 알림 발송
- 계정/인증
  - 로그인 및 인증 화면
  - 매직 링크 관련 화면/컨트롤러
  - 비밀번호 변경 및 변경 주기 체크
- 공통 관리자 기능
  - 회계(`Accounting`)
  - 인사(`HumanResource`)
  - 회사 정보(`Company`)
  - 캘린더(`Calendar`)
  - 사용자 관리(`User`)
  - 통합 관리(`Integrate`)
- 헬스체크
  - `zenith-project/public/health.php` 에서 JSON 상태 응답 제공

## 코드베이스 요약

- PHP 컨트롤러: 36개
- View 템플릿: 87개
- 주요 컨트롤러 도메인
  - `Accounting`
  - `Advertisement`
  - `AdvertisementManager`
  - `Api`
  - `Auth`
  - `Calendar`
  - `Company`
  - `EventManage`
  - `HumanResource`
  - `Integrate`
  - `User`


- `HomeController`
  - 로그인 사용자의 비밀번호 변경 시점 확인
  - AJAX 요청에서만 리포트 응답
  - 예외 발생 시 API 에러 응답 처리
- `ExampleController`
  - 뷰 이름 화이트리스트 패턴 검증
  - 존재하지 않는 뷰 접근 차단
- `PasswordChangeController`
  - Shield 비밀번호 정책 사용
  - 비밀번호 변경 후 강제 재설정 해제 처리
- `public/health.php`
  - 애플리케이션 상태를 별도 JSON 엔드포인트로 제공

## 기술 스택

### Backend

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![CodeIgniter 4](https://img.shields.io/badge/CodeIgniter-4-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)
![CodeIgniter Shield](https://img.shields.io/badge/CodeIgniter%20Shield-Auth-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)
![CodeIgniter Settings](https://img.shields.io/badge/CodeIgniter%20Settings-2.1-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)
![CodeIgniter Tasks](https://img.shields.io/badge/CodeIgniter%20Tasks-dev-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-Package%20Manager-885630?style=for-the-badge&logo=composer&logoColor=white)

### Frontend / Static Assets

`public/static/package.json` 기준:

![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Bootstrap Icons](https://img.shields.io/badge/Bootstrap%20Icons-1-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-3-0769AD?style=for-the-badge&logo=jquery&logoColor=white)
![jQuery UI](https://img.shields.io/badge/jQuery%20UI-1.13-0769AD?style=for-the-badge&logo=jquery&logoColor=white)
![DateRangePicker](https://img.shields.io/badge/DateRangePicker-UI-0F172A?style=for-the-badge)
![DataTables](https://img.shields.io/badge/DataTables-1.13%2B-1F6FEB?style=for-the-badge&logo=datatables&logoColor=white)
![JSZip](https://img.shields.io/badge/JSZip-3.10-DC382D?style=for-the-badge)
![pdfmake](https://img.shields.io/badge/pdfmake-0.2-8B5CF6?style=for-the-badge)

정적 에셋은 이미 `public/static` 아래에 포함되어 있어, 단순 확인 목적이면 별도 프런트엔드 빌드 없이도 구조 파악이 가능합니다.

## 저장소 구조

```text
php_ci4_zenith-my-commits/
|-- README.md
`-- zenith-project/
    |-- .github/
    |   |-- COMMIT_CONVENTION.md
    |   |-- pull_request_template.md
    |   `-- workflows/php-lint.yml
    |-- app/
    |   |-- Controllers/
    |   `-- Views/
    |-- public/
    |   |-- health.php
    |   `-- static/
    |-- composer.json
    `-- spark
```

- 공개본에는 전체 서비스 구동에 필요한 일부 내부 설정/구현이 빠져 있습니다.
- 특히 민감한 연동 설정, 일부 모델/라이브러리, 환경 파일은 저장소에 포함되지 않습니다.


## 개발 규칙 및 보조 문서

- 커밋 규칙: `zenith-project/.github/COMMIT_CONVENTION.md`
- PR 템플릿: `zenith-project/.github/pull_request_template.md`
- CI: `zenith-project/.github/workflows/php-lint.yml`

현재 CI는 아래 범위를 점검합니다.

- `app/Controllers` PHP 문법 검사
- `app/Common.php`
- `public/index.php`

## 보안 및 공개 범위

`.gitignore` 기준으로 아래 항목은 버전 관리에서 제외되도록 구성되어 있습니다.

- `.env`
- `vendor/`
- 로그/세션/업로드 파일
- `node_modules/`
- 외부 API 설정 파일
- 광고 연동용 비공개 키/설정 파일

따라서 이 저장소만으로 내부 운영 환경을 그대로 재현하는 것이 목적은 아닙니다. 공개 가능한 구조, 화면, 컨트롤러 설계, 운영 도메인 구성을 보여주는 용도의 저장소입니다.

