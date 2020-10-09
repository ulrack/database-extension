# Ulrack Database Extension - Create a database connection

After the package has been installed, it can be used to easily create database
connections for the project.

## Configuring the connection

To create a database connection in a project, create a new folder in the
`configuration` folder named `database-connections`. Inside this folder, create
a configuration for the database, by default only the PDO type is supported.

```json
{
    "main": {
        "type": "pdo",
        "driver": "mysql",
        "database": "",
        "host": "localhost",
        "username": "my-user",
        "password": "@{parameters.database.password}"
    }
}
```

The `type` field contains the name of the responsible manager for creating the
database connection. The `driver` determines the type of connection. The
`database` field is optional and can contain the name of the database.
The `host` field is used to determine the location of the database.
The `username` and `password` fields determine the credentials for connecting to
the database. Optionally, the `options` and `attributes` nodes can be used to
configure additional settings. See the
[PHP PDO manual](https://www.php.net/manual/en/class.pdo.php) for more information.

In the configuration, parameters can be used. This can be usefull when using
environment variables to setup database connections. For the above example a
parameters file could look like the following:

```json
{
    "database.password": "${DB_PASSWORD}"
}
```

## Testing the connection

In order to see if the connections have been setup correctly, a command can be run.
First of all, after making configuration changes, run the following command:

```
bin/application cache clear
```

This will clear the cache and read the changed configuration on the next run of
the application.

To check the available database connections, run the following command:

```
bin/application db connection list
```

This will display a list of available database connections.

To check whether the database connections can be made, run the command:

```
bin/application db connection test
```

This will check each configured connection, whether or not a connection request
will result in a valid connection.

## Using the connection

When the database connection is set-up, it can be easily retrieved from within
the services layer. An example would be with the following class:

```php
<?php

namespace MyVendor\MyProject;

use Ulrack\Dbal\Common\ConnectionInterface;

class MyClass
{
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }
}

```

To provide this class with the database connection created in the first segment,
create a services file with the following content:

```json
{
    "my.class": {
        "class": "\\MyVendor\\MyProject\\MyClass",
        "parameters": {
            "connection": "@{database-connections.main}"
        }
    }
}
```

After clearing the cache and using this service, the database connection will be
provided automatically.

## Further reading

[Back to usage index](index.md)

[Installation](installation.md)