# Sql Blade Package

The packages execute raw SQL queries which are in a separate file with the flexibility to embed Blade extensions, enabling the dynamic creation of queries using Blade syntax.

# About the package

- You can use Blade syntax when creating queries.
- You place queries in separate files. (Ex: `select_user.blade.sql`).
- Execute your queries using `Zjk\SqlBlade\Contract\SqlBladeInterface` service.
- Result of execution `Zjk\SqlBlade\Contract\SqlBladeInterface->executeQuery(..)` is instance of Doctrine\DBAL\Driver\Result, use their methods to get results.
- Query execution via transaction `Zjk\SqlBlade\Contract\SqlBladeInterface->transaction(..)`

# Installation

Add `zjkiza/sql-blade` to your composer.json file:

```
composer require zjkiza/sql-blade
```

# A quick example 
## Working with the bundle

It is necessary to define which directory/directories will be used for storing files with sql queries.

In file `config/view.php` add paths

Example:

```php
    
    'paths' => [
        ...
        app_path('Sql/User'),
        ..
    ],
```

Create a sql query (to the directory defined above). Example (`select_user.blade.sql`):

```sql
# file select_user.blade.sql

SELECT u.id, u.email FROM users u
WHERE 1

@isset($emails)
    AND u.email IN ( :emails )
@endif

@isset($ids)
  AND u.id IN ( :ids )
@endif

ORDER BY
    @isset($ids)
        u.id
    @else
        u.email
    @endisset
;
```

Notice: At the end of the query, you must put `;`  

Working in php, Example:

```php

namespace App\Example;

use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Doctrine\DBAL\Result;

class MyRepository {
    
    private SqlTwigInterface $sqlBlade;
    
    public function __construct(SqlBladeInterface $sqlBlade) 
    {
        $this->sqlBlade = $sqlBlade;
    }
    
    public function users(): array
    {
        return $this->sqlBlade->executeQuery('select_user', [
                'emails' => ['foo@example.com', 'bar@example.com']
               ],[
                 'emails' => ArrayParameterType::STRING,
              ])->fetchAllAssociative()
    }    
    
    public function withTransaction(): array
    {
        $result = $this->sqlBlade->transaction(function (SqlBladeInterface $sqlBlade): Result {
            return $sqlBlade->executeQuery('select_without_blade');
        }, TransactionIsolationLevel::READ_UNCOMMITTED);

        return $result->fetchAllAssociative();
    }   
}

```

##  Crafting intricate and sophisticated queries.

At times, it's essential to have the flexibility to construct your queries in accordance with the logic of your application. This is where the sqlBlade comes into play, leveraging Blade to pre-parse all your query resources. This enables you to dynamically shape your queries as needed.

# Query logging

Executed queries are logged in `laravel.log` file. In which there are: 
- file name, 
- sql query that was executed, 
- arguments, 
- argument types,
- query execution time in sec.