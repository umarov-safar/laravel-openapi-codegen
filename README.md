# Laravel OpenAPI Code Generation

### Introduction
This package provides a convenient way to generate Laravel routes, requests, and controllers based on OpenAPI specifications in YAML format.


# Installation
To install the package, you can use Composer:

`composer require --dev laravel-openapi/codegen`

### Configuration
After installing the package, you need to publish the configuration file:

```php artisan vendor:publish --provider="LaravelOpenapi\Codegen\LaravelOpenapiCodegenProvider"```

This will create an `openapi-codegen.php` file in your Laravel application's `config` directory.

### Usage
#### Generate code
To generate Laravel code from an OpenAPI YAML file, you can use the following Artisan command:

```php artisan openapi:generate-code```

This command will generate Laravel routes, requests, routes and controllers based on the provided OpenAPI YAML file.


### Configuration Options
In the `openapi-codegen.php` configuration file, `you must define api_docs_url=to_your_yaml_file.yaml`.

### Contributing
We welcome contributions from the community! If you have any ideas for improvements or find any issues, please feel free to open an issue or submit a pull request on GitHub.

### License
This package is open-source software licensed under the MIT License.

### Support
If you encounter any problems or have questions about using the package, please don't hesitate to reach out to us via email (safarumarov@gmail.com) or GitHub issues.