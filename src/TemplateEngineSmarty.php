<?php

namespace TemplateEngineSmarty;

use ProcessWire\WireException;
use TemplateEngineFactory\TemplateEngineBase;

/**
 * Provides the Smarty template engine.
 */
class TemplateEngineSmarty extends TemplateEngineBase
{
    const COMPILE_DIR = 'TemplateEngineSmarty_compile/';
    const CACHE_DIR = 'TemplateEngineSmarty_cache/';

    /**
     * @var \Smarty
     */
    protected $smarty;

    /**
     * {@inheritdoc}
     */
    public function render($template, $data = [])
    {
        $template = $this->normalizeTemplate($template);
        $this->assignData($data);

        try {
            return $this->getSmarty()->fetch($template, $this->getCacheId());
        } catch (\Exception $e) {
            throw new WireException($e->getMessage());
        }
    }

    /**
     * @throws \ProcessWire\WireException
     *
     * @return \Smarty
     */
    protected function getSmarty()
    {
        if ($this->smarty === null) {
            return $this->buildSmarty();
        }

        return $this->smarty;
    }

    /**
     * @throws \ProcessWire\WireException
     *
     * @return \Smarty
     */
    protected function buildSmarty()
    {

        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir($this->getTemplatesRootPath());
        $this->smarty->setCompileDir($this->wire('config')->paths->assets . 'cache/' . self::COMPILE_DIR);
        $this->smarty->setCacheDir($this->wire('config')->paths->assets . 'cache/' . self::CACHE_DIR);

        if ($this->moduleConfig['caching'] === true) {
            $this->smarty->caching = true;
        }

        if ($this->moduleConfig['escape_html'] === true) {
            $this->smarty->escape_html = true;
        }

        if ($this->moduleConfig['compile_check'] === true) {
            $this->smarty->compile_check = true;
        }

        if ($this->moduleConfig['error_reporting'] === true) {
            $this->smarty->error_reporting = true;
        } else {
            $this->smarty->error_reporting = E_ALL ^ E_NOTICE;
        }

        $this->initSmarty($this->smarty);

        return $this->smarty;
    }

    /**
     * Hookable method called after Smarty has been initialized.
     *
     * Use this method to customize the passed $smarty instance,
     * e.g. adding functions and filters.
     *
     * @param \Smarty $smarty
     */
    protected function ___initSmarty(\Smarty $smarty)
    {
    }

    private function isDebug()
    {
        if ($this->moduleConfig['debug'] === 'config') {
            return $this->wire('config')->debug;
        }

        return (bool) $this->moduleConfig['debug'];
    }

    /**
     * @param string $key
     * @param $value
     *
     */
    public function set(string $key, $value)
    {
        $this->getSmarty()->assign($key, $value);
    }

    /**
     * @param object $data
     *
     * @throws \ProcessWire\WireException
     *
     * @return Smarty
     */
    private function assignData(array $data)
    {
        if ($this->moduleConfig['api_vars_available']) {
            foreach ($this->wire('all') as $name => $object) {
                $this->set($name, $object);
            }
        }

        if (isset($data)) {
            foreach ($data as $name => $object) {
                $this->set($name, $object);
            }
        }
    }

    /**
     * Normalize the given template by adding the template files suffix.
     *
     * @param string $template
     *
     * @return string
     */
    private function normalizeTemplate($template)
    {
        $suffix = $this->moduleConfig['template_files_suffix'];

        $normalizedTemplate = ltrim($template, DIRECTORY_SEPARATOR);

        if (!preg_match("/\.{$suffix}$/", $template)) {
            return $normalizedTemplate . sprintf('.%s', $suffix);
        }

        return $normalizedTemplate;
    }

    /**
     * Generate a unique cache-ID.
     * Cache-ID is generated based on page-id, user language, urlSegments, page numbers and template filename.
     *
     * @return string
     */
    protected function getCacheId()
    {
        $segments = array(
            $this->filename,
            $this->wire('page')->id,
            $this->wire('user')->language ? $this->wire('user')->language->name : '',
            $this->wire('input')->urlSegmentStr,
            $this->wire('input')->pageNum(),
        );

        return implode('-', $segments);
    }
}
