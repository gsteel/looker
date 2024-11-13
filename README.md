# "Looker" - Another PHP HTML Templating Library

[![Continuous Integration](https://github.com/gsteel/looker/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/gsteel/looker/actions/workflows/continuous-integration.yml)
[![Type Coverage](https://shepherd.dev/github/gsteel/looker/coverage.svg)](https://shepherd.dev/github/gsteel/looker)

This library is modelled after [laminas-view](https://github.com/laminas/laminas-view/) but has _less_ functionality 🙃

It doesn't have any of the legacy associated with the MVC framework, and has a plugin manager that keeps track of stateful plugins so that they can easily be reset for each rendering cycle.

State in plugins is often unavoidable, but it's a pain in the ass in long-running processes such as Swoole and Roadrunner etc. This library attempts to make dealing with that state safer, easier and more obvious.

If you are familiar with `laminas-view`, then a lot of what's here should be familiar too.

'Looker' is in active development and won't be preserving BC until a 1.0 release.

## Configuration

The primary target for this lib is a [Mezzio](https://github.com/mezzio/mezzio) application, that said, it doesn't come with any useful bindings for Laminas/Mezzio apps. For that, have a look at [gsteel/looker-mezzio](https://github.com/gsteel/looker-mezzio/).

With that out of the way, basic configuration can be found in the [Config Provider](./src/ConfigProvider.php) and you'll most likely be interested in setting up template files:

### Configuring Templates

As per `laminas-view`, we have "template resolvers". 3 of them:

- `MapResolver`
- `DirectoryResolver`
- `AggregateResolver`

#### Map Resolver

The map resolver requires one-to-one mapping of template name to on-disk file:

```php
return [
    'looker' => [
        'templates' => [
            'map' => [
                'some::template' => __DIR__ . '/some/path/to/template.phtml',
            ],
        ],
    ],
];
```

Given the above configuration, you would render the template with:

```php
use Looker\Model\Model;
use Looker\Renderer\Renderer;

$model = Model::new('some::template', ['variable' => 'value']);
/** @var Renderer */
$renderer->render($model);
```

#### Directory Resolver

The directory resolver iterates over the configured directories in order and attempts to find the template name provided, if necessary, appending the `defaultSuffix` when no file name extension is provided.

```php
return [
    'looker' => [
        'templates' => [
            'paths' => [
                __DIR__ . '/some-directory',
                __DIR__ . '/other-directory',
            ],
            'defaultSuffix' => 'phtml',
        ],
    ],
];
```

Given the above configuration, the resolver would yield:

```php
$resolver->resolve('some/template'); // __DIR__ . '/some-directory/some/template.phtml'
$resolver->resolve('something.txt'); // __DIR__ . '/some-directory/something.txt'
```

#### Aggregate Resolver

The aggregate resolver composes multiple resolvers and iterates over each in order until it finds the first resolver that can find the given template name.

Each configured resolver must be a `\Looker\Template\Resolver` instance that can be retrieved from the DI container in use.

```php
return [
    'looker' => [
        'templates' => [
            'aggregate' => [
                \Looker\Template\MapResolver::class,
                \Looker\Template\DirectoryResolver::class,
            ],
        ],
    ],
];
```

With the above configuration, first the Map resolver would be consulted and then the directory resolver.

### Documentation

Is lacking - at some point I'll get around to improving it… maybe.

### Contributing

Once you cloned the project and made your changes in a branch, please run `make qa` to ensure that all QA tools run successfully before opening a PR 👍
