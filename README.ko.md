# ESP
ESP는 **Extreme Short PHP**의 약자입니다.  
중복을 배제하고 짧은 코드를 지향하는 PHP 프레임워크입니다.  
자동으로 처리할 수 있는 부분은 가능한 자동화합니다.  

- 한국어 소개 페이지는 [ESP 한국어 소개](https://github.com/koeunyeon/esp/blob/main/README.ko.md)에서 볼 수 있습니다.
- 영어 소개 페이지는 [ESP 영어 소개](https://github.com/koeunyeon/esp/blob/main/README.md)에 있습니다.

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

`link_edit()`, `link_delete()`, `link_list()` 정적 메소드는 현재 `{리소스}`에 해당하는 수정, 삭제, 목록 링크를 자동으로 만들어주는 헬퍼입니다.

## 블로그 글 수정 만들기
`/src/article/edit.php` 파일을 만듭니다.
```
<?php
ESP::auto_save();
$model = ESP::auto_find();
?>
<form method="POST">
    <p>title : <input type="text" name="title" id="title" value="<?= $model->title ?>" /></p>
    <p>content : <input type="text" name="content" id="content" value="<?= $model->content ?>" /></p>
    <input type="submit" value="저장" />
</form>
```

ESP의 `auto_save()` 메소드는 `{액션}`에 따라 `insert`와 `update`를 구분합니다.  
따라서 `create.php`와 `edit.php`에서 동일한 함수를 호출해도 `create`에서는 `insert`, `edit`에서는 `update` 동작합니다.

`$model = ESP::auto_find()`로 불러온 데이터는 `$model->title` 처럼 객체 형태로 사용합니다.  
예제 코드에서 `$model`은 `EspData` 타입으로 잘못된 키가 있어도 오류를 반환하지 않고 빈 문자열(`""`)을 반환합니다.  
즉 `article` 테이블에 `missing` 컬럼이 없어도 `$model->missing` 은 `""`을 리턴하므로 실제 데이터 유무에 관계없이 생각한 대로 코드를 작성할 수 있습니다.  

## 블로그 글 삭제 만들기
이번에는 삭제입니다. `/src/article/delete.php` 파일을 만듭니다.
```
<?php
ESP::auto_delete();
```
단 한줄입니다. ESP는 자동으로 리소스를 삭제하고 목록 페이지로 이동합니다.

## 블로그 글 목록 보여주기
마지막은 글 목록을 보여줍시다. `/src/article/list.php` 파일을 만듭니다.
```
<?php
$page_list = ESP::auto_pagenate();
?>
<ul>
    <?php
    foreach ($page_list as $row) {
        ESP::part_auto("row", $row->items());
    }
    ?>
</ul>
```

아직 실행하기 전에 파일을 하나 더 만들겠습니다. 경로는 `/part/article/list.row.php` 입니다.
```
<li><a href="<?= ESP::link_read($id) ?>"><?= $title ?></a></li>
```

ESP는 웹 페이지가 여러 개의 조각으로 구성될 수 있다고 가정합니다. 따라서 각 조각을 쉽게 끼워넣을 수 있도록 `part` 계열의 메소드들을 제공합니다.  
예제에 쓰인 `part_auto` 메소드는 자동으로 `/part/{리소스}/{액션}.{경로}.php` 파일을 호출하고 `데이터`를 조각을 전달하는 역할을 합니다.  
즉 `ESP::part_auto("row", $row->items());` 코드는 `/part/article/list.row.php` 파일에 `$row->items()` 데이터를 전달합니다.  

부분을 구성하는 파일들 (`/part` 디렉토리 아래의 파일들)은 연관 배열로 전달받은 값들을 변수처럼 사용할 수 있습니다.  
`/part/article/list.row.php`의 `$id`와 `$title`은 `/src/article/list.php`의 `$row->items()` 연관 배열의 값입니다.

## JSON 다루기
`/src/article/read_json.php` 파일을 생성해 봅시다.
```
<?php            
    $model = ESP::auto_find();
    ESP::response_json($model);
```
이 코드는 현재 id의 상세 내용을 반환합니다.
ESP에는 JSON을 응답하기 위해서 `response_json`이라는 간단한 래퍼 함수가 있습니다. 
`response_json`은 배열, 연관 배열, 문자열, EspData 타입 전부에 대해 동작하므로 단순히 `ESP::response_json($데이터);` 형식으로 응답을 보장할 수 있습니다.

[http://localhost:8000/article/read_json/1](http://localhost:8000/article/read_json/1) 에서 확인해 보세요.

## 헤더와 푸터 추가하기
대부분의 웹 사이트는 공통의 헤더와 푸터를 사용합니다.  
ESP에서는 `part`를 이용해서 헤더와 푸터를 쉽게 붙일 수 있는 방법을 제공합니다.  
`/part/common/header.php` 파일을 만듭니다.  
```
<!DOCTYPE html>
<head>
<title>ESP</title>
</head>
<body>
<h1>헤더 영역</h1>
```

헤더와 마찬가지로 푸터도 넣겠습니다.
`/part/common/footer.php` 파일을 만듭니다.  
```
<footer>푸터 영역</footer>
</body>
</html>
```

이제 생성 페이지를 아래와 같이 수정합니다.
```
... 생략 ...

ESP::auto_save(null, ['title', 'content']);
?>
<?php ESP::part_header(); ?>
<form method="POST">

... 생략 ...
```
```
... 생략 ...

</form>
<?php ESP::part_footer(); ?>
```

`part_header()`와 `part_footer()` 메소드를 이용해서 헤더와 푸터를 붙일 수 있습니다.

## 회원가입
회원 가입을 만들어 봅시다.
### 회원 테이블
먼저 회원 테이블을 생성합니다. 만약 ESP에 내장된 회원 기능을 사용하려면 `esp_user` 테이블이 필수입니다.
```
CREATE TABLE `esp_user` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`login_id` VARCHAR(20) NOT NULL,	
	`login_pw` VARCHAR(256) NOT NULL,	
	`insert_date` DATETIME NOT NULL,
	`update_date` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
```

### 회원 가입 화면 및 기능
`/src/user/regist.php`
```
<?php
list($result, $message) = ESP::regist();
if ($result){
    ESP::redirect("/user/login");
}
?>
<?php ESP::part_header(); ?>
<form method="POST">
    <p>user_id : <input type="text" name="login_id" id="login_id" value="<?= ESP::param("login_id") ?? "" ?>" /></p>
    <p>user_pw : <input type="password" name="login_pw" id="login_pw"  value="<?= ESP::param("login_pw") ?? "" ?>"/></p>
    <p><input type="submit" value="회원가입" /></p>
</form>
<?php ESP::part_footer(); ?>
```
`regist()` 메소드는 `login_id`와 `login_pw` 파라미터를 바탕으로 회원 가입을 진행합니다.
`param()` 메소드는 파라미터를 읽습니다. http GET 요청일 때는 $_GET에서, http POST 요청일때는 $_POST에서 값을 읽고, 만약에 http 메소드에 해당하는 값이 없다면 다른 파라미터를 읽습니다.
## 로그인
로그인도 회원 가입과 비슷하므로 코드만 소개하겠습니다.
`/src/user/login.php`
```
<?php
if (ESP::login()){
    ESP::redirect("/article/list");
}
?>
<?php ESP::part_header(); ?>
<form method="POST">
<p>user_id : <input type="text" name="login_id" id="login_id" value="<?= ESP::param("login_id") ?? "" ?>" /></p>
    <p>user_pw : <input type="password" name="login_pw" id="login_pw"  value="<?= ESP::param("login_pw") ?? "" ?>"/></p>
    <p><input type="submit" value="로그인" /></p>
</form>
<?php ESP::part_footer(); ?>
```

## 로그아웃
로그아웃은 몹시 직관적입니다.
```
<?php
ESP::logout();
ESP::redirect("/article/list");
```

## 글쓰기에서 로그인 처리
글쓰기 파일에 `ESP::login_required();` 코드를 추가합니다.
`/src/article/create.php`
```
<?php
ESP::login_required();
ESP::auto_save(null, ['title', 'content']);

... 생략 ...
```
이제 `/article/create` 주소에 접근하면 로그인이 되어 있지 않다면 자동으로 로그인 페이지로 이동합니다.

## 글에 글쓴이 정보 넣기
글 테이블에 글쓴이 정보를 넣겠습니다. 테이블을 수정합니다.
```
ALTER TABLE `article` ADD COLUMN `author_id` VARCHAR(512) NULL DEFAULT NULL AFTER `update_date`;	
```

글쓰기 파일에 글쓴이 정보를 추가합니다.
`/src/article/create.php`
```
<?php
ESP::login_required();
ESP::auto_save(null, ['title', 'content'], ['author_id'=>ESP::login_id()]);
?>

... 생략 ...
```
`auto_save` 메소드의 첫번째 인수는 테이블 이름, 두번째는 POST 데이터 중 사용할 키 목록. 마지막 인수는 추가로 저장할 데이터입니다.

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
그렇지만 때로는 규칙과 단단함보다는 속도를 중시해야 하는 경우도 있습니다.  
특히 "출시하고 잊어버리는" 웹 에이전시 스타일의 개발에서는 더욱 그렇습니다.  

PHP는 "혼자 작업하기 좋은 언어"이고, "스스로 규칙을 세울 수 있다는 점에서 더욱 좋은 언어"입니다.  
ESP는 PHP의 이런 철학에 따라 "당면한 문제를 해결할 수 있는 툴박스"를 목표로 삼고 있습니다.  

## 제작자
저는 한국인이어서 영어를 완벽하게 구사할 수는 없습니다. 어색한 표현이 있어도 이해해 주세요.  

개발자로 일하는 시간동안 다양한 언어의 많은  프레임워크를 사용해 왔고, 저에게 맞는 프레임워크를 찾으려고 애썼습니다.  
이러한 시도의 결과 중 결과 중 하나는 ESP입니다.  

ESP를 사용해 보시고 버그 혹은 불만 사항이 있으면 알려주세요.  
감사합니다.  


