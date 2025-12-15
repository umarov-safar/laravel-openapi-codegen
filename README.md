# Laravel OpenAPI Code Generation

## Introduction
This package provides a convenient way to generate Laravel routes, requests, controllers and etc. based on OpenAPI specifications in YAML format.


### Motivation
For example, we have this openapi.yaml
```yaml
openapi: 3.0.0
info:
  title: User API
  version: 1.0.0
paths:
  /users:
    get:
      #...
      x-og-route-name: listUsers
      x-og-controller: App\Http\Controllers\UsersController@index
      x-og-skip-request: true
      x-og-middlewares: auth
      x-og-skip-resource: false
      responses:
        '200':
          description: A list of users.
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/User'
    post:
      ...
      x-og-route-name: createUser
      x-og-controller: App\Http\Controllers\UsersController@store
      x-og-skip-request: false
      x-og-skip-resource: false
      x-og-middlewares: auth,admin
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/User'
      responses:
        '201':
          description: User created successfully.
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    $ref: '#/components/schemas/User'
  /users/{userId}:
    put:
      ...
      x-og-generation: true
      x-og-route-name: updateUser
      x-og-controller: App\Http\Controllers\UsersController@update
      x-og-skip-request: false
      x-og-middlewares: auth,admin
      x-og-skip-resource: false
      parameters:
        - name: userId
          in: path
          required: true
          description: ID of the user to update.
          schema:
            type: integer
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/User'
      responses:
        '200':
          description: User updated successfully.
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/User'
components:
  schemas:
    User:
      type: object
      properties:
        id:
          type: integer
          format: int64
        username:
          type: string
          pattern: '^[a-zA-Z0-9_-]{3,16}$'
        email:
          type: string
          format: email
          maxLength: 30
      required:
        - username
        - email
```
by using package after running `php artisan openapi:generate-code` package generates for you below code.

`routes/openapi-codegen.php`
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::get('users', [UsersController::class, 'index'])->name('listUsers')->middleware(['auth']);
Route::post('users', [UsersController::class, 'store'])->name('createUser')->middleware(['auth', 'admin']);
Route::put('users/{userId}', [UsersController::class, 'update'])->name('updateUser')->middleware(['auth', 'admin']);
```
`app/Http/Controllers/UsersController.php`
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UsersResource;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;

class UsersController
{
    public function index(): JsonResponse
    {
        // return UsersResource();
    }

    public function store(StoreUsersRequest $request): JsonResponse
    {
        // return UsersResource();
    }

    public function update(int $userId, UpdateUsersRequest $request): JsonResponse
    {
        // return UsersResource();
    }

}
```
`app/Http/Requests/StoreUsersRequest.php`
```php
<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class StoreUsersRequest extends Request
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]{3,16}$/'],
            'email' => ['required', 'string', 'email', 'max:30'],
        ];
    }
}
```
`app/Http/Requests/UpdateUsersRequest.php`
```php
<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class UpdateUsersRequest extends Request
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]{3,16}$/'],
            'email' => ['required', 'string', 'email', 'max:30'],
        ];
    }
}
```
`app/Http/Resources/UsersResource.php`
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
        ];
    }
}
```
## Installation
To install the package, you can use Composer:

```
composer require --dev laravel-openapi/codegen
```

### Configuration
After installing the package, you'll need to configure it to suit your project's needs. This package provides various configuration options to customize the code generation process.

#### Publishing Configuration
```
php artisan vendor:publish --provider="LaravelOpenapi\Codegen\LaravelOpenapiCodegenProvider"
```
This command will create an `openapi-codegen.php` file in your Laravel application's `config` directory.

### Configuration Options

In the openapi-codegen.php configuration file, you'll find the following options:
* `api_docs_url`: Specifies the URL where the OpenAPI documentation will be accessible. This URL should point to the location of your OpenAPI specification file.
* `entities`: Defines the entities for which code generation will be performed. You can specify which entities should be generated by adding them to this array. Supported entities include:
  * `route`: Generate Laravel routes based on OpenAPI paths.
  * `request`: Generate request classes based on OpenAPI request bodies.
  * `resource`: Generate resource classes for API responses.
  * `controller`: Generate controllers for handling API requests.
  
    By default, all supported entities will be generated. 
    To exclude specific entities, simply remove them from this array.
* `paths`: Specifies additional paths used in the application. Currently, only the routes_file option is available, which defines the file path where the OpenAPI routes will be generated.

### Usage
#### Generate code
To generate Laravel code from an OpenAPI YAML file, you can use the following Artisan command:

```
php artisan openapi:generate-code
```

This command will generate Laravel routes, requests, resource and controllers based on the provided OpenAPI YAML file.


### Contributing
I welcome contributions from the community! If you have any ideas for improvements or find any issues, please feel free to open an issue or submit a pull request on GitHub.

### License
This package is open-source software licensed under the MIT License.

### Support
If you encounter any problems or have questions about using the package, please don't hesitate to reach out to us via email (safarumarov@gmail.com) or GitHub issues.
