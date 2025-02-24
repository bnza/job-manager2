### job-manager2

A simple job manager/runner for Symfony applications

## Installation

```shell
cd packages
git clone https://github.com/bnza/job-manager2.git
cd erc-api
```

Update your ```composer.json```

```json
{
    ...
    "require": {
        ...
        "bnza/job-manager2": "@dev",
        ...
    },
    ...
    "minimum-stability": "alpha",
    "repositories": [
        {
            "type": "path",
            "url": "/srv/api/packages/*"
        }
    ]
}
```

and update the project

```shell
composer update
```

Modify ```config/packages/doctrine.yaml``` setting up the correct connection and entity manager for the bundle

Connection and entity manager name can be changed but must match the ```config/packages/bnza_job_manager.yaml```
parameter value

```yaml
doctrine:
    dbal:
        # ...
        default_connection: default
        connections:
            default:
            # ...
            bnza_job_manager:
            # your connection settings here 
    orm:
        # ...
        default_entity_manager: default
        entity_managers:
            default:
            # ...
            bnza_job_manager:
                connection: bnza_job_manager
```

Add ```config/packages/bnza_job_manager.yaml``` with the following content:

```yaml
bnza_job_manager:
    em_name: bnza_job_manager #! your entity manager name here
```

Create the database and migrate the schema

```shell
php bin/console doctrine:database:create --connection=bnza_job_manage
php bin/console bnza:job-manager:migrations:migrate 
```


