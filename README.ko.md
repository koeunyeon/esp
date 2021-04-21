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

소스코드를 `/src/{리소스}/{액션}.php` 규칙에 맞게 작성하면 `/{리소스}/{액션}` 경로로 접속할 수 있다는 것을 알게 되었습니다.

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

## 블로그 글 보기 만들기
[/article/create](http://localhost:8000/article/create)에 제목과 내용을 쓰고 나면 자동으로 [http://localhost:8000/article/read/1](http://localhost:8000/article/read/1) 으로 이동하는 것을 볼 수 있습니다.  
다만 아직 블로그 글 보기 기능을 만들지 않았기 때문에 **PAGE NOT FOUND** 오류가 보여집니다. 수정해 봅시다.  

`/src/article/read.php` 파일을 만들겠습니다.  
```
<?php    
    $model = ESP::auto_find();
?>
<p>title : <?=$model->title ?></p>
<p>content : <?=$model->content ?></p>

<p><a href="<?= ESP::link_edit() ?>">수정</a></p>
<p><a href="<?= ESP::link_delete() ?>">삭제</a></p>
<p><a href="<?= ESP::link_list() ?>">목록</a></p>
```

데이터베이스에서 데이터를 불러오는 코드는 단 한줄입니다.  
```
$model = ESP::auto_find();
```
ESP의 `auto_find()`는 `auto_save()`와 비슷하게 `{리소스}`에서 `$id`에 해당하는 데이터를 찾습니다.  
`$id` 변수는 어디에 있을까요? 예상할 수 있듯이 `$_GET['id]` 를 읽습니다.   
만약 URL이 `{리소스}/{액션}/{ID}` 형태일 경우 ESP는 자동으로 `$_GET['id']` 대신 URL에서 `$id` 변수를 읽습니다.  
즉 `/article/read?id=3` 대신 `/article/read/3` 형태로도 사용 가능합니다.  

불러온 데이터는 `$model->title` 처럼 객체 형태로 사용합니다.  
예제 코드에서 `$model`은 `EspData` 타입으로 잘못된 키가 있어도 오류를 반환하지 않고 빈 문자열(`""`)을 반환합니다.  
즉 `article` 테이블에 `missing` 컬럼이 없어도 `$model->missing` 은 `""`을 리턴하므로 실제 데이터 유무에 관계없이 생각한 대로 코드를 작성할 수 있습니다.  

# ABOUT
## ESP는 MVC 프레임워크가 아닙니다.
ESP는 MVC 프레임 워크가 아닙니다.  
ESP의 목표는 엉망이 되더라도 더 빨리 개발하는 것입니다.  
ESP는 혼란스럽고 체계적이지 않지만 유용한 기능을 포함합니다.  

물론 MVC 구조가 훌륭하다는 것을 알고 있습니다.  
하지만 때로는 좋은 구조보다 더 빨리 개발하는 것이 더 좋다는 점도 잘 알고 있습니다.  

모든 도구에는 고유한 역할이 있습니다. 아무리 좋은 망치라도 작은 구멍을 만드는 데는 적합하지 않습니다.  

## PHP는 자바가 아닙니다.
PHP 7버전부터 PHP는 자바가 장악하고 있는 엔터프라이즈 영역을 노리는 것처럼 변화하고 있습니다.  
하지만 저는 PHP와 자바는 포지셔닝이 달라야 한다고 생각합니다.  
잔소리쟁이 엄마처럼 모든 것에 규칙을 세우고, 규칙에 어긋나면 따가운 소리를 들어야 하는 자바 세상도 나쁘지는 않습니다.  
그렇지만 때로는 규칙보다는 실리를, 단단함보다는 속도를 중시해야 하는 경우도 있습니다.  
특히 "출시하고 잊어버리는" 웹 에이전시 스타일의 개발에서는 더욱 그렇습니다.  

PHP는 "혼자 작업하기 좋은 언어"이고, "스스로 규칙을 세울 수 있다는 점에서 더욱 좋은 언어"입니다.  
ESP는 PHP의 이런 철학에 따라 "당면한 문제를 해결할 수 있는 툴박스"를 목표로 삼고 있습니다.  

## 제작자
저는 한국인이어서 영어를 완벽하게 구사할 수는 없습니다. 어색한 표현이 있어도 이해해 주세요.  

개발자로 일하는 시간동안 다양한 언어의 많은  프레임워크를 사용해 왔고, 저에게 맞는 프레임워크를 찾으려고 애썼습니다.  
이러한 시도의 결과 중 결과 중 하나는 ESP입니다.  

ESP를 사용해 보시고 버그 혹은 불만 사항이 있으면 알려주세요.  
감사합니다.  


