<?php

namespace LaravelOpenapi\Codegen\Tests\Generators;

use cebe\openapi\Reader;
use cebe\openapi\spec\Operation;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\DTO\RouteConfiguration;
use LaravelOpenapi\Codegen\DTO\RouteInfo;
use LaravelOpenapi\Codegen\Factories\DefaultGeneratorFactory;
use LaravelOpenapi\Codegen\Generators\RouteGenerator;
use LaravelOpenapi\Codegen\Tests\TestCase;
use LaravelOpenapi\Codegen\Utils\RouteControllerResolver;
use LaravelOpenapi\Codegen\Utils\Stub;
use PHPUnit\Framework\Attributes\Depends;

class RouteGeneratorTest extends TestCase
{
    protected RouteGenerator $routeGenerator;

    protected SpecObjectInterface $spec;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeGenerator = DefaultGeneratorFactory::createGenerator('route');

        $this->spec = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));
    }

    protected function getOperation(string $path, string $method): Operation
    {
        return $this->spec->paths[$path]->{$method};
    }

    public function test_correctly_extract_route_segments()
    {
        $singleRouteString = Stub::getStubContent('route.stub');

        [$route, $routeName, $middleware] = $this->routeGenerator->extractRouteSegments($singleRouteString);

        $this->assertSame("Route::{{ method }}('{{ uri }}', [{{ controller }}::class, '{{ action }}'])", $route);
        $this->assertSame("name('{{ routeName }}')", $routeName);
        $this->assertSame('middleware([{{ middlewares }}])', $middleware);

        return [$route, $routeName, $middleware];
    }

    public function test_can_make_route_info_object()
    {

        $operation = $this->getOperation('/users', 'get');

        /**
         * @var RouteInfo $routeInfo
         */
        $routeInfo = $this->routeGenerator->makeRouteInfo('/users', $operation, 'get');

        // make dummy object that makeRouteInfo must return
        $extractedRouteController = RouteControllerResolver::extract($operation->{'x-og-controller'});
        $routeConfiguration = RouteConfiguration::create(
            'get',
            'users',
            $operation->{'x-og-route-name'},
            $operation->{'x-og-middlewares'}
        );

        // assert objects properties are the same
        $this->assertSame(get_object_vars($extractedRouteController), get_object_vars($routeInfo->extractedRouteController));
        $this->assertSame(get_object_vars($routeConfiguration), get_object_vars($routeInfo->routeConfiguration));
    }

    #[Depends('test_correctly_extract_route_segments')]
    public function test_correctly_replace_middleware_method_in_route_stub_string(array $routeSegments)
    {
        $routeConfiguration = RouteConfiguration::create('get', 'users', 'getUsers', 'auth,admin');

        $replacedMiddlewares = $this->routeGenerator->replaceMiddlewareMethod($routeConfiguration, $routeSegments[2]);

        $this->assertSame("->middleware(['auth', 'admin'])", $replacedMiddlewares);
    }

    #[Depends('test_correctly_extract_route_segments')]
    public function test_correctly_replace_route_name_method_in_route_stub_string(array $routeSegments)
    {
        $routeConfiguration = RouteConfiguration::create('get', 'users', 'getUsers', 'auth,admin');

        $replacedRouteNameMethod = $this->routeGenerator->replaceRouteNameMethod($routeConfiguration, $routeSegments[1]);

        $this->assertSame("->name('getUsers')", $replacedRouteNameMethod);
    }

    public function test_correctly_replace_route_stub()
    {
        $routeConfiguration = RouteConfiguration::create('get', 'users', 'getUsers', 'auth,admin');
        $extractedRouteController = RouteControllerResolver::extract("App\Http\Controllers\UserController@index");

        $routeInfo = RouteInfo::create($extractedRouteController, $routeConfiguration);

        $route = $this->routeGenerator->replaceRouteStub($routeInfo);
        $expectedRoute = "Route::get('users', [UserController::class, 'index'])->name('getUsers')->middleware(['auth', 'admin'])";

        $this->assertSame($expectedRoute, $route);
    }

    public function test_can_get_namespaces_as_string()
    {
        $routeGeneratorReflected = new \ReflectionClass(RouteGenerator::class);
        $reflection = $routeGeneratorReflected->getProperty('routes');

        $routeConfiguration = RouteConfiguration::create('get', 'users', 'getUsers', 'auth,admin');
        $extractedRouteController = RouteControllerResolver::extract("App\Http\Controllers\UserController@index");

        $reflection->setValue($this->routeGenerator, [new RouteInfo($extractedRouteController, $routeConfiguration)]);
        $namespaces = $this->routeGenerator->getNamespacesAsString();

        $this->assertSame("use App\Http\Controllers\UserController;\n", $namespaces);
    }
}
