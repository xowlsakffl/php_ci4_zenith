<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row container-fluid agree-container">
    <div class="card col-lg-6 col-12">
    <form action="" method="post">  
        <h1 class="card-title">이용 약관 동의</h1>              
    
        <!-- 모두 동의 버튼 -->
        <button type="buttton">네, 모두 동의합니다.
            <input type="checkbox" name="chk_all" id="chk_all"> 
            <label for="chk_all"><i class="bi bi-check2"></i></label>          
        </button>    
        
        <!-- agree-box -->
        <div class="card-body">  
            <div class="row agree-box">   
                <div class="title d-flex">
                    <input type="checkbox" name="termsService" id="termsService">  
                    <label for="termsService"><i class="bi bi-check2"></i></label>  
                    <h2>이용약관<span>&nbsp;&#40;필수&#41;</span></h2>
                </div>   
                <div class="info">                             
                    <textarea readonly>
체인소우 서비스 이용 약관
본 이용약관의 목차는 아래와 같습니다.

제 1조 목적
제 2조 용어의 정의
제 3조 약관의 게시와 개정
제 4조 약관의 해석
제 5조 "이용계약" 체결
제 6조 서비스의 내용
제 7조 서비스 이용요금
제 8조 "회원" 정보의 변경
제 9조 개인정보보호 의무
제 10조 "회원"의 "아이디" 및 "비밀번호"의 관리에 대한 의무
제 11조 "회사"의 의무
제 12조 "회원"의 의무
제 13조 "서비스"의 제공 등
제 14조 "서비스"의 변경
제 15조 정보의 제공 및 광고의 게재
제 16조 게시물의 삭제
제 17조 게시물에 대한 저작권
제 18조 개인위치정보의 이용 또는 제공
제 19조 개인위치정보주체의 권리
제 20조 법정대리인의 권리
제 21조 8세 이하의 아동 등의 보호의무자의 권리
제 22조 위치정보관리책임자의 지정
제 23조 손해배상
제 24조 이용제한 등
제 25조 책임제한
제 26조 준거법 및 재판관할
--

제1조(목적)
이 약관은 바이브알씨(주식회사 케어랩스 바이브알씨 사업부) (이하 “회사” 및 “바이브알씨”이라 함)이 제공하는 체인소우 서비스(이하 "서비스" 및 "체인소우"이라 함)의 이용과 관련하여 "회사"와 "회원"과의 권리, 의무 및 책임사항, 이용조건 및 절차 등 기본적인 사항을 규정함을 목적으로 합니다.

제2조(용어의 정의)
이 약관에서 사용하는 용어의 정의는 다음과 같습니다.
(1) "회원"이라 함은 "회사"의 "서비스"에 접속하여 이 약관에 따라 "회사"와 "이용계약"을 체결하고 "회사"가 제공하는 "서비스"를 이용하는 고객을 말합니다.
(2) "아이디(ID)"라 함은 "회원"의 식별과 "서비스" 이용을 위하여 "회원"이 정하고 "회사"가 승인하는 문자와 숫자의 조합을 의미합니다.
(3) "비밀번호"라 함은 "회원"이 부여 받은 "아이디"와 일치되는 "회원"임을 확인하고 비밀보호를 위해 "회원" 자신이 정한 문자 또는 숫자의 조합을 의미합니다.
(4) "이용계약"이라 함은 이 약관을 포함하여 서비스 이용과 관련하여 "회사"와 "회원 간에 체결하는 모든 계약을 말합니다.
(5) "해지"라 함은 "회사" 또는 "회원"이 "이용계약"을 해약하는 것을 말합니다.
(6) "이용자"라 함은 "회사"에서 제공하는 "서비스" 또는 관련 제반 서비스를 이용하는 "회원"과 "비회원"을 말합니다.
이 약관에서 사용하는 용어 중 제 2조에서 정하지 아니한 것은 관계 법령 및 서비스별 안내에서 정하는 바에 따르며, 그 외에는 일반 관례에 따릅니다.

제3조(약관의 게시와 개정)
(1) "회사"는 이 약관의 내용을 "회원" 또는 개인위치정보주체가 쉽게 알 수 있도록 서비스 내 환경설정에 게시합니다.
(2) "회사"는 "약관의 규제에 관한 법률", "정보통신망이용촉진 및 정보보호 등에 관한 법률(이하 "정보통신망법")" 등 관련법을 위배하지 않는 범위에서 이 약관을 개정할 수 있습니다.
(3) "회사"가 약관을 개정할 경우에는 적용일자 및 개정사유를 명시하여 현행약관과 함께 제1항의 방식에 따라 그 개정약관의 적용일자 7일 전부터 적용일자 전 일까지 공지합니다. 다만, "회원"에게 불리한 약관의 개정의 경우에는 그 적용일자 30일 전부터 공지하고, 공지 외에 일정기간 서비스 내 로그인 시 동의창 등의 전자적 수단을 통해 따로 명확히 통지하도록 합니다.

