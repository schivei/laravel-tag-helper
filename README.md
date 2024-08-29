# Laravel Tag Helpers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schivei/laravel-tag-helper.svg?style=flat-square)](https://packagist.org/packages/schivei/laravel-tag-helper)
[![Build Status](https://github.com/schivei/laravel-tag-helper/actions/workflows/php.yml/badge.svg)](https://github.com/schivei/laravel-tag-helper/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/schivei/laravel-tag-helper/branch/master/graph/badge.svg?token=KFB5AUBUSR)](https://codecov.io/gh/schivei/laravel-tag-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/schivei/laravel-tag-helper.svg?style=flat-square)](https://packagist.org/packages/schivei/laravel-tag-helper)

This package allows you to register custom "tag helpers" in your Laravel application. These helpers can modify the HTML code.

> It is a fork of [beyondcode/laravel-tag-helper](https://github.com/beyondcode/laravel-tag-helper)

## What the difference between this package and the original one?

This package is focused on element attributes, while the original proposes to implement custom elements.
This package also has a better test coverage and a more robust codebase.

To use custom elements, try to create Blade Components instead.

## Why should I use this package?

To create shortcuts for common HTML patterns and reduce the double bracket usage in your Blade templates.

## Built-In Helpers

### CSRF Token

Instead of writing this:

```html
<form>
    <input type="hidden" name="_token" value="csrf-token">    
</form>
```

You can write this:

```html
<form csrf>
</form> 
```

### Form Method

Instead of writing this:
```html
<form method="post">
    <input type="hidden" name="_method" value="DELETE">
</form>
```

You can write this:
```html
<form method="delete">
</form>
```

### Link

Instead of writing this:

```html
<a href="{{ route('home') }}">Home</a>
```

You can write this:

```html
<a route="home">Home</a>
```

### Assets

Instead of writing this:

```html
<link href="{{ asset('asset.css') }}" .../>
<style src="{{ asset('asset.js') }}" .../>
<script src="{{ asset('asset.js') }}" .../>
```

You can write this:

```html
<link asset="asset.css" .../>
<style asset="asset.js" .../>
<script asset="asset.js" .../>
```

### Conditional Attributes

Instead of writing this:

```html
@if($condition)
    <div class="bg-red-500"></div>
@endif

@isset($condition)
    <div class="bg-red-500"></div>
@endisset

@empty($condition)
    <div class="bg-red-500"></div>
@endempty

@unless($condition)
    <div class="bg-red-500"></div>
@endunless
```

You can write this:

```html
<div class="bg-red-500" if="$condition"></div>

<div class="bg-red-500" isset="$condition"></div>

<div class="bg-red-500" empty="$condition"></div>

<div class="bg-red-500" unless="$condition"></div>
```

NOTE: `else` and `elseif` are not supported.

### Guest and Auth

Instead of writing this:

```html
@auth("web")
    <div class="bg-red-500"></div>
@endauth

@auth($what)
<div class="bg-red-500"></div>
@endauth

@guest("web")
    <div class="bg-red-500"></div>
@endguest

@guest($what)
<div class="bg-red-500"></div>
@endguest
```

You can write this:

```html
<div class="bg-red-500" auth="web"></div>

<div class="bg-red-500" :auth="$what"></div>

<div class="bg-red-500" guest="web"></div>

<div class="bg-red-500" :guest="$what"></div>
```

## Installation

You can install the package via composer:

```bash
composer require schivei/laravel-tag-helper
```

## Usage

You can create your own Tag Helper, by creating a new class and extend from the `Schivei\TagHelper\Helper` class.
Within this class you can define on which HTML elements and attributes your helper should be triggered:

```php
<?php

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class CustomTagHelper extends Helper
{
    protected $targetAttribute = 'custom';

    protected $targetElements = ['div'];

    protected function _process(HtmlElement $element)
    {
        // Manipulate the element
    }
}

```

To use and apply this tag helper, you need to register it.
Typically, you would do this in the `AppServiceProvider boot()` method or a service provider of your own.

```php
$this->app['tag-helper']->helper(CustomTagHelper::class);
```

Pay attention, the `$autoRemoveAttribute` property is set to `true` by default.
If you do not want to remove the attribute after processing, you can set it to `false`.

Since you only register the class name of the custom tag helper, you can use dependency injection inside your custom
helper class.

### Binding your helper to HTML elements and attributes

In your custom tag helper, you can use the `$targetAttribute` and `$targetElements` properties to specify which HTML
element (`div`, `form`, `a`, etc.) and attributes (`<div custom="value />`, `<form method="post">`, etc.) you want to
bind this helper to.

If you do not provide a `targetElements` on your own, this package will target to **all** elements with a specific
attribute, like this:

```php
class CustomTagHelper extends Helper
{
    protected $targetAttribute = 'my-attribute';
    
    // ...
    
}
```

This tag helper would be called for every HTML element that has a `my-attribute` attribute.

### Manipulating DOM Elements

Once your tag helper successfully matches one or multiple HTML elements, the `process` method of your tag helper will be called.

Inside of this method, you can manipulate the HTML element.

Available features:

#### Prepend / Append outer HTML

You can add HTML before or after the current element.

```php
<?php

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class CustomLink extends Helper
{
    protected $targetElement = 'my-custom-link';

    protected function _process(HtmlElement $element)
    {
        $element->prependOuterHtml('<div class="custom-link">');
        
        $element->appendOuterHtml('</div>');
    }
}
```

#### Prepend / Append / Replace inner HTML

You can add HTML inside the current element, only if the element is not self-closing only.

```php
<?php

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class CustomLink extends Helper
{
    protected $targetElement = 'my-custom-link';

    protected function _process(HtmlElement $element)
    {
        $element->prependInnerHtml('<span class="custom-link">');
        
        $element->appendInnerHtml('</span>');
        
        $element->replaceInnerHtml('<span class="custom-link">Hello</span>');
    }
}
```

#### Manipulating Attributes

You can also add, edit or delete HTML element attributes.

In this example, we are binding our helper to all link tags that have a custom `route` attribute.
We then update the `href` attribute of our link, remove the `route` attribute and add a new `title` attribute. 

```php
<?php

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class CustomLink extends Helper
{
    protected $targetAttribute = 'route';
    
    protected $targetElement = 'a';

    protected function _process(HtmlElement $element)
    {
        $element->setAttribute('href', route($element->getAttribute('route')));
        
        $element->setAttribute('title', 'This is a link.');
    }
}
```

### Passing variables to your tag helpers

You can pass attribute values to your tag helpers as you would usually pass attributes to HTML elements.
Since the modifications of your tag helpers get cached, you should always return valid Blade template output in your modified attribute values.

You can **not** directly access the variable content inside your tag helper, but only get the attribute string
representation.

For example, to get the attribute value of the `method` attribute:

```html
<form method="post"></form>
```

You can access this data, using the `getAttribute` method inside your helper:

```php
<?php

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class CustomForm extends Helper
{
    protected $targetElement = 'form';

    protected function _process(HtmlElement $element)
    {
        $formMethod = $element->getAttribute('method');
    }
}
```

If you want to write Blade output, you sometimes need to know if the user passed a variable or function call, or a string value.
To tell the difference, users can pass variable data by prefixing the attribute using a colon.

If you want to output this attribute into a blade template, you can then use the `getAttributeForBlade` method, and it
will
either give you an escaped string representation of the attribute - or the unescaped representation, in case it got prefixed by a colon.

For example:

```html
<a :route="home">Home</a>
```

```php
<?php

namespace Schivei\TagHelper\Helpers;

use Schivei\TagHelper\Helper;
use Schivei\TagHelper\Html\HtmlElement;

class CustomForm extends Helper
{
    protected $targetElement = 'a';

    protected $targetAttribute = 'route';

    protected function _process(HtmlElement $element)
    {
        $element->setAttribute('href', "{{ route(" . $element->getAttributeForBlade('route') . ") }}");
        
        $element->removeAttribute('route');
    }
}
```

This will output:

```html
<a href="{{ route('home') }}">Home</a>
```

But if you pass a dynamic parameter like this:

```html
<a :route="$routeVariable">Home</a>
```

This will output:

```html
<a href="{{ route($routeVariable) }}">Home</a>
```

This way you do not need to manually care about escaping and detecting dynamic variables.

### Testing

``` bash
composer test
```

### Changelog

Please see [Releases](https://github.com/schivei/laravel-tag-helper/releases) for more information what has changed
recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email costa@elton.schivei.nom.br instead of using the issue tracker.

## Credits

- [Elton Schivei Costa](https://github.com/schivei)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
