<?php

namespace ProcessWire;

use TemplateEngineSmarty\TemplateEngineSmarty as SmartyEngine;

/**
 * Adds Smarty templates to the TemplateEngineFactory module.
 */
class TemplateEngineSmarty extends WireData implements Module, ConfigurableModule
{
    /**
     * @var array
     */
    private static $defaultConfig = [
        'template_files_suffix' => 'tpl',
        'api_vars_available' => 1,
        'caching' => 0,
        'compile_check' => 1,
        'escape_html' => 0,
        'error_reporting' => 0,
        'debug' => 'config',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->wire('classLoader')->addNamespace('TemplateEngineSmarty', __DIR__ . '/src');
        $this->setDefaultConfig();
    }

    /**
     * @return array
     */
    public static function getModuleInfo()
    {
        return [
            'title' => 'Template Engine Smarty',
            'summary' => 'Smarty templates for the TemplateEngineFactory',
            'version' => 220,
            'author' => 'Blue Tomato',
            'href' => 'https://processwire.com/talk/topic/6834-module-smarty-for-the-templateenginefactory/',
            'singular' => true,
            'autoload' => true,
            'requires' => [
                'TemplateEngineFactory>=2.0.0',
                'PHP>=7.0',
                'ProcessWire>=3.0',
            ],
        ];
    }

    public function init()
    {
        /** @var \ProcessWire\TemplateEngineFactory $factory */
        $factory = $this->wire('modules')->get('TemplateEngineFactory');

        $factory->registerEngine('Smarty', new SmartyEngine($factory->getArray(), $this->getArray()));
    }

    private function setDefaultConfig()
    {
        foreach (self::$defaultConfig as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param array $data
     *
     * @throws \ProcessWire\WireException
     * @throws \ProcessWire\WirePermissionException
     *
     * @return \ProcessWire\InputfieldWrapper
     */
    public static function getModuleConfigInputfields(array $data)
    {
        /** @var Modules $modules */
        $data = array_merge(self::$defaultConfig, $data);
        $wrapper = new InputfieldWrapper();
        $modules = wire('modules');

        /** @var \ProcessWire\InputfieldText $field */
        $field = $modules->get('InputfieldText');
        $field->label = __('Template files suffix');
        $field->name = 'template_files_suffix';
        $field->value = $data['template_files_suffix'];
        $field->required = 1;
        $wrapper->append($field);

        $field = $modules->get('InputfieldCheckbox');
        $field->label = __('Provide ProcessWire API variables in Smarty templates');
        $field->description = __('API variables (`$pages`, `$input`, `$config`...) are accessible in Smarty, e.g. `{$config}` for the config API variable.');
        $field->name = 'api_vars_available';
        $field->checked = (bool) $data['api_vars_available'];
        $wrapper->append($field);

        /** @var \ProcessWire\InputfieldSelect $field */
        $field = $modules->get('InputfieldSelect');
        $field->label = __('Debug');
        $field->name = 'debug';
        $field->addOptions([
            'config' => __('Inherit from ProcessWire'),
            0 => __('No'),
            1 => __('Yes'),
        ]);
        $field->value = $data['debug'];
        $wrapper->append($field);

        /** @var \ProcessWire\InputfieldCheckbox $field */
        $field = $modules->get('InputfieldCheckbox');
        $field->label = __('Enable Smarty\'s template chaching');
        $field->description = __('If enabled, every rendered result is cached for 1 hour');
        $field->name = 'caching';
        $field->checked = (bool) $data['caching'];
        $wrapper->append($field);

        $field = $modules->get('InputfieldCheckbox');
        $field->label = __('Enable Smarty\'s compile check for templates');
        $field->description = __('If enabled, every template file involved with the cache is checked for modification. If modified, the cache is regenerated.');
        $field->name = 'compile_check';
        $field->checked = (bool) $data['compile_check'];
        $wrapper->append($field);

        $field = $modules->get('InputfieldCheckbox');
        $field->label = __('Error Reporting');
        $field->description = __('If set to `false`, Smarty will silently ignore invalid variables (variables and or attributes/methods that do not exist) and replace them with a `null` value. When set to `true`, Smarty throws an exception instead');
        $field->name = 'error_reporting';
        $field->checked = (bool) $data['error_reporting'];
        $wrapper->append($field);

        $field = $modules->get('InputfieldCheckbox');
        $field->label = __('Auto escape variables');
        $field->description = __('If enabled, templates will auto-escape variables. If you are using ProcessWire textformatters to escape field values, do not enable this feature.');
        $field->name = 'escape_html';
        $field->checked = (bool) $data['escape_html'];
        $wrapper->append($field);

        return $wrapper;
    }
}