(4) "회사"가 전항에 따라 개정약관을 공지 또는 통지하면서 "회원"에게 7일 또는30일 기간 내에 의사표시를 하지 않으면 의사표시가 표명된 것으로 본다는 뜻을 명확하게 공지 또는 통지하였음에도 "회원"이 명시적으로 거부의 의사표시를 하지 아니한 경우 "회원"이 개정약관에 동의한 것으로 봅니다.
(5) "회원"이 개정약관의 적용에 동의하지 않는 경우 "회사"는 개정 약관의 내용을 적용할 수 없으며, 이 경우 "회원"은 "이용계약"을 해지할 수 있습니다. 또한, 기존 약관을 적용할 수 없는 특별한 사정이 있는 경우에 "회사"는 "이용계약"을 해지할 수 있습니다.

제4조(약관의 해석)
(1) "회사"는 개별 서비스에 대해서는 별도의 이용약관 및 정책(이하 서비스 별 이용약관 또는 정책)을 둘 수 있으며, 해당 내용이 이 약관과 상충할 경우에는 "서비스 별 이용약관 또는 정책"을 우선하여 적용합니다.
(2) 이 약관에서 정하지 아니한 사항이나 해석에 대해서는 관계법령 또는 상관례에 따릅니다.

제5조("이용계약" 체결)
(1) "이용계약"은 "회원"이 되고자 하는 자(이하 가입신청자)가 약관의 내용에 대하여 동의를 한 다음 회원가입신청을 하고 "회사"가 이러한 신청에 대하여 승낙함으로써 체결됩니다.
(2) "회사"는 "가입신청자"의 신청에 대하여 "서비스" 이용을 승낙함을 원칙으로 합니다. 다만, "회사"는 다음 각 호에 해당하는 신청에 대하여는 승낙을 하지 않거나 사후에 "이용계약"을 해지할 수 있습니다.
가) 가입신청자가 이 약관에 의하여 이전에 회원자격을 상실한 적이 있는 경우, 단 "회사"의 회원 재가입 승낙을 얻는 경우에는 예외로 함.
나) 타인의 명의를 이용한 경우
다) 허위의 정보를 기재하거나, "회사"가 제시하는 내용을 기재하지 않은 경우
라) "회원"의 귀책사유로 인하여 승인이 불가능하거나 기타 규정한 제반 사항을 위반하며 신청하는 경우
마) 사회의 안녕질서 또는 미풍양속을 저해하거나, 저해할 목적으로 신청한 경우
(3) "회사"는 서비스 관련 설비의 여유가 없거나, 기술상 또는 업무상 문제가 있는 경우에는 승낙을 유보할 수 있습니다.
(4) 제 2항과 제 3항에 따라 회원가입신청의 승낙을 하지 아니하거나 유보한 경우, "회사"는 원칙적으로 이를 가입신청자에게 알리도록 합니다.
(5) "이용계약"의 성립 시기는 "회사"가 가입완료를 신청절차 상에서 표시한 시점으로 합니다.
(6) "회사"는 "가입신청자"가 관계 법령에서 규정하는 미성년자일 경우에 서비스별 안내에서 정하는 바에 따라 승낙을 보류할 수 있습니다.
(7) "회사"는 "회원"에 대해 "회사" 정책에 따라 등급별로 구분하여 이용시간, 이용횟수, 서비스 메뉴 등을 세분하여 이용에 차등을 둘 수 있습니다.
(8) "회사"는 "회원"에 대하여 "청소년보호법" 등에 따른 등급 및 연령 준수를 위해 이용제한이나 등급별 제한을 할 수 있습니다.

제6조(서비스의 내용)
회사가 제공하는 서비스는 아래와 같습니다.
(1) 서비스명: 체인소우
(2) 서비스 내용:  이벤트 신청자 조회 및 안심채팅 기능 제공

제7조(서비스 이용요금)
(1) "회사"가 제공하는 서비스는 회원제 무료입니다.
(2) “회원”의 개인정보도용 등 법적분쟁으로 인한 정보 요구는 법률이 정한 경우 외에는 거절될 수 있습니다.
(3) 무선 서비스 이용 시 발생하는 데이터 통신료는 별도이며 가입한 각 이동통신사의 정책에 따릅니다.

제8조("회원" 정보의 변경)
(1) "회원"은 개인정보관리화면을 통하여 언제든지 본인의 개인정보를 열람하고 수정할 수 있습니다. 다만, 서비스 관리를 위해 필요한 이메일 등은 수정이 불가능합니다.
(2) "회원"은 회원가입신청 시 기재한 사항이 변경되었을 경우 온라인으로 수정을 하거나 전자우편 등 기타 방법으로 "회사"에 대하여 그 변경사항을 알려야 합니다.
(3) 제2항의 변경사항을 "회사"에 알리지 않아 발생한 불이익에 대하여 "회사"는 책임지지 않습니다.

