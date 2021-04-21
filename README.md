# ESP
ESP stands for **Extreme Short PHP**.  
It is a PHP framework that excludes the two and is oriented towards short code.  
Any part that can be handled automatically is as automated as possible.  

- The Korean introduction page can be viewed at [ESP Korean Introduction](https://github.com/koeunyeon/esp/blob/main/README.ko.md).
- The English introduction page is in [ESP English Introduction](https://koeunyeon.github.io/esp/).

To get started right away, see [getting started](https://koeunyeon.github.io/esp/).
# Features
## Database connection setting
`core/esp.config.php`
```
<? PHP
$db_config=[
    'Host' =>'local host',
    'Port' => '3306',
    'dbname' =>'pdb',
    'charset' =>'utf8',
    'Username' =>'fuser',
    'Password' =>'p_pw1234'
];
```

## URL Mapping
When the `/article/create` URL is requested, the `/src/article/create.php` file is executed.  
ESP's URL rule consists of `/{resource}/{action}/{parameter}`.

## Save (Create / Edit)
```
ESP::auto_save();
```
ESP's `auto_save` method automatically attempts to save data to the `database table` corresponding to `{resource}` for http POST requests.  
The data to be saved is `http POST request ($_POST)`.  
If `{action}` is `create`, `insert` is executed, and if `edit`, `update` is executed.  

## Data find
```
$model = ESP::auto_find();
```
There is only one line of code to fetch data from the database.
ESP uses the `/{resource}/{action}/{id}` URL rule to change the data corresponding to `{id}` in the database table corresponding to `{resource}` by `ESP::auto_find()` method. 

## Resource link
The static methods `link_edit()`, `link_delete()`, and `link_list()` are helpers that automatically create edit, delete, and list links for the current `{resource}`.  

## Flexable data model
The loaded data is used as an object like `$model->title`.  
`$model` is a `EspData` type. Even if there is an invalid key, an empty string (`""`) is returned without returning an error.  

## Delete
```
<?php
ESP::auto_delete();
```
It's only one line. ESP automatically deletes the resource and goes to the list page.  

## Pagnate
```
$page_list = ESP::auto_pagenate();
```
ESP can automatically fetch data from database tables according to the URL `{resource}/list/{page}`.

## Handling JSON
```
<?php
     $model = ESP::auto_find();
     ESP::response_json($model);
```
This code returns the details of the current id.  
There is a simple wrapper function called `response_json` to respond to JSON in ESP.  
Because `response_json` works for all arrays, associative arrays, strings, and EspData types, you can guarantee a response simply in the form of `ESP::response_json($data);`.

## Part
```
ESP::part_auto("row", $row->items());
```

ESP assumes that a web page can be made up of several pieces. Therefore, it provides `part` family of methods to easily insert each piece.  
The `part_auto` method used in the example is responsible for automatically calling the `/part/{resource}/{action}.{path}.php` file and passing the `data` fragment.  
That is, the `ESP::part_auto("row", $row->items());` code passes `$row->items()` data to the `/part/article/list.row.php` file.  

```
<li><a href="<?= ESP::link_read($id) ?>"><?= $title ?></a></li>
```

## Header, footer part
The header file must be in the path `/part/common/header.php`.  
The footer file must be in the path `/part/common/footer.php`.  

Header and footer files can be called as follows.
```
<?php ESP::part_header(); ?>
```
```
<?php ESP::part_footer(); ?>
```
## Read Query String Parameter
```
ESP::param("login_id")
```

The `param()` method reads a parameter. In the case of http GET request, the value is read from $_GET, in the case of http POST request, the value is read from $_POST.  
If there is no value corresponding to the http method, other parameters are read.  


## Read URI Parameter
```
ESP::param_uri(0);
```
If `/article/read/1_times/2_times/3_times` URL is called, you can get the value of `1_times` using the `ESP::param_uri(0)` code.

## User Table
First, create a member table. If you want to use the membership function built into ESP, the `esp_user` table is required.  
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
## Regist
```
list($result, $message) = ESP::regist();
```
The `regist()` method proceeds with membership registration based on the `login_id` and `login_pw` parameters.  

## Login
```
ESP::login()
```
The `login()` method proceeds based on the `login_id` and `login_pw` parameters.  
And the session is used to handle the login.

## Log out
```
<?php
ESP::logout();
```

## Login Required
```
ESP::login_required();
```
User will be automatically directed to the login page if user is not logged in.

## Saving with additional data
Add author information to the writing file.
```
ESP::auto_save(null, ['title','content'], ['author_id'=>ESP::login_id()]);
```
## Checking if the author is the author when editing a post
```
<?php
ESP::author_if_not_matched_to_list();
```

The added code is `ESP::author_if_not_matched_to_list();`.  
ESP has a method `author_matched` that checks whether you are logged in if there is a `author_id` column in the table, and if you are logged in, the currently logged in ID matches the author_id.  
There is also an `author_if_not_matched_to_list()` that automatically moves to the list if it does not match.  


# ABOUT
## ESP is not an MVC framework.
Yes. ESP is not an MVC framework.  
ESP's goal is to develop faster.  
ESP is confusing and unstructured, but it does contain useful features.  

I know the MVC structure is great.  
But we also know that good things are better developed faster than structures.  

Every tool has its own role. Even the finest hammer is not suitable for making small holes.  

## PHP is not Java.
From PHP version 7, PHP is changing as if it targets the enterprise domain dominated by Java.  
But I think PHP and Java should have different positioning.  
It's not bad for a Java world where you have to set rules on everything like a nagging mother and listen to a stinging sound if it goes against the rules.  
Sometimes, though, you need to value speed over rules and rigidity.
This is especially true for "release and forget" web agency style development.  

PHP is a "good language to work with on your own", and "a better language in that you can set your own rules."  
ESP follows this philosophy of PHP and aims to be a "toolbox that can solve the problem at hand."  

## About Me
I am Korean, so I cannot speak English perfectly. Please understand even if there are awkward expressions.  

During my time as a developer, I have come across a lot of frameworks in various languages, and I have a framework that suits me and I'm struggling.  
One of the results of these attempts is ESP.  

Take a look at the ESP and let us know if you have any complaints.  
Thank you.  