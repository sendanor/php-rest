# php-rest

Simple object oriented PHP Framework to build JSON REST APIs.

> It's ***2017***. Why PHP? Node.js is better!

* **Less dependencies.** `php-rest` only requires PHP 5.3 features. No other external requirements.
* **Long Term Support.** This software will work unchanged next year, and probably in year 2027, too.
* **Easy deployment.** No need to setup process supervisors; your OS and Apache (or whatever HTTP server you use) already does that.
* **Less disk space.** `php-rest` is about 836 kB. *Our Node.js API framework takes* ***freaking*** *182 MB.*

### Framework, not a library.

`php-rest` is intended to be **a framework**, *not* ***a library***.

It's a tool you can use to easily build JSON based REST interfaces for your 
client side or backend projects, focus on your business logic and not to worry 
about the internal implementation details.

We use it to build backend APIs for our Node.js backends -- especially for our 
legacy projects.

### Requirements

* PHP 5.3. or newer

Optional features:

* ***Web server software:*** Apache, Nginx, etc
* ***Database Server:*** MySQL Server 5.0. or newer

### Install

`npm install --save php-rest`

### Example usage

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