제9조(개인정보보호 의무)
(1) "회사"는 "정보통신망법" 등 관계 법령이 정하는 바에 따라 "회원"의 개인정보를 보호하기 위해 노력합니다. 개인정보의 보호 및 사용에 대해서는 관련법 및 "회사"의 개인정보처리방침이 적용됩니다. 다만, "회사"의 공식 사이트 이외의 링크된 사이트에서는 "회사"의 개인정보 처리방침이 적용되지 않습니다.

제10조("회원"의 "아이디" 및 "비밀번호"의 관리에 대한 의무)
(1) "회원"의 "아이디"와 "비밀번호"에 관한 관리책임은 "회원"에게 있으며, 이를 제3자가 이용하도록 하여서는 안 됩니다.
(2) "회원"은 "아이디" 및 "비밀번호"가 도용되거나 제3자가 사용하고 있음을 인지한 경우에는 이를 즉시 "회사"에 통지하고 "회사"의 안내에 따라야 합니다.
(3) 제2항의 경우에 해당 "회원"이 "회사"에 그 사실을 통지하지 않거나, 통지한 경우에도 "회사"의 안내에 따르지 않아 발생한 불이익에 대하여 "회사"는 책임지지 않습니다.
(4) "회원"의 "아이디"는 원칙적으로 변경이 불가하며 부득이한 사유로 인하여 변경하고자 하는 경우에는 해당 "아이디"를 해지하고 재가입해야 합니다.
(5) "서비스"의 회원 아이디는 "회원" 본인의 동의 하에 "회사" 또는 자회사가 운영하는 사이트의 회원 아이디와 연결될 수 있습니다.
(6) "회원"의 "아이디"는 다음 각 호에 해당하는 경우에는 "회원"의 요청 또는 "회사"의 직권으로 변경 또는 이용을 정지할 수 있습니다.
가) "회사"는 "회원"의 "아이디"가 전화번호 등으로 등록되어 개인정보 유출이 우려되는 경우
나) 타인에게 혐오감을 주거나 반사회적 미풍양속에 어긋나는 경우
다) "회사" 및 "회사"의 운영자로 오인할 우려가 있는 경우
라) 기타 합리적인 사유가 있는 경우

제11조("회사"의 의무)
(1) "회사"는 "회원"이 희망한 "서비스" 제공 개시일에 특별한 사정이 없는 한 "서비스"를 이용할 수 있도록 하여야 합니다.
(2) "회사"는 "회원"이 안전하게 "서비스"를 이용할 수 있도록 개인정보 보호를 위해 보안시스템을 갖추어야 하며 개인정보처리방침을 공시하고 준수합니다.
(3) "회사"는 서비스 이용과 관련하여 발생하는 "이용자"의 불만 또는 피해구제요청을 적절하게 처리할 수 있도록 필요한 인력 및 시스템을 구비합니다.
(4) "회사"는 서비스 이용과 관련하여 "회원"으로부터 제기되는 의견이나 불만이 정당하다고 객관적으로 인정될 경우에는 적절한 절차를 거쳐 이를 처리하여야 합니다. "회원"이 제기한 의견이나 불만사항에 대해서는 게시판을 활용하거나 전자우편 등을 통하여 "회원"에게 처리과정 및 결과를 전달합니다.

제12조("회원"의 의무)
(1) "회원"은 회원가입 신청 또는 회원정보 변경 시 모든 사항을 사실에 근거하여 본인의 진정한 정보로 작성하여야 하며, 허위 또는 타인의 정보를 등록할 경우 이와 관련된 모든 권리를 주장할 수 없습니다.
(2) "회원"은 약관에서 규정하는 사항과 기타 "회사"가 정한 "운영정책" 등의 제반 규정, 공지사항 등 "회사"가 공지하는 사항 및 관계 법령을 준수하여야 하며, 기타 "회사"의 업무에 방해가 되는 행위, "회사"의 명예를 손상시키는 행위, 타인에게 피해를 주는 행위를 해서는 안 됩니다.
(3) "회원"은 "회사"의 사전 승낙 없이 서비스를 이용하여 영업활동을 할 수 없으며, 그 영업활동의 결과에 대해 "회사"는 책임을 지지 않습니다. 또한 "회원"은 이와 같은 영업활동으로 "회사"가 손해를 입은 경우, "회원"은 "회사"에 대해 손해배상의무를 지며, "회사"는 해당 "회원"에 대해 서비스 이용제한 및 적법한 절차를 거쳐 손해배상 등을 청구할 수 있습니다.
(4) "회원"은 "회사"의 명시적 동의가 없는 한 서비스의 이용권한, 기타 이용계약상의 지위를 타인에게 양도, 증여할 수 없으며 이를 담보로 제공할 수 없습니다.
(5) "회원"은 "회사" 및 기타 제3자의 지적 재산권을 포함한 제반 권리를 침해하는 행위를 해서는 안 됩니다.


