### Laravel ready to go
- laravel 9.42.2
- nova 4.10.0 (Silver Surfer)
- blueprint supports nova
- 
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
