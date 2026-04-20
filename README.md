# Zenith Project

광고 운영 실무에서 자주 필요한 기능을 한 화면으로 통합한 관리 시스템입니다.  
Facebook, Google, Kakao 광고 연동, 고객 데이터 관리, 자동화 조건 실행, 이벤트 운영 업무를 한 번에 처리하도록 구성했습니다.

> 포트폴리오 공개 버전 기준으로 보안상 민감한 코드/SQL 일부는 제외되어 있습니다.

## 프로젝트 한눈에 보기

| 항목 | 내용 |
| --- | --- |
| 프로젝트 성격 | 광고 운영/고객 데이터 통합 관리 웹 서비스 |
| 핵심 목표 | 광고 효율 최적화, 운영 자동화, 데이터 기반 의사결정 |
| 개발 기간 | 1년 |
| 개발 인원 | 2명 |
| 백엔드 | PHP, CodeIgniter4 |
| 데이터베이스 | MySQL |

## 주요 기능

1. 광고 플랫폼 통합 관리
- Facebook, Google, Kakao 광고 상태 확인
- 예산/성과 기준으로 운영 항목 점검

2. 고객 DB 관리
- 고객 데이터 수집/저장/활용 기반 마련
- 운영 의사결정을 위한 데이터 조회 구조

3. 자동 광고 조정
- 예산, CPC, CPA, 스케줄 조건 기반 자동화
- 반복 작업 축소 및 반응 속도 향상

4. 운영 업무 기능
- 이벤트/광고주/회원/블랙리스트 관리
- 엑셀 업로드 처리
- JIRA, Slack API 연동 알림

## 화면 예시

기존 문서의 스크린샷은 그대로 유지했습니다.

![1111 (1)](https://github.com/user-attachments/assets/8036d236-f2e3-4f7d-8153-5a18ab03fee3)
![local vrzenith](https://github.com/user-attachments/assets/8655dce2-334a-4258-b783-edaceccfdfff)
![1](https://github.com/user-attachments/assets/4fc2d973-1ecf-4e64-877e-56ed0fd97fee)
![123123](https://github.com/user-attachments/assets/ac62d0c2-a963-4830-aa96-3bc48fedc1ca)

## 기술 스택

<img src="https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=PHP&logoColor=white"/>
<img src="https://img.shields.io/badge/CodeIgniter-EF4223?style=flat-square&logo=Codeigniter&logoColor=white"/>
<img src="https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=MySQL&logoColor=white"/>

## 디렉터리 구조

```text
php_ci4_zenith-my-commits/
├─ README.md
└─ zenith-project/
   ├─ app/
   │  ├─ Controllers/
   │  └─ Views/
   ├─ public/
   │  ├─ static/
   │  └─ health.php
   ├─ composer.json
   └─ spark
```

## 실행 방법

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

## 운영/개발 개선 사항

- Git 규칙 파일 추가: `.editorconfig`, `.gitattributes`
- 협업 템플릿 추가: `.github/pull_request_template.md`, `.github/COMMIT_CONVENTION.md`
- 최소 CI 추가: `.github/workflows/php-lint.yml`
- 컨트롤러 안정성 리팩터링:
  - AJAX/HTTP 메서드 검증
  - 예외 처리 및 실패 응답 일관화
  - 예제 뷰 경로 입력값 검증
- 운영 확인용 헬스체크 추가: `zenith-project/public/health.php`

## 커밋 컨벤션

```text
feat: 기능 추가
refactor: 구조 개선(동작 동일)
infra: 개발환경/CI/저장소 운영 규칙
docs: 문서 변경
```