제13조("서비스"의 제공 등)
(1) "회사"는 "회원"에게 제공하는 일체의 서비스 등 "회원"이 인터넷과 웹사이트를 이용할 수 있는 다양한 서비스를 제공합니다.
(2) 서비스 이용은 "회사"의 업무상 또는 기술상 특별한 지장이 없는 한 연중무휴, 1일 24시간 운영을 원칙으로 합니다. 단, "회사"는 시스템 정기점검, 증설 및 교체를 위해 "회사"가 정한 날이나 시간에 "서비스"를 일시 중단할 수 있으며, 예정되어 있는 작업으로 인한 서비스 일시 중단은 "서비스" 내 공지사항 등을 통해 사전에 공지합니다.
(3) "회사"는 컴퓨터 등 정보통신설비의 보수점검, 교체 및 고장, 통신두절 또는 운영상 상당한 이유가 있는 경우 사전 예고 없이 일시적으로 "서비스"의 전부 또는 일부를 중단할 수 있습니다. 이 경우 "회사"는 "회원"이 "서비스"에 등록한 전자우편 주소 또는 "회사"의 게시판, "서비스"의 공지사항에 게시하여 "회원"에게 통지합니다. 다만, "회사"가 사전에 통지할 수 없는 부득이한 사유가 있는 경우 사후에 통지할 수 있습니다.

제14조("서비스"의 변경)
(1) "회사"는 상당한 이유가 있는 경우에 운영상, 기술상의 필요에 따라 제공하고 있는 전부 또는 일부 "서비스"를 변경할 수 있습니다.
(2) "서비스"의 내용, 이용방법, 이용시간에 대하여 변경이 있는 경우에는 변경사유, 변경될 서비스의 내용 및 제공일자 등은 그 변경 전에 해당 서비스 초기화면에 게시하여야 합니다.
(3) "회사"는 무료로 제공되는 서비스의 일부 또는 전부를 "회사"의 정책 및 운영의 필요상 수정, 중단, 변경할 수 있으며, 이에 대하여 관련법에 특별한 규정이 없는 한 "회원"에게 별도의 보상을 하지 않습니다.

제15조(정보의 제공 및 광고의 게재)
(1) "회사"는 "서비스"를 운영함에 있어 각종 정보를 서비스화면에 게재하거나 문자메시지, 전자우편, 어플리케이션 Push 알림 등의 방법으로 "이용자"에게 제공할 수 있습니다.
(2) "회사"는 서비스의 운영과 관련하여 홈페이지, 서비스화면, 문자메시지, 전자우편, 어플리케이션 Push 알림 등에 광고 등을 게재할 수 있습니다. 다만, "회사"가 광고 등을 문자메시지, 전자우편, 어플리케이션 Push 알림 등을 이용하여 발송하는 경우 수신 동의 여부를 확인한 후 수신 동의한 "이용자"에 한하여 이를 발송합니다.
(3) "이용자"는 "회사"가 제공하는 서비스와 관련하여 게시물 또는 기타 정보를 변경, 수정, 제한하는 등의 조치를 취하지 않습니다.
(4) "이용자"가 서비스상에 게재되어 있는 광고를 이용하거나 서비스를 통한 광고주의 판촉활동에 참여하는 등의 방법으로 교신 또는 거래를 하는 것은 전적으로 "이용자"와 광고주 간의 문제입니다. 만약 "이용자"와 광고주간에 문제가 발생할 경우에도 "이용자"와 광고주가 직접 해결하여야 하며, 이와 관련하여 “회사”가 광고주의 위법행위에 적극 가담하였다거나, 위법행위를 고의 또는 중과실로 방치하였다는 등의 사정이 없는 한 "회사"는 어떠한 책임도 부담하지 않습니다.


제16조(게시물의 삭제)
(1) "회사"는 "회원"이 게시하거나 전달하는 서비스 내의 모든 "게시물"(텍스트, 이미지, 동영상, 각종 파일, 링크, 댓글 등을 모두 포함)이 다음 각 호의 어느 하나에 해당한다고 판단되는 경우 사전 통지 없이 삭제할 수 있으며, 이에 대해 "회사"는 어떠한 책임도 부담하지 않습니다.

가) 사회 질서 및 기타 선량한 풍속 위반 게시물
ㄱ) 국가 이념을 훼손하거나 자유민주적 기본질서를 현저히 위태롭게 할 우려가 있는 정보를 게시한 경우
ㄴ) 성별, 종교, 장애, 연령, 사회적 신분, 인종, 지역, 직업 등을 차별하거나 이에 대한 편견을 조정하는 내용을 작성하는 경우
ㄷ) 장애인, 노인, 임산부, 아동 등 사회적인 약자 또는 부모, 스승에 대한 살상, 폭행, 협박, 학대행위 등을 구체적으로 묘사하는 경우
ㄹ) 무기 또는 흉기 등을 사용하여 과도하게 신체 또는 시체를 손상하는 등 생명을 경시하는 잔혹한 내용을 작성한 경우
ㅁ) 자살을 목적으로 하거나 이를 미화, 방조 또는 권유아혀 자살 충동을 일으킬 우려가 있는 정보를 작성한 경우
ㅂ) 타인에게 불쾌감을 유발할 수 있는 거친 언어 또는 욕설과 저속한 비어, 은어, 속어를 사용한 경우
ㅅ) 근거 없는 비방을 하거나 허위사실을 유포 또는 선동하는 글을 게시한 경우
ㅇ) 도박 등 사행심을 조장하는 내용을 작성한 경우

