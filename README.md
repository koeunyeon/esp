# ESP
Extreme Short PHP Framework

# NO MVC Framework
ESP is not an MVC framework. 
ESP's goal is to develop faster, even if it's messed up.
ESP is confusing, unorganized, but contains useful functions. 

Of course, I know that the MVC structure is great. 
But I also know that sometimes you need to develop faster than a good structure.

Every tool has its own role. No matter how good a hammer is, it won't be suitable for making small holes.

# Let's Start
## Create database table
First, let's create a database table.
Since ESP is specialized for MySQL, we will create it in MySQL.
```
CREATE TABLE `article` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(50) NOT NULL,
	`content` LONGTEXT NOT NULL,
	`insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`update_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
)
```

`id`, `insert_date`, and `update_date` are required columns used internally by ESP.

## Provide database access information
Next, you need to tell ESP the database connection information.

Open the `core/esp.config.php` file and enter the database connection information.
```
<?php
$db_config = [
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'pdb',
    'charset' => 'utf8',
    'username' => 'puser',
    'password' => 'p_pw1234'
];
```

That's all of the settings. It's very simple, isn't it?

## 

# About Me
I am Korean, so I can't speak English perfectly.
Please understand even if there are awkward expressions.

I have been working as a web developer for over 10 years.
If it's long, I've been using many frameworks for a long time, and I've tried to find a framework that's right for me.

One of the results is ESP.
Try it out, and let us know if you have any bugs or complaints.
Thank you.


D:\programs\xampp\php\php.exe -S localhost:8000