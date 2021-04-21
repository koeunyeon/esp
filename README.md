# ESP
ESP stands for **Extreme Short PHP**.  
It is a PHP framework that excludes the two and is oriented towards short code.  
Any part that can be handled automatically is as automated as possible.  

- The Korean introduction page can be viewed at [ESP Korean Introduction](https://github.com/koeunyeon/esp/blob/main/README.ko.md).
- The English introduction page is in [ESP English Introduction](https://github.com/koeunyeon/esp/blob/main/README.md).

# Getting started
## what we will make
We create a very simple blogging system.  
If you are familiar with ESP, you can create a simple CRUD program within 15 minutes.  

## create database table
First, let's create a database table.  
ESP is specific to MySQL, so it is created by MySQL.  
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
`id`,`insert_date`,`update_date` are required columns used internally by ESP.  
It is necessary not to care when entering/modifying to be automatically generated by ESP.  


## Provide database access information
Next, let's tell ESP the database connection information.  
Enter the database connection information in the `core/esp.config.php` file.  
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

This is all settings.

## Blog writing screen
Create the `/src/article/create.php` file and enter the code below.  
```
<form method="POST">
    <p>title: <input type="text" name="title" id="title" /></p>
    <p>content: <input type="text" name="content" id="content" /></p>
    <p><input type="submit" value="save" /></p>
</form>
```
This is plain HTML code. File don't have PHP yet.  

Now let's open this file in a web browser.  
First, let's run the local PHP server with the command `php -S localhost:8000`. If you are using the portable version, you can use `D:\Programs\xampp\php\php.exe -S localhost:8000`.  

Instead of accessing the browser to http://localhost:8000/src/article/create.php, connect to [http://localhost:8000/article/create](http://localhost:8000/article/create) see.  
Now we know that if you write the code according to the `/src/{resource}/{action}.php` rule, you can access the `/{resource}/{action}` path.  

## Blog writing function
Let's modify the `/src/article/create.php` file as follows.  
```
<?php
ESP::auto_save();
?>
<form method="POST">
    <p>title: <input type="text" name="title" id="title" /></p>
    <p>content: <input type="text" name="content" id="content" /></p>
    <p><input type="submit" value="save" /></p>
</form>
```
Added 3 lines of code.  
```
<?php
ESP::auto_save();
?>
```
ESP's `auto_save` method automatically attempts to save data to the `database table` corresponding to `{resource}` for http POST requests.  
The data to be saved is `http POST request ($_POST)`.  
If `{action}` is `create`, `insert` is executed, and if `edit`, `update` is executed.  
Go to [http://localhost:8000/article/create](http://localhost:8000/article/create) again, enter the data, and check if it is saved in the database.  

## Blog post view
Automatically [http://localhost:8000/article/read/1](http://localhost:8000/article/read/1) after writing the title and content in http://localhost:8000/article/create You can see it go to.

However, because the blog post viewing function has not been created yet, a **PAGE NOT FOUND** error is displayed. Let's fix it.

Let's create a file called `/src/article/read.php`.
```
<?php
    $model = ESP::auto_find();
?>
<p>title: <?=$model->title ?></p>
<p>content: <?=$model->content ?></p>

<p><a href="<?= ESP::link_edit() ?>">edit</a></p>
<p><a href="<?= ESP::link_delete() ?>">Delete</a></p>
<p><a href="<?= ESP::link_list() ?>">List</a></p>
```

There is only one line of code to fetch data from the database.
```
$model = ESP::auto_find();
```
ESP `auto_find()`, similar to `auto_save()`, looks for data corresponding to `$id` in `{resource}`.  
Where is the `$id` variable? As you might expect, it reads `$_GET['id]`.  
If the URL is in the form of `{Resource}/{Action}/{ID}`, ESP automatically reads the `$id` variable from the URL instead of `$_GET['id']`.  
That is, instead of `/article/read?id=3`, it can also be used in the form of `/article/read/3`.  

The static methods `link_edit()`, `link_delete()`, and `link_list()` are helpers that automatically create edit, delete, and list links for the current `{resource}`.  

## Blog post edit
Create the `/src/article/edit.php` file.
```
<?php
ESP::auto_save();
$model = ESP::auto_find();
?>
<form method="POST">
     <p>title: <input type="text" name="title" id="title" value="<?= $model->title ?>" /></p>
     <p>content: <input type="text" name="content" id="content" value="<?= $model->content ?>" /></p>
     <input type="submit" value="save" />
</form>
```

ESP's `auto_save()` method differentiates between `insert` and `update` according to `{action}`.  
Therefore, even if you call the same function in `create.php` and `edit.php`, `insert` works in `create` and `update` works in `edit`.  

The loaded data is used as an object like `$model->title`.  
In the example code, `$model` is a `EspData` type. Even if there is an invalid key, an empty string (`""`) is returned without returning an error.  
In other words, even if there is no `missing` column in the `article` table, `$model->missing` returns `""`, so you can write the code as you think, regardless of whether there is actual data or not.  

## Blog post delete
This time it is delete. Create a `/src/article/delete.php` file.
```
<?php
ESP::auto_delete();
```
It's only one line. ESP automatically deletes the resource and goes to the list page.  

## Blog post list
Finally, let's show the list of articles. Create a `/src/article/list.php` file.
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

I'll create one more file before running it yet. The path is `/part/article/list.row.php`.
```
<li><a href="<?= ESP::link_read($id) ?>"><?= $title ?></a></li>
```

ESP assumes that a web page can be made up of several pieces. Therefore, it provides `part` family of methods to easily insert each piece.  
The `part_auto` method used in the example is responsible for automatically calling the `/part/{resource}/{action}.{path}.php` file and passing the `data` fragment.  
That is, the `ESP::part_auto("row", $row->items());` code passes `$row->items()` data to the `/part/article/list.row.php` file.  

Files that make up a part (files under the `/part` directory) can use the values ​​passed in associative arrays like variables.  
`$id` and `$title` in `/part/article/list.row.php` are the values ​​of the `$row->items()` associative array in `/src/article/list.php`.  

## Handling JSON
Let's create the `/src/article/read_json.php` file.
```
<?php
     $model = ESP::auto_find();
     ESP::response_json($model);
```
This code returns the details of the current id.
There is a simple wrapper function called `response_json` to respond to JSON in ESP.
Because `response_json` works for all arrays, associative arrays, strings, and EspData types, you can guarantee a response simply in the form of `ESP::response_json($data);`.

Check it out at [http://localhost:8000/article/read_json/1](http://localhost:8000/article/read_json/1).


## Adding headers and footers
Most websites use a common header and footer.  
ESP provides an easy way to attach headers and footers using `part`.  
Create a `/part/common/header.php` file.  
```
<!DOCTYPE html>
<head>
<title>ESP</title>
</head>
<body>
<h1>Header area</h1>
```

Just like the header, I'll put the footer.  
Create a `/part/common/footer.php` file.  
```
<footer>footer area</footer>
</body>
</html>
```

Now modify the creation page as shown below.  
```
... skip ...

ESP::auto_save(null, ['title','content']);
?>
<?php ESP::part_header(); ?>
<form method="POST">

... skip ...
```
```
... skip ...

</form>
<?php ESP::part_footer(); ?>
```

You can attach headers and footers using the `part_header()` and `part_footer()` methods.  

### Membership screen and features
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
    <p>user_id: <input type="text" name="login_id" id="login_id" value="<?= ESP::param("login_id") ?? "" ?>" /></p>
    <p>user_pw: <input type="password" name="login_pw" id="login_pw" value="<?= ESP::param("login_pw") ?? "" ?>"/></p>
    <p><input type="submit" value="submit" /></p>
</form>
<?php ESP::part_footer(); ?>
```
The `regist()` method proceeds with membership registration based on the `login_id` and `login_pw` parameters.  
The `param()` method reads a parameter. In the case of http GET request, the value is read from $_GET, in the case of http POST request, the value is read from $_POST. If there is no value corresponding to the http method, other parameters are read.  
## login
Login is similar to signing up, so I'll just introduce the code.  
`/src/user/login.php`
```
<?php
if (ESP::login())(
    ESP::redirect("/article/list");
}
?>
<?php ESP::part_header(); ?>
<form method="POST">
<p>user_id: <input type="text" name="login_id" id="login_id" value="<?= ESP::param("login_id") ?? "" ?>" /></p>
    <p>user_pw: <input type="password" name="login_pw" id="login_pw" value="<?= ESP::param("login_pw") ?? "" ?>"/></p>
    <p><input type="submit" value="login" /></p>
</form>
<?php ESP::part_footer(); ?>
```
## Log out
Logging out is extremely intuitive.
```
<?php
ESP::logout();
ESP::redirect("/article/list");
```

## Login handling in writing
Add the code `ESP::login_required();` to your writing file.
`/src/article/create.php`
```
<?php
ESP::login_required();
ESP::auto_save(null, ['title','content']);

... skip ...
```
Now, when you access the `/article/create` address, you will be automatically directed to the login page if you are not logged in.

## Put author information in the article
I will put the author information in the article table. Modify the table.
```
ALTER TABLE `article` ADD COLUMN `author_id` VARCHAR(512) NULL DEFAULT NULL AFTER `update_date`;
```

Add author information to the writing file.
`/src/article/create.php`
```
<?php
ESP::login_required();
ESP::auto_save(null, ['title','content'], ['author_id'=>ESP::login_id()]);
?>

... skip ...
```
The first argument of the `auto_save` method is the table name, and the second is a list of keys to be used among POST data.  
The last argument is the additional data to be stored.

## Checking if the author is the author when editing a post
Let's add only one line to the post edit file.
```
<?php
ESP::author_if_not_matched_to_list();
ESP::auto_save(null, ['title','content']);
```

The added code is `ESP::author_if_not_matched_to_list();`.  
ESP has a method `author_matched` that checks whether you are logged in if there is a `author_id` column in the table, and if you are logged in, the currently logged in ID matches the author_id.  
There is also an `author_if_not_matched_to_list()` that automatically moves to the list if it does not match.  

## Add author confirmation to delete function as well
`/src/article/delete.php`
```
<?php
ESP::author_if_not_matched_to_list();
ESP::auto_delete();
```

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