나) 음란성 게시물
ㄱ) 외설적인 내용으로 성적 수치심과 불쾌감을 유발하고 일반인의 성 관념에 위배되는 경우
ㄴ) 과도한 신체노출, 남녀의 성기를 저속하게 표현한 내용이나 음란한 성행위 등을 묘사한 경우
ㄷ) 일반인이 보기에 혐오감을 유발하는 사진, 그래픽, 비디오 및 내용을 작성한 경우(인간/동물의 사체 또는 훼손된 모습, 방뇨/배설/살인/자살의 장면 등)
ㄹ) 음란성 소프트웨어, 음악, 사진, 그래픽, 비디오 등을 광고하는 내용을 작성하거나 음란한 게시물을 게재하고 있는 타 사이트의 링크를 제공하는 경우
ㅁ) 윤락행위를 알선하거나 성행위를 목적으로 만남을 유도하는 경우
ㅂ) 아동 또는 청소년을 성적 유희의 대상으로 직접적이고 구체적으로 묘사한 경우

다) 개인정보 침해 및 명예훼손 게시물
ㄱ) 개인정보 유포 등 사생활의 비밀과 자유를 침해할 우려가 현저한 내용을 작성한 경우
ㄴ) 정당한 권한 없이 타인의 사진, 영상 등을 게재하여 타인의 인격권을 현저히 침해하는 내용을 작성한 경우
ㄷ) 비방할 목적으로 공연히 타인을 모욕하거나 사실 또는 허위의 사실을 적시하여 타인의 명예를 훼손하는 경우

라) 저작권 침해 게시물
ㄱ) 저작권자의 동의 없이 방송, 음원, 영화, 소설, 모바일 게임, 만화 등의 타인의 지적 활동으로 창출된 결과물 등을 복제, 배포, 전송하는 경우
ㄴ) 저작권자의 권리에 속하는 상표권, 의장권 등을 무단으로 침해하는 경우
ㄷ) 저작권이 걸려있는 소프트웨어 등을 동의 없이 무료로 다운로드 할 수 있게 하거나, CD 키 자동생성 프로그램 등을 공유하는 경우
ㄹ) 정당한 권한 없이 타인의 상표 또는 저작물 등을 사용, 실시 또는 매개하는 등 특허권, 상표권, 디자인권, 저작권 등 지적재산권을 침해하는 경우

마) 기타 제재 게시물
ㄱ) 동일한 내용을 여러개 나열, 혹은 각 게시판 마다 같은 내용을 반복적으로 등록하는 경우
ㄴ) 관리자 사칭 등 커뮤니티 서비스를 본 목적에 어긋나게 사용하는 경우
ㄷ) 여러 차례에 걸쳐 제한사유에 해당하지 않는 내용에 대해 신고한 것이 악의적이라고 판단되는 경우 게시물 제재 기준 및 이용자 제재 기준/종류는 "서비스" 내 "운영정책"을 따릅니다.
(2) "회원"이 "서비스"에 등록하는 "게시물" 등으로 인하여 본인 또는 타인에게 손해나 기타 문제가 발생하는 경우 "회원"은 이에 대한 책임을 지게 되며, "회사"는 특별한 사정이 없는 한 이에 대하여 책임을 지지 않습니다.
(3) "회사"는 "게시물"에 대하여 제3자로부터 명예훼손, 지적재산권 등의 권리침해를 이유로 게시중단 요청을 받은 경우 이를 임시로 게시중단(전송중단)할 수 있으며, 게시중단 요청자와 게시물 등록자 간에 소송, 합의 기타 이에 준하는 관련기관의 결정 등이 이루어져 "회사"에 접수된 경우 이에 따릅니다.

