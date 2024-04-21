# Laravel OpenAPI Code Generation

## Introduction
This package provides a convenient way to generate Laravel routes, requests, and controllers based on OpenAPI specifications in YAML format.


### Motivation
```yaml

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
