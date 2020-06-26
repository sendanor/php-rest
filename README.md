# php-rest

Simple object oriented PHP Framework to build JSON REST APIs.

> It's ***2020***. Why PHP? Node.js is better!

* **Less dependencies.** `php-rest` only requires PHP 7 features. No other external requirements.
* **Long Term Support.** This software will work unchanged next year, and probably in the year 2030, too. We'll need something like a zombie apocalypse to make it obsolete.
* **Easy deployment.** No need to setup process supervisors; your OS and *Apache* (or whatever HTTP server you use) already does that.
* **Less disk space.** `php-rest` is about 836 kB. *Our Node.js API framework takes* ***freaking*** *182 MB.*

---------------------------------------------------------------------------------------

### It's a framework, not a library.

`php-rest` is intended to be **a framework**, *not* ***a library***.

It's a tool you can use to easily build JSON based REST interfaces for your 
client side or backend projects, focus on your business logic and not to worry 
too much about the internal implementation details.

We use it to build backend APIs for our Node.js backends -- especially for our 
legacy projects.

---------------------------------------------------------------------------------------

### About our terminology

*Resource* is any REST path which you can make HTTP requests with different 
methods.

*Collection* is a resource which lets you interact with multiple resources. 
These are normally other elements. It's like a database table.

*Element* is a resource which lets you interact with a single item. It's like a 
row in a database table.

---------------------------------------------------------------------------------------

### Requirements

* PHP 7.2 or newer

Optional features:

* ***Web server software:*** *Apache*, *Nginx*, etc
* ***Database Server:*** *MySQL* Server 5.0. or newer

---------------------------------------------------------------------------------------

### Install

`npm install --save php-rest`

---------------------------------------------------------------------------------------

### Using `php-rest` with *Apache*

---------------------------------------------------------------------------------------

#### Securing your API with HTTPS

