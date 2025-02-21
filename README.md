### Laravel ready to go

- laravel 11.43.2
- nova 5.1.5

### Installation:

Composer command:

```shell
composer create-project mphpmaster/laravel-nova-blueprint-permission
```

### Config:

- run fresh setup:

```shell
composer install
php artisan setup -mfs
```

- resetup:

```shell
php artisan setup --migrate --fresh --seed
```

### Api Status:

* `200` OK
* `201` Created
* `400` Bad request
* `401` Authentication failure
* `403` Forbidden
* `404` Resource not found
* `405` Method Not Allowed
* `422` Unprocessable Content _(Validation errors)_
