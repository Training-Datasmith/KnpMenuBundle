<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Dependency_Injection;

use Knp\Menu\Attribute\As_Menu_Builder;
use Knp\Menu\Factory\Extension_Interface;
use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Voter\Voter_Interface;
use Symfony\Component\Config\File_Locator;
use Symfony\Component\Dependency_Injection\Child_Definition;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Extension\Extension;
use Symfony\Component\Dependency_Injection\Extension\Prepend_Extension_Interface;
use Symfony\Component\Dependency_Injection\Loader\Php_File_Loader;
class Knp_Menu_Extension extends Extension implements Prepend_Extension_Interface
{
    public function load(array $configs, Container_Builder $container): void
    {
        $loader = new Php_File_Loader($container, new File_Locator(__DIR__ . '/../../config'));
        $loader->load('menu.php');
        $configuration = new Configuration();
        $config = $this->process_configuration($configuration, $configs);
        foreach ($config['providers'] as $builder => $enabled) {
            if ($enabled) {
                $container->get_definition(\sprintf('knp_menu.menu_provider.%s', $builder))->add_tag('knp_menu.provider');
            }
        }
        if (isset($config['twig'])) {
            $loader->load('twig.php');
            $container->set_parameter('knp_menu.renderer.twig.template', $config['twig']['template']);
        }
        if ($config['templating']) {
            trigger_deprecation('knplabs/knp-menu-bundle', '3.3', 'Using the templating component is deprecated since version 3.3, this option will be removed in version 4.');
            $loader->load('templating.php');
        }
        $container->set_parameter('knp_menu.default_renderer', $config['default_renderer']);
        $container->register_for_autoconfiguration(Voter_Interface::class)->add_tag('knp_menu.voter');
        $container->register_for_autoconfiguration(Extension_Interface::class)->add_tag('knp_menu.factory_extension');
        $container->register_attribute_for_autoconfiguration(As_Menu_Builder::class, static function (Child_Definition $definition, As_Menu_Builder $attribute, \ReflectionMethod $reflection_method): void {
            $definition->add_tag('knp_menu.menu_builder', ['alias' => $attribute->name, 'method' => $reflection_method->get_name()]);
        });
    }
    public function get_namespace(): string
    {
        return 'http://knplabs.com/schema/dic/menu';
    }
    public function get_xsd_validation_base_path(): string
    {
        return __DIR__ . '/../Resources/config/schema';
    }
    public function prepend(Container_Builder $container): void
    {
        if (!$container->has_extension('twig')) {
            return;
        }
        $refl = new \ReflectionClass(Item_Interface::class);
        $path = \dirname($refl->get_file_name()) . '/Resources/views';
        $container->prepend_extension_config('twig', ['paths' => [$path]]);
    }
}