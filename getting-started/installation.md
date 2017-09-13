# Installation

### Server Requirements

The O2System framework has a few system requirements to start working on your local application development environment. You will need to make sure your server meets the following requirements:

#### HTTP Server

* Apache
  * Module Rewrite \(mod\_rewrite\)
  * Module Header \(mod\_header\)
* NGINX
  * PHP FPM
* Microsoft IIS
  * PHP Fast-CGI
  * Rewrite Rule
* PHP version 
  &gt;
  = 5.6.0+

#### PHP Extensions

* Fileinfo
* Mcrypt
* OpenSSL
* Mbstring
* Tokenizer
* XML
* APCu 
  &
   Zend OPCache



### Installing Framework

The O2System Framework use Composer as Dependency Management so the easiest way to installing is issuing the Composer`create-project`command in your terminal:

```
composer create
-
project 
-
-
prefer
-
dist o2system/o2system [project
-
folder] [version]
```

#### Local Development Server

If you have PHP installed locally and you would like to use PHP's built-in development server to serve your application, you may use the`serve`O2System Framework Console command. This command will start a development server at`http://localhost:8000`:

```
php o2system serve
```

You may also define PHP's built-in development server to serve your application with custom hostname and custom port number:

```
php o2system serve 
-
-
host domain
.
com 
-
-
port 
80
```



### Configuration

#### Public Directory

After installing O2System Framework, you should configure your web server's document / web root to be the`public`directory. The`index.php`in this directory serves as the front controller for all HTTP requests entering your application.

#### Configuration Files

All of the configuration files for the O2System Framework are stored in the`app/Config`directory. Each option is documented, so feel free to look through the files and get familiar with the options available to you.

#### Directory Permissions

After installing O2System Framework, you may need to configure some permissions. Directories within the`cache`and the`storage`directories should be writable by your web server or O2System Framework will not run.



  