제17조(게시물에 대한 저작권)
(1) "회원"이 "서비스" 내에 게시한 게시물은 저작권법에 의해 보호를 받습니다. 다만 "회사"가 작성한 게시물에 대한 저작권 등 제 권리는 "회사"에 귀속됩니다.
(2) "회원"은 자신의 게시물을 "회사"가 국내외에서 다음 각 호의 어느 하나의 목적으로 사용하는 것을 허락합니다.
가) "회사"의 "서비스" 또는 "회사"의 공식 사이트 이외의 "회사"의 업무와 연계된 사이트 내에서 "회원"의 "게시물"의 복제, 전송, 전시 및 우수 게시물을 "회사"의 "서비스" 또는 "회사"의 공식 사이트 이외의 "회사"의 업무와 연계된 사이트 화면 등에 노출하기 위하여 "회원" 게시물의 크기를 변환하거나 이미지 변경, 단순화하는 등의 방식으로 수정하는 것
나) "회사"의 "서비스"를 홍보하기 위한 목적으로 미디어, 언론기관 등에게 "회원"의 "게시물" 내용을 보도, 방영하게 하는 것. 단, 이 경우 "회사"는 "회원"의 개별 동의 없이 미디어, 언론기관 등에게 개인정보를 제공하지 않습니다.
(3) 전항의 규정에도 불구하고 "회사"가 "회원"의 게시물을 전항 각 호의 어느 하나에 기재된 목적 이외의 방법으로 사용하는 경우에는 사전에 해당 "회원"으로부터 동의를 얻어야 합니다. 게시물에 대한 "회사"의 사용 요청, "회원"의 동의 및 동의철회는 전화, 전자우편 등 "회사"가 요청하는 방식에 따릅니다. 이 경우 "회사"는 "게시물"의 출처를 표시합니다.
(4) "회원"이 "서비스"의 "이용계약"을 "해지" 하더라도 "회원"의 명의로 "서비스"에 등록한 게시물을 삭제되지 않습니다. "회원"은 자신의 게시물이 회원 탈퇴 후 "서비스"에서 이용되는 것을 원하지 않는 경우 회원 탈퇴 전에 직접 "게시물"을 삭제하여야 합니다.(단, 제3자가 다시 게시하거나 저장…
                    </textarea>
                </div>            
            </div>

            <div class="row agree-box"> 
                <div class="title d-flex">
                    <input type="checkbox" name="termsPrivacy" id="termsPrivacy">  
                    <label for="termsPrivacy"><i class="bi bi-check2"></i></label>  
                    <h2>개인정보 처리방침<span>&nbsp;&#40;필수&#41;</span></h2>
                </div>   
                <div class="info">
                <textarea name="name" cols="3" rows="3" readonly="">개인정보처리(취급)방침
바이브알씨(주식회사 케어랩스 바이브알씨 사업부) (이하 “회사” 및 “바이브알씨”이라 함)은 이용자의 개인정보를 매우 중요하게 생각하며, 정보통신 서비스 제공자가 준수하여야 하는 관련 법령 및 규정을 준수하고 있습니다. 본 개인정보처리방침은 회사의 웹사이트 및 모바일 웹 등에 적용되며, 다음과 같은 내용을 담고 있습니다.

(1) 바이브알씨가 수집하고 있는 회원의 개인정보
(2) 개인정보의 수집 및 이용 목적
(3) 개인정보의 제 3자 제공
(4) 개인정보의 처리 위탁
(5) 개인정보 보유 및 이용 기간
(6) 개인정보 파기절차 및 방법
(7) 이용자 및 법정 대리인의 권리
(8) 쿠키의 운영 및 수집 거부 방법
(9) 개인정보 보호를 위한 기술적/권리적 보호 대책
(10) 개인정보 보호책임자 및 담당부서
(11) 링크 사이트에 대한 책임
(12) 개인정보처리방침의 고지 의무
--

(1) 바이브알씨가 수집하고 있는 회원의 개인정보
가. 수집하는 개인정보의 항목 필수항목: ID(이메일 또는 SNS 계정), 비밀번호, 성명, 휴대전화 번호, 성별, 나이 등의 정보는 회원가입 시 바이브알씨의 개인정보 수집이용취급 방침에 동의하는 경우에 수집됩니다. 바이브알씨에서는 또한 아래의 항목들에 대해서도 안정된 서비스 제공을 위해 합법적인 절차로 수집할 수 있습니다.
ㄱ) IP Address, 쿠키, 방문 일시, 서비스 이용 기록, 불량 이용 기록, 광고식별자
ㄴ) 사용 이동통신사
나. 개인정보 수집방법 회사는 다음과 같은 방법으로 개인정보를 수집하고 있습니다.
- 모바일 앱, 홈페이지 내 이벤트 신청 페이지
다. 개인정보 보관방법 회사는 아래의 서버 호스팅 서비스를 이용해, “1조 가”항에 해당하는 개인정보를 보관하고 있습니다.
- 회원의 개인정보는 하나로 호스팅 서버에 저장됩니다

(2) 개인정보의 수집 및 이용 목적 회사는 아래와 같은 목적으로 서비스 제공을 위한 최소한의 개인정보만을 수집하며, 수집한 정보를 목적 외로 사용하거나, 이용자의 동의 없이 외부에 공개하지 않습니다.
가) 회원관리: 서비스 제공에 따른 개인식별, 가입의사 확인, 이용약관 등 위반 회원에 대한 이용제한 조치, 가입 및 가입횟수 제한, 서비스 부정 이용 제재, 비인가 사용 방지, 법정대리인 동의 없는 만 14세 미만 아동의 가입 방지(추후 법정 대리인 본인 확인), 고충 처리 및 분쟁 조정을 위한 기록 보존, 고지사항 전달, 회원탈퇴 의사의 확인
나) 신규 서비스 개발 및 마케팅 광고에의 활용: 신규 서비스 개발 및 맞춤 서비스 제공, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재, 서비스의 유효성 확인, 이벤트/광고성 정보 및 참여 기회 제공, 접속빈도 파악, 회원의 서비스 이용에 대한 통계

