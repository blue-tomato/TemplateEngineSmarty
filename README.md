TemplateEngineSmarty
====================
ProcessWire module adding Smarty templates to the TemplateEngineFactory.

## Installation
Install the module just like any other ProcessWire module. Check out the following guide: http://modules.processwire.com/install-uninstall/

This module requires TemplateEngineFactory: https://github.com/wanze/TemplateEngineFactory

After installing, don't forget to enable Smarty as engine in the TemplateEngineFactory module's settings.

## Configuration
* **Path to templates** Path to folder where you want to store your Smarty template files.
* **Template files suffix** The suffix of the template files, default is *tpl*.
* **Smarty caching lifetime** Caching time in seconds for smarty templates, enter 0 to deactivate cache.
* **Import ProcessWire API variables in Smarty template** If checked, any API variable is accessible inside the Smarty templates, for example *{$page}* refers to the current page.
* **Enable Smarty's compile check for template** If activated, the module checks if template files were modified. If this is the case, any compiled templates or cache files are cleared before rendering the template.

## Best practices
Smarty offers a nice feature called *Template inheritance*, see: http://www.smarty.net/inheritance
Here is an example how you could use this feature in combination with the TemplateEngineFactory and ProcessWire:

### Main template file

First of all, a global template file is created containing the main markup of the site. Other template files will extend from this file and overwrite the provided blocks, if necessary.

```html
<!--Global template file: /site/templates/smarty/template.tpl-->
<html>
<head>
  <title>{$browser_title}</title>  
  <link rel="stylesheet" type="text/css" href="{$config->urls->templates}styles/main.css">
  
  {block name="head"}{/block}

</head>
<body>
<nav>
<ul>
{foreach $nav_items as $p}
  <li{if $p->id == $page->id} class="active"{/if}><a href="{$p->url}">{$p->title}</a></li>
{/foreach}
</ul>
</nav>
<div id="content">

  {block name="content"}
  <h1>{$page->title}</h1>
  {$page->body}
  {/block}

</div>
<script type="javascript" src="{$config->urls->templates}js/main.js"></script>

{block name="javascript"}{/block}

</body>
</html>
```
Notice that there are three blocks defined. Blocks *head* and *javascript* are empty and can be used by derived templates to add specific CSS or javascript stuff. Furthermore the *content* block outputs by default the page's title and body, but another template may want to display other informations there and thus overwrite the block.

### Using a global controller

There exist some variables in our global template that should always be passed to the template, namely *{$browser_title}* and *{$nav_items}*. The easiest way to achieve this is by enabling  *$config->prependTemplateFile* in */site/config.php*. If enabled, we now have a controller file that is always prepended to the more "normal" controllers.

```php
// In controller file _init.php, global logic

$browser_title = $page->title;
if ($page->template == 'home') {
  $browser_title = 'Home: ' . $page->title;
}
// Pass title to template
$view->set('browser_title, $browser_title);

// Collect navigation pages
$view->set('nav_items', $pages->get("/")->children("name!=foo"));
```

### Example of controller and derived template file
Assume that the *products* template wants to display a list of products in the content block, not the title and body. Here's an example how the template file and controller could look like if a product-page is served by ProcessWire:
```html
<!-- In file: /site/templates/smarty/products.tpl -->

{extends file="template.tpl"}

{block name="content"}
  <h1>Check out the following products</h1>
  <ul>
  {foreach $products as $p}
    <li>{$p->title}</li>
  {/foreach}
  </ul>
{/block}

{block name="javascript"}
<script type="javascript" src="{$config->urls->templates}js/products.js"></script>
{/block}

```
The template file overwrites the content block and displays some products. Also an additional javascript file is loaded before the closing body tag. All the other markup is provided by the global template file! What's left is to pass the products from the corresponding controller to the template:
```php
// In file: /site/templates/products.php

$products = $pages->find('template=product,active=1');
$view->set('products', $products);
```
This is it :)
