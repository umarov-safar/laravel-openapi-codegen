## OpenApi Server generator

### Route generator 
Now can generate route with:
- [x] Route name
- [x] Middlewares
- [ ] Regular Expression Constraints

### settings
###### x-og-controller: App\Http\Controllers\UserController@get

Controller action for which will be generate route

```php
Route::get([UserController::class, 'get']);
```



###### x-og-route-name: getUser

Route name optional if name was set then in route will be added.

```php
Route::get([UserController::class, 'get'])->name('getUser');
```

###### x-og-middlewares: auth,admin

Middleware optional if it was set then middlewares will be add

```php
Route::get([UserController::class, 'get'])->name('getUser')
    ->middleware(['auth', 'amin']);;
```



### Request generator

For all controller method automatically will be generate Request except `Request GET` method.

But we are free to skip generation with flag

##### x-og-skip-request: true

if this flag is set to true then class request not will be gSenerate.

If you want also create request for your get method than set `x-og-skip-request: false` if it was set false it create request even for request method