(3) 개인정보의 제 3자 제공
회사는 회원의 개인정보를 동의 없이 제3자에게 제공하지 않습니다. 다만, 법령의 규정에 의한 경우는 예외로 합니다.

(4) 개인정보의 처리 위탁
회사는 향상된 서비스 제공을 위하여 전문업체에 일부 서비스를 위탁 처리하고 있습니다. 회사는 위탁 시 관련법령에 따라 개인정보가 안전하게 관리될 수 있도록 필요한 조치를 취하고 있으며, 위탁 처리기관의 해당 업무에 필요한 최소한의 개인정보만 전달됩니다.

(5) 개인정보 보유 및 이용 기간
회사는 원칙적으로 이용자의 개인정보 수집/이용 목적이 달성되면 지체 없이 파기합니다. 단, 다음의 경우는 예외로 합니다.

보존근거: 통신비밀보호법
보존기록 / 보유기간: 방문기록 / 3개월

보존근거: 회사 내부 방침(부정가입 및 이용 방지, 사법기관 수사의뢰 시 증거자료)
보존기록 / 보유기간: 개인정보 및 부정이용기록(불량 혹은 비정상 이용 기록): 이름, 휴대전화번호, 이메일주소, IP, 로그기록 / 6개월

보존근거: 회사 내부 방침(이벤트 신청 이용에 따른 소비자의 불만 또는 분쟁처리에 관한 기록 보존)
보존기록 / 보유기간: 이벤트 신청 등 서비스 이용 기록(이름, 휴대전화번호, 성별, 생년, 문의사항) / 3년

보존근거: 정보통신망법
보존기록 / 보유기간: 1년 간 서비스를 이용하지 않은 회의원 개인정보 / 1년

(6) 개인정보 파기절차 및 방법
회사는 원칙적으로 개인정보 처리목적이 달성된 경우 지체없이 해당 개인정보를 파기하며, 파기절차 및 파기방법은 다음과 같습니다.
가. 파기절차 이용자가 회원가입 등을 위해 입력한 정보는 수집 목적이 달성된 후 별도의 DB에 옮겨져(종이의 경우 별도의 서류) 내부 방침 및 기타 관련 법령에 따라 일정기간 저장된 후 혹은 즉시 파기됩니다. 이 때, 별도의 DB로 옮겨진 개인정보는 법률에 의한 경우 외에는 다른 목적으로 이용되지 않습니다.
나. 파기방법 전자적 파일 형태의 정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제하며, 종이에 출력된 정보는 분쇄기로 분쇄하거나 소각하여 파기합니다.

(7) 이용자 및 법정대리인의 권리
이용자 또는 법정대리인은 회사에 대해 언제든지 자신 혹은 만 14세 미만 아동의 개인정보보호 관련 권리를 행사할 수 있습니다. 이용자 또는 법정대리인은 회사의 개인정보 처리에 동의하지 않는 경우 동의 철회 혹은 회원 탈퇴를 요청할 수 있습니다. 단, 이경우 서비스의 일부 또는 전부의 이용이 어려울 수 있습니다.
가. 개인정보 조회, 수정을 위해서는 서비스 내 "기본정보 수정"을, 회원탈퇴를 위해서는 회사의 전자우편 또는 전화를 통해 탈퇴 신청을 한 후 본인 확인 절차를 걸치고 직접 열람, 정정 또는 탈퇴가 가능합니다.
나. 개인정보 보호책임자에게 서면, 전화 또는 이메일로 연락한 경우에도 지체 없이 조치합니다.
다. 이용자가 개인정보의 오류에 대한 정정을 요청하는 경우에는 정정을 완료하기 전까지 당해 개인정보를 이용 또는 제공하지 않습니다. 또한 잘못된 개인정보를 제 3자에게 이미 제공한 경우에는 정정 처리결과를 제 3자에게 지체 없이 통지하여 정정이 이루어지도록 하겠습니다.
라. 회사는 이용자 또는 법정 대리인의 요청에 의해 해지 또는 삭제된 개인정보를 "(5) 개인정보의 보유 및 이용기간"에 명시된 바에 따라 처리하고 그 외의 용도로 열람 또는 이용할 수 없도록 처리하고 있습니다.

