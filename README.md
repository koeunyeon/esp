# ESP
ESP stands for **Extreme Short PHP**.  
It is a PHP framework that excludes the two and is oriented towards short code.  
Any part that can be handled automatically is as automated as possible.  

-The Korean introduction page can be viewed at [ESP Korean Introduction](https://github.com/koeunyeon/esp/blob/main/README.ko.md).
-The English introduction page is in [ESP English Introduction](https://github.com/koeunyeon/esp/blob/main/README.md).

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

## Create a blog writing screen
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

## Create blog writing function
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

## Create a blog post view
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

## Create a blog post edit
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

## Create a blog post delete
This time it is delete. Create a `/src/article/delete.php` file.
```
<?php
ESP::auto_delete();
```
It's only one line. ESP automatically deletes the resource and goes to the list page.  

## show the list of blog posts
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

# ABOUT
## ESP is not an MVC framework.
ESP is not an MVC framework.  
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