# php-rest

Simple object oriented PHP Framework to build JSON REST APIs.

### Requirements

* PHP 5.3. or newer
* For optional database operations: MySQL Server 5.0. or newer

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
