# Commit Convention

이 저장소는 커밋 메시지에 아래 타입 접두사를 사용합니다.

- `feat:` 사용자 관점 기능 추가/확장
- `refactor:` 동작 변경 없이 구조 개선
- `infra:` 개발환경, CI, 빌드, 저장소 운영 규칙 변경
- `docs:` 문서 추가/수정
- `fix:` 버그 수정

권장 형식:

```text
<type>: <한 줄 요약>
```

예시:

```text
feat: 운영 점검용 헬스체크 엔드포인트 추가
refactor: 홈 리포트 조회 응답 처리 안정화
infra: Git 속성 및 PR 템플릿 정비
docs: README 구조화 및 실행 가이드 보강
```
