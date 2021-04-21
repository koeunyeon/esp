# ESP
ESP는 **Extreme Short PHP**의 약자입니다.
중복을 배제하고 짧은 코드를 지향하는 PHP 프레임워크입니다.
자동으로 처리할 수 있는 부분은 가능한 자동화합니다.

# 시작하기
## 우리가 만들어 볼 것
우리는 아주 간단한 블로그 시스템을 만듭니다.
ESP에 익숙하다면 15분 내로 간단한 CRUD 프로그램을 만들어낼 수 있습니다.

## 데이터베이스 테이블 생성
먼저 데이터베이스 테이블을 만들어 보겠습니다.
ESP는 MySQL에 특화되어 있으므로 MySQL에서 생성합니다.
```
CREATE TABLE `article` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(50) NOT NULL,
	`content` TEXT NOT NULL,
	`insert_date` DATETIME NOT NULL,
	`update_date` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
```
`id`,`insert_date`,`update_date`는 ESP에서 내부적으로 사용하는 필수 열입니다.
ESP에서 자동으로 생성하므로 입력/수정시 신경쓰지 않아도 됩니다.


## 데이터베이스 액세스 정보 제공
다음으로 ESP에 데이터베이스 연결 정보를 알려주겠습니다.

`core / esp.config.php` 파일을 열고 데이터베이스 연결 정보를 입력합니다.
```
<? php
$db_config = [
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'pdb',
    'charset' => 'utf8',
    'username' => 'puser',
    'password' => 'p_pw1234'
];
```

이것이 설정 전부입니다.

## 블로그 글쓰기 화면 만들기
`/src/article/create.php` 파일을 생성하고 아래 코드를 입력합니다.
```
<form method="POST">
    <p>title : <input type="text" name="title" id="title" /></p>
    <p>content : <input type="text" name="content" id="content" /></p>
    <p><input type="submit" value="저장" /></p>
</form>
```
평범한 HTML 코드입니다. PHP는 아직 없어요.

이제 이 파일을 웹 브라우저에서 열어봅시다.
우선 `php -S localhost:8000` 명령으로 로컬 PHP 서버를 실행하겠습니다. 만약 포터블 버전을 쓴다면 `D:\Programs\xampp\php\php.exe -S localhost:8000` 방식으로 사용할 수 있습니다.

브라우저에 http://localhost:8000/src/article/create.php 로 접속하는 대신 [http://localhost:8000/article/create](http://localhost:8000/article/create) 로 접속해 봅니다.
이제 우리는 코드를 `/src/{리소스}/{액션}.php` 규칙에 맞게 작성하면 `/{리소스}/{액션}` 경로로 접속할 수 있다는 것을 알게 되었습니다.

## 블로그 글쓰기 기능 만들기
`/src/article/create.php` 파일을 아래와 같이 수정합시다.
```
<?php
ESP::auto_save();
?>
<form method="POST">
    <p>title : <input type="text" name="title" id="title" /></p>
    <p>content : <input type="text" name="content" id="content" /></p>
    <p><input type="submit" value="저장" /></p>
</form>
```
코드 3줄이 추가되었습니다.
```
<?php
ESP::auto_save();
?>
```
ESP의 `auto_save` 메소드는 http POST 요청에 대해 자동으로 `{리소스}` 에 해당하는 `데이터베이스 테이블`에 데이터 저장을 시도합니다.
저장할 데이터는 `http POST 요청($_POST)` 입니다.
만약 `{액션}`이 `create` 이면 `insert`를, `edit`이면 `update`를 실행합니다.
다시 [http://localhost:8000/article/create](http://localhost:8000/article/create) 에 접속해서 데이터를 입력해보고 데이터베이스에 저장되었는지 확인해 보세요.



# ESP는 MVC 프레임워크가 아닙니다.
ESP는 MVC 프레임 워크가 아닙니다.
ESP의 목표는 엉망이 되더라도 더 빨리 개발하는 것입니다.
ESP는 혼란스럽고 체계적이지 않지만 유용한 기능을 포함합니다.

물론 MVC 구조가 훌륭하다는 것을 알고 있습니다.
하지만 때로는 좋은 구조보다 더 빨리 개발하는 것이 더 좋다는 점도 잘 알고 있습니다.

모든 도구에는 고유한 역할이 있습니다. 아무리 좋은 망치라도 작은 구멍을 만드는 데는 적합하지 않습니다.

# 나에 대해서
저는 한국인이어서 영어를 완벽하게 구사할 수는 없습니다. 어색한 표현이 있어도 이해해 주세요.

개발자로 일하는 시간동안 다양한 언어의 많은  프레임워크를 사용해 왔고, 저에게 맞는 프레임워크를 찾으려고 애썼습니다.
이러한 시도의 결과 중 결과 중 하나는 ESP입니다.

ESP를 사용해 보시고 버그 혹은 불만 사항이 있으면 알려주세요.
감사합니다.