(8) 쿠키의 운영 및 수집 거부 방법
가. 회사는 이용자에게 맞춤형 서비스를 제공하고, 보다 신속한 서비스 제공을 위해 이용자에 대한 정보를 저장하고 수시로 불러오는 ‘쿠키(Cookie)’를 사용합니다. 쿠키란 웹사이트 방문 시, 서버가 이용자의 하드디스크에 저장하는 작은 기록 정보 파일을 말합니다. 이후 이용자가 웹사이트에 방문할 경우 웹사이트 서버는 이용자의 하드디스크에 저장되어 있는 쿠키의 내용을 읽어 이용자의 환경설정을 유지하고 맞춤화된 서비스를 제공합니다. 쿠키는 개인을 식별하는 정보를 자동적•능동적으로 수집하지 않으며, 이용자는 언제든지 이러한 쿠키의 저장을 거부하거나 삭제할 수 있습니다. 단, 쿠키 저장을 거부하는 경우에는 일부 서비스 이용이 어려울 수 있습니다.
나. 쿠키의 설치 / 운용 및 거부 방법 안내 이용자는 사용하는 웹 브라우저의 옵션을 설정함으로써 모든 쿠키를 허용 혹은 거부하거나, 쿠키가 저장될 때마다 확인을 거치도록 지정할 수 있습니다. - Internet Explorer 의 경우 [도구] 메뉴에서 [인터넷 옵션]을 선택합니다. [개인정보 탭]에서 설정합니다. [개인정보취급 수준]을 설정합니다. - Chrome의 경우 [설정] 하단 [고급설정 표시]를 클릭합니다. [개인정보] 컨텐츠 설정 -&gt; [쿠키] 영역에서 원하시는 정책을 선택합니다.

(9) 개인정보 보호를 위한 기술적/권리적 보호 대책
“회사”는 이용자들의 개인정보 보호를 위해 다음과 같은 기술적∙관리적 노력을 하고 있습니다.
가. 개인정보 암호화 이용자의 비밀번호 등 중요정보는 암호화되어 저장, 관리되고 있으며, 개인정보의 확인 및 변경은 본인에 의해서만 가능합니다.
나. 해킹 등에 대비한 대책 회사는 해킹이나 악성코드에 의하여 이용자들의 개인정보가 유출• 훼손되는 것을 방지하기 위하여 침입차단 시스템을 24시간 운영하여 외부로부터의 무단접근을 통제하고 있으며, 백신 프로그램을 설치하여 시스템이 악성코드나 바이러스에 감염되지 않도록 노력하고 있습니다. 또한 개인정보의 훼손에 대비하여 자료를 수시로 백업하고 있고, 암호화통신 등을 통하여 네트워크 상에서 개인정보를 안전하게 전송할 수 있도록 하고 있습니다. 더불어 회사는 더욱 안전한 개인정보 처리를 위해 최대한의 기술적 방법을 구비하기 위해 노력하고 있습니다.
다. 개인정보 처리 직원의 최소화 및 교육 회사는 개인정보를 처리하는 직원을 최소한으로 제한하고 있으며, 관련 직원들에 대한 수시 교육을 실시하여 개인정보처리방침의 중요성을 인지시키고 있습니다.

(10) 개인정보 보호책임자 및 담당부서
이용자는 회사의 서비스를 이용하는 중 발생하는 모든 개인정보 관련 문의, 불만처리 등에 관한 사항을 개인정보 보호책임자 혹은 담당부서로 문의할 수 있습니다. 회사는 이용자의 문의에 대한 신속하고 성실한 답변 및 처리를 위해 노력하고 있습니다.

[개인정보 보호 책임자]
- 개인정보 보호책임자: 김창기
- 담당부서/직위: 바이브알씨 본부/본부장
- 전화번호: 02-6247-1001
- 이메일 주소: kimcg@carelabs.co.kr

기타 개인정보침해에 대한 신고나 상담이 필요하신 경우에는 아래 기관으로 문의하시기 바랍니다.
- 개인정보침해신고센터, 개인정보 분쟁조정위원회(privacy.kisa.or.kr / 국번없이 118)
- 대검찰청 사이버수사과(www.spo.go.kr / 02-3480-3571)
- 경찰청 사이버테러대응센터(www.ctrc.go.kr / 1566-0112)

(11) 링크 사이트에 대한 책임
회사는 이용자에게 다른 웹사이트에 대한 링크를 제공할 수 있습니다. 그러나 링크 웹사이트들은 회사의 개인정보처리방침이 적용되지 않을 수 있으므로 해당 링크를 통해 외부 웹사이트로 이동하는 경우, 해당 서비스의 정책을 검토하시기 바랍니다.

(12) 개인정보처리방침의 고지 의무
회사는 개인정보처리방침에 대한 변경이 있을 경우에는 개정 개인정보처리방침의 시행일로부터 최소 7일 전에 홈페이지/모바일 앱의 공지사항 또는 이메일을 통해 고지합니다. 또한, 필요 시 이용자 동의를 다시 받을 수도 있습니다.

부 칙
가) 본 개인정보취급방침은 2018년 1월 1일부터 적용됩니다.
나) 시행되던 종전의 개인정보취급방침은 본 방침으로 대체됩니다

- 개인정보 처리방침 제정일자: 2013. 5. 24
- 개인정보 처리방침 최종 변경 개정일자: 2018. 1. 1</textarea>
                </div>                
            </div>
        </div>  

        <!--가입 확인버튼  -->
        <div class="row">
            <a href="#" id="btnAgree" role="button" class="confirm_agree">확인</a>
        </div>     
    </form>       
    </div>
</div>
<?= $this->endSection() ?>