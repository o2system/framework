# Installation

## Server Requirements

The O2System framework has a few system requirements to start working on your local application development environment. You will need to make sure your server meets the following requirements:

### HTTP Server

* Apache
  * Module Rewrite \(mod\_rewrite\)
  * Module Header \(mod\_header\)
* NGINX
  * PHP FPM
* Microsoft IIS
  * PHP Fast-CGI
  * Rewrite Rule
* PHP version 
  v5.6.0+ 
  > For best performance, please use v7.0.0+

### PHP Extensions

* Fileinfo
* Mcrypt
* OpenSSL
* Mbstring
* Tokenizer
* XML
* APCu & Zend OPCache

## Installing Framework

The O2System Framework use Composer as Dependency Management so the easiest way to installing is issuing the Composer with `create-project` command in your terminal:

```
composer create-project o2system/o2system [project-name]
```

## Local Development Server

If you have PHP installed locally and you would like to use PHP's built-in development server to serve your application, you may use the `serve` O2System Framework Console command. This command will start a development server at `http://localhost:8000`:

```
php o2system serve
```

You may also define PHP's built-in development server to serve your application with custom hostname and custom port number:

```
php o2system serve --host=example.com --port=80
```

## Configuration

### Public Directory

After installing O2System Framework, you should configure your web server's document / web root to be the`public`directory. The`index.php`in this directory serves as the front controller for all HTTP requests entering your application.

### Configuration Files

All of the configuration files for the O2System Framework are stored in the`app/Config`directory. Each option is documented, so feel free to look through the files and get familiar with the options available to you.

### Directory Permissions

After installing O2System Framework, you may need to configure some permissions. Directories within the`cache`and the`storage`directories should be writable by your web server or O2System Framework will not run.

## Web Server Configuration

### Apache

O2System Framework includes a `public/.htaccess` file that is used to provide URLs without the`index.php`front controller in the path. Before serving O2System Framework with Apache, be sure to enable the`mod_rewrite`module so the `.htaccess` file will be honored by the server.

If the `.htaccess` file that ships with Laravel does not work with your Apache installation, try this alternative:

```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    Options +FollowSymlinks -Indexes
    RewriteEngine On

    # If you installed O2System in a subfolder, you will need to
    # change the following line to match the subfolder you need.
    # http://httpd.apache.org/docs/current/mod/mod_rewrite.html#rewritebase
    # RewriteBase /

    # Rewrite "www.example.com -> example.com"
    RewriteCond %{HTTPS} !=on
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^ http://%1%{REQUEST_URI} [R=301,L]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>
```

#### NGINX

If you are using Nginx, the following directive in your site configuration will direct all requests to the `index.php` front controller:

```
server {
	listen 80;

	root path/to/project/public;
	index index.php;

	server_name example.com;

	location / {
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
		if ($fastcgi_script_name !~ "^\/index\.php$") {
			return 403;
		}
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		include fastcgi_params;
	}

	location ~ /\. {
		deny all;
		access_log off;
		error_log off;
		log_not_found off;
	}
}
```

### Microsoft IIS

If you are using IIS create a web.config file at your public directory and put the following code into it, now all request will direct all requests to the `index.php` front controller:

```
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <clear />
                <rule name="REQUEST_URI" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll" trackAllCaptures="false">
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

  


