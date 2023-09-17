<?php

namespace matpoppl\SmallMVC\View\Helper;

use matpoppl\SmallMVC\Utils\HTMLAttributes;
use Psr\Container\ContainerInterface;

class AssetsHelper extends AbstractHelper
{
    private $options = [];
    
    private $include = [];
    
    public function __construct(array $options)
    {
        $this->options = $options;
    }
   
    public function attachLibrary($id)
    {
        if (! array_key_exists($id, $this->options['libraries'])) {
            throw new \DomainException('Library `'.$id.'` not found');
        }
        
        $library = $this->options['libraries'][$id];
        
        if (array_key_exists('dependencies', $library)) {
            foreach ($library['dependencies'] as $dependency) {
                $this->attachLibrary($dependency);
            }
        }
        
        $this->include[$id] = true;
        
        return $this;
    }
    
    public function renderHead()
    {
        $ret = '';
        
        foreach (array_intersect_key($this->options['libraries'], $this->include) as $library) {

            if (array_key_exists('css', $library)) {
                foreach ($library['css'] as $path => $opts) {
                    
                    $baseUrl = empty($opts['remote']) ? '//pop-pc.lan:8080/static/css/' : '';
                    
                    $attrs = HTMLAttributes::create([
                        'rel' => 'stylesheet',
                        // @TODO pathManager
                        'href' => $baseUrl . $path,
                    ] + ($opts['attributes'] ?? []));
                    
                    $ret .= '<link'.$attrs->render().'/>';
                }
            }
        }

        return $ret;
    }
    
    public function renderFoot()
    {
        $ret = '';
        
        foreach (array_intersect_key($this->options['libraries'], $this->include) as $library) {
            if (array_key_exists('js', $library)) {
                foreach ($library['js'] as $path => $opts) {
                    $baseUrl = empty($opts['remote']) ? '//pop-pc.lan:8080/static/js/' : '';
                    
                    $attrs = HTMLAttributes::create([
                        'defer' => true,
                        'src' => $baseUrl . $path,
                    ] + ($opts['attributes'] ?? []));
                    
                    $ret .= '<script'.$attrs->render().'></script>';
                }
            }
        }
        
        return $ret;
    }
    
    public static function create(ContainerInterface $container, ...$args)
    {
        $config = $container->get('config');
        
        return new static($config['view_assets'] ?? [], ...$args);
    }
}
