# ESP
ESP stands for **Extreme Short PHP**.
It is a PHP framework that excludes the two and is oriented towards short code.
Any part that can be handled automatically is as automated as possible.


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

# ESP is not an MVC framework.
ESP is not an MVC framework.
ESP's goal is to develop faster.
ESP is confusing and unstructured, but it does contain useful features.

I know the MVC structure is great.
But we also know that good things are better developed faster than structures.

Every tool has its own role. Even the finest hammer is not suitable for making small holes.

# About Me
I am Korean, so I cannot speak English perfectly. Please understand even if there are awkward expressions.

During my time as a developer, I have come across a lot of frameworks in various languages, and I have a framework that suits me and I'm struggling.
One of the results of these attempts is ESP.

Take a look at the ESP and let us know if you have any complaints.
Thank you.