Using HTTPS in the year 2020 is recommended, free and there isn't many good 
reasons not to use it. We recommend using [Let's Encrypt](https://letsencrypt.org/). 
*Apache* has a good support for it, too.

---------------------------------------------------------------------------------------

#### Securing the project folder with `.htaccess`

You should start your project carefully securing your web folder with a 
`.htaccess` file(s). We have [a sample file](examples/.htaccess) you can use as 
a starting point.

You can -- and probably *should* -- also use it to control who can make POST, 
PUT and DELETE requests. ***`php-rest` doesn't have any ACL support built-in.***

---------------------------------------------------------------------------------------

##### Basic HTTP Authentication

Setup a password file at a path outside of webroot.

```
$ htpasswd -c /path/to/your/htusers foo
$ chmod 604 /path/to/your/htusers
```

Configure `.htaccess` file:

```
AuthName "REST API"
AuthType Basic
AuthUserFile **/path/to/your/htusers**
require valid-user
```

If you want to let people use GET, HEAD and OPTIONS; and only require 
authenticated users for other methods, you can limit it like this:

```
<LimitExcept GET OPTIONS HEAD>
Require valid-user
</LimitExcept>
```

***Please note:*** You can also use *MySQL* instead of files with 
[mod_authn_dbd](https://httpd.apache.org/docs/2.4/mod/mod_authn_dbd.html).

---------------------------------------------------------------------------------------

#### Setup a front controller

Your REST backend will operate from a single front controller -- our example 
uses [index.php](examples/index.php).

---------------------------------------------------------------------------------------

##### Pass everything to the front controller

We'll use `.htaccess` to tell *Apache* to pass everything to our `index.php`:

```
AcceptPathInfo On
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,L]
```

---------------------------------------------------------------------------------------

##### Enable autoloader

Our framework has an interface to use `__autoload()`, which you can 
enable in your front controller:

```
function __autoload ($className) {
	return \REST\Autoloader::load($className);
}
```

You should let it know where to find your business logic classes:

```
\REST\addAutoloadPath(dirname(__FILE__) . '/src')
```

---------------------------------------------------------------------------------------

##### Enable database support

If you want to use our built-in MySQL database support, you need to configure 
it:

```
require_once('path/to/REST/Database/index.php');
$db = new \REST\Database\MySQL\Database(REST_HOSTNAME, REST_USERNAME, REST_PASSWORD, 
REST_DATABASE);
$db->charset(REST_CHARSET);
$db->setTablePrefix(REST_TABLE_PREFIX);
\REST\Database\setDefaultDatabase($db);
```

---------------------------------------------------------------------------------------

###### Extending from `\REST\Database\DatabaseElement` and `\REST\Database\DatabaseCollection`

These classes implement complete REST resources to use *MySQL* tables with 
*GET*, *POST*, *PUT*, and *DELETE* operations.

Minimal required setup is to call `parent::setTableName('contact');` from the 
extending class to map it to specific *MySQL* table.

Of course, you can extend or overwrite any method.

***Be careful!*** These elements **do not have any ACL built-in**. If you expose 
DatabaseElement -- or anything extended from it -- to the public, it will let 
delete and modify any row in your *MySQL* table.

---------------------------------------------------------------------------------------

##### Expose automatic API documentation with OPTIONS method

`php-rest` can automatically use PHP's ReflectionClass to read documentation 
from PHPdoc comments in your implementation and provide it to users as OPTIONS 
method. To enable this feature use `\REST\enableAutoOptions();`.

---------------------------------------------------------------------------------------

##### Setup routes to resource implementations

Finally you'll need to map your routes to your business logic classes.

```
\REST\run(array(
    "/" => "RootElement",
    "/contact" => "ContactCollection",
    "/contact/:contact_id" => "ContactElement"
));
```

---------------------------------------------------------------------------------------

#### Writing REST resources

You can extend from our abstract classes:

* `\REST\Element`, `\REST\Collection`, or `\REST\Resource` for non-database 
resources
* `\REST\Database\DatabaseElement`, `\REST\Database\DatabaseCollection`, or `\REST\Database\DatabaseResource` 
for database-based resources

When you want to overwrite a built-in method, you simply write a function with 
the name of the method:

```
/** The root resource for this REST service */
class RootElement extends \REST\Element {
        /** Doesn't return anything useful yet. Simply a hello world. */
        function get (iRequest $request) {
                return array(
                        'hello' => 'world'
                );
        }
}
```

***Note:*** You can also call `parent::get($request)` to use the parent 
implementation.

---------------------------------------------------------------------------------------

##### `$request->getPath()`

Returns the path of the request, like `/Contact/1`.

---------------------------------------------------------------------------------------

##### `$request->getParams()`

Returns the route parameters, like `1` for `$params['contact_id']` if route had 
path like `/Contact/:contact_id`.

---------------------------------------------------------------------------------------

##### `$request->getQuery()`

Returns the request query params, like `/Contact?email=foo@example.com` ==> 
`$query['email']` ==> `'foo@example.com'`.

---------------------------------------------------------------------------------------

##### `$request->getInput()`

Returns the parsed JSON body from the request.

---------------------------------------------------------------------------------------

##### Other methods

See [Request.class.php](lib/REST/Request.class.php) for other public methods 
available.

---------------------------------------------------------------------------------------

### Running local PHP for development

```
cd examples
php -S localhost:8000
```

...and open http://localhost:8000

---------------------------------------------------------------------------------------

### Testing the API with *curl*

Source code for the example API at [examples/](examples/).

```
$ curl -s -X OPTIONS https://www.example.com/api/|python -m json.tool
{
    "description": "The root resource for this REST service",
    "methods": [
        {
            "description": "Doesn't return anything useful yet. Simply a hello world.",
            "method": "get"
        },
        {
            "description": "Returns information for this REST resource",
            "method": "options"
        }
    ],
    "routes": [
        {
            "description": "Contact collection",
            "route": "/contact"
        },
        {
            "description": "Contact Element",
            "route": "/contact/:contact_id"
        }
    ]
}
```

```
$ curl https://www.example.com/api/
{"hello":"world"}
```

```
$ curl -s -X OPTIONS https://www.example.com/api/contact| python -m json.tool
{
    "description": "Contact collection",
    "methods": [
        {
            "description": "Returns all elements in the collection. You may use query params to limit matches.",
            "method": "get"
        },
        {
            "description": "Create a new element in the collection",
            "method": "post"
        },
        {
            "description": "Returns information for this REST resource",
            "method": "options"
        }
    ],
    "routes": [
        {
            "description": "Contact Element",
            "route": "/contact/:contact_id"
        }
    ]
}
```

```
$ curl -s -X GET https://www.example.com/api/contact| python -m json.tool
[
    {
        "contact_id": "5",
        "creation": "0000-00-00 00:00:00",
        "email": "",
        "name": "foo",
        "updated": "0000-00-00 00:00:00"
    },
    {
        "contact_id": "6",
        "creation": "0000-00-00 00:00:00",
        "email": "",
        "name": "bar",
        "updated": "0000-00-00 00:00:00"
    },
    {
        "contact_id": "7",
        "creation": "0000-00-00 00:00:00",
        "email": "",
        "name": "hello",
        "updated": "0000-00-00 00:00:00"
    }
]
```

```
$ curl -s -X POST -d '{"name":"Mr. Grey"}' https://www.example.com/api/contact| python -m json.tool
{
    "contact_id": "8",
    "creation": "0000-00-00 00:00:00",
    "email": "",
    "name": "Mr. Grey",
    "updated": "0000-00-00 00:00:00"
}
```

```
$ curl -s -X OPTIONS https://www.example.com/api/contact/8| python -m json.tool
{
    "description": "Contact Element",
    "methods": [
        {
            "description": "Returns the element",
            "method": "get"
        },
        {
            "description": "Removes the element",
            "method": "delete"
        },
        {
            "description": "Changes the element",
            "method": "post"
        },
        {
            "description": "Returns information for this REST resource",
            "method": "options"
        }
    ],
    "routes": []
}
```

```
$ curl -s -X POST -d '{"name":"Mr. Black"}' https://www.example.com/api/contact/8| python -m json.tool
{
    "contact_id": "8",
    "creation": "0000-00-00 00:00:00",
    "email": "",
    "name": "Mr. Black",
    "updated": "0000-00-00 00:00:00"
}
```

```
$ curl -s -X GET https://www.example.com/api/contact/8| python -m json.tool
{
    "contact_id": "8",
    "creation": "0000-00-00 00:00:00",
    "email": "",
    "name": "Mr. Black",
    "updated": "0000-00-00 00:00:00"
}
```

```
$ curl -s -X DELETE https://www.example.com/api/contact/8| python -m json.tool
{
    "deleted": "success",
    "id": "8"
}
```
