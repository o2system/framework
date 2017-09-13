# Introduction

The O2System Framework is a next generation of PHP framework which designed to adapt easily in small to large application development scale. Therefore, understanding the fundamental of O2System Framework becomes very important for us to understand what the differences with other php frameworks.

### Based On PHP Standard Recommendations \(PSR\)

The writing of php programming codes is very diverse. This greatly affects php programmers, especially when working in a team. A group of PHP projects calling itself PHP Framework Interop Group \(PHP-FIG\) began to build a collaboration through the standards of writing php programming codes and became a new standard in writing php programming codes.

O2System PSR is one of the most important foundations in the O2System Framework based on PHP Standard Recommendations \(PSR\) that has been launched by PHP-FIG. The entire writing of php codes in the O2System Framework and its libraries are created in accordance with the basic coding standards of PSR-1 and PSR-2. The following is a list of the PSRs that are applied to the O2System Framework:

| Based | Title |  |
| :--- | :--- | :--- |
| PSR-1 | Basic Coding Standard | [http://www.php-fig.org/psr/psr-1/](http://www.php-fig.org/psr/psr-3/) |
| PSR-2 | Coding Style Guide | [http://www.php-fig.org/psr/psr-2/](http://www.php-fig.org/psr/psr-3/) |
| PSR-3 | Logger Interface | [http://www.php-fig.org/psr/psr-3/](http://www.php-fig.org/psr/psr-3/) |
| PSR-4 | Autoloading Standard | [http://www.php-fig.org/psr/psr-4/](http://www.php-fig.org/psr/psr-4/) |
| PSR-6 | Caching Interface | [http://www.php-fig.org/psr/psr-6/](http://www.php-fig.org/psr/psr-6/) |
| PSR-7 | HTTP Message Interface | [http://www.php-fig.org/psr/psr-7/](http://www.php-fig.org/psr/psr-7/) |

O2System PSR also comes with PHP Classes Pattern which is a set of php abstract class that aims to become the standard design pattern structure of the codes on O2System Framework as well as the application to be made. Here is a list of PHP Pattern Classes that exist on the O2System Framework:

* Parent-Child Pattern Class
* Data Storage Pattern Class
* Factory-Prototype Pattern Class
* Handler Pattern Class
* Item Storage Pattern Class
* Object Container Pattern Class
* Registry Pattern Class Object
* Observer Pattern Class
* Singleton Pattern Class
* Subject Pattern Class
* Variable Storage Pattern Class

### Relies on Standard PHP Library \(SPL\)

O2System SPL which is also one of the important foundations of the O2System Framework is made relies on the Standard PHP Library \(SPL\) and is created by implementing the interfaces of the SPL.

Some php programmers may have never heard of the existence of the Standard PHP Library \(SPL\). SPL is a collection of interfaces and php classes created to solve common problems. The SPL provides a set of standard datastructure, a set of iterators to traverse objects, a set of interfaces, an Exception set of standards, a number of classes to work with files and provides a set of functions like spl\_autoload\_register used by Composer and O2System Framework Autoloader.

SPL has been introduced since the php 5.0 version was launched and became the default package from php since then. SPL is not an external library or external extension but is available and compiled in php packages.

## Comes with Developer Facility

Some difficulties of the php programmer is when to do the testing, debugging and profilling. But in the O2System Framework there is an O2System Gear that provides a set of functions and a set of class libraries for unit testing, debugging and profilling. The following is a list of some of the features already available:

* Browser Debugging Toolbar
* Debugging Class with Helper
* Profiler Class
* Unit Testing Class
* Browser and Command Line Interface \(CLI\) Print-Out

## Kernel as Core Framework

In the world of computer programming, the kernel is known as the core of an operating system on a computer. The kernel is the first program loaded on start-up that handles start-up services required by the system and where the input / output \(I/O\) requests to the application are processed for the first time.

One of the differences between O2System Framework and other php frameworks is that it has the kernel as the core of its framework system. The kernel owned by the O2System Framework is loaded at start-up and runs automatically the services required by the framework or required by libraries created for the O2System Framework.

The kernel type of the O2System Framework can be included in the Hybrid \(or modular\) category because it runs multiple services at start-up. The services that run at start-up are:

* Profiler Service
* Language Service
* Logger Service
* Shutdown Service

## Singleton Design Pattern

In the software engineering world a singleton pattern is a program code design in which class instantiation is restricted to only one object. Inside the O2System Framework only one instance of the core system is allowed, therefore the singleton pattern design is applied.

The function of the system instance is to coordinate all actions and services that occur in the system and application into a single system. This allows the system to operate very efficiently in memory usage that also provides a global status within it. This instance is often also called Super Global Instance.

## Registry System

Designed to resemble an operating system, the existence of a registry system adds one more difference to the O2System Framework with other php frameworks. The system registry in the O2System Framework stores data from the modular system in the O2System Framework, especially data about the namespace aliases of the modules used by the Autoloader system to obtain the directory location of the namespace module called.

## Static Page

By default the O2System Framework has static-page support, This adds another difference from the O2System Framework with other php framework. The term for the static-page in the O2System Framework is called Page or Pages term in plural.

