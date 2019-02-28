# TemplateEngineSmarty

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![ProcessWire 3](https://img.shields.io/badge/ProcessWire-3.x-orange.svg)](https://github.com/processwire/processwire)

A ProcessWire module adding Smarty to the [TemplateEngineFactory](https://github.com/wanze/TemplateEngineFactory).

## Requirements

* ProcessWire `3.0` or newer
* TemplateEngineFactory `2.0` or newer
* PHP `7.0` or newer
* Composer

> The `1.x` version of this module is available on the [1.x branch](https://github.com/blue-tomato/TemplateEngineSmarty/tree/1.x).
Use this version if you still use _TemplateEngineFactory_ `1.x`.  

## Installation

Execute the following command in the root directory of your ProcessWire installation:

```
composer require blue-tomato/template-engine-smarty:^2.0
```

This will install the _TemplateEngineSmarty_ and _TemplateEngineFactory_ modules in one step. Afterwards, don't forget
to enable Smarty as engine in the _TemplateEngineFactory_ module's configuration.

> ℹ️ This module includes test dependencies. If you are installing on production with `composer install`, make sure to
pass the `--no-dev` flag to omit autoloading any unnecessary test dependencies!.

## Configuration

The module offers the following configuration:

* **`Template files suffix`** The suffix of the Smarty template files, defaults to `tpl`.
* **`Provide ProcessWire API variables in Smarty templates`** API variables (`$pages`, `$input`, `$config`...)
are accessible in Smarty,
e.g. `{{ config }}` for the config API variable.
* **`Debug`** If enabled, Smarty outputs debug information.
* **`Compile Check`** If enabled, templates are recompiled whenever the source code changes.
* **`Error Reporting`** If set to `false`, Smarty will silently ignore invalid variables (variables and
or attributes/methods that do not exist) and replace them with a `null` value. When set to `true`,
Smarty throws an exception instead
* **`Escape HTML`** If enabled, templates will auto-escape variables. If you are using ProcessWire
textformatters to escape field values, do not enable this feature.

## Extending Smarty

It is possible to extend Smarty after it has been initialized by the module. Hook the method `TemplateEngineSmarty::initSmarty`
to register custom functions, extensions, global variables, filters etc.

Here is an example how you can use the provided hook to attach a custom function.

```php
function foo_function($params, $smarty) {
  return 'bar';
};

wire()->addHookAfter('TemplateEngineSmarty::initSmarty', function (HookEvent $event) {
    /** @var \Smarty $smarty */
    $smarty = $event->arguments('smarty');

    $smarty->registerPlugin("function", "foo", "foo_function");
});

// ... and then use it anywhere in a Smarty template:

{foo}
```

> The above hook can be put in your `site/init.php` file. If you prefer to use modules, put it into the module's `init()`
method and make sure that the module is auto loaded.
