<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Provider;

use Knp\Menu\Factory_Interface;
use Knp\Menu\Item_Interface;
use Knp\Menu\Provider\Menu_Provider_Interface;
use Symfony\Component\Dependency_Injection\Container_Aware_Interface;
use Symfony\Component\Dependency_Injection\Container_Interface;
use Symfony\Component\Http_Kernel\Kernel_Interface;
/**
 * A menu provider that allows for an AcmeBundle:Builder:mainMenu shortcut syntax.
 *
 * @author Ryan Weaver <ryan@knplabs.com>
 */
final class Builder_Alias_Provider implements Menu_Provider_Interface
{
    private array $builders = [];
    public function __construct(private readonly Kernel_Interface $kernel, private readonly Container_Interface $container, private readonly Factory_Interface $menu_factory)
    {
    }
    /**
     * Looks for a menu with the bundle:class:method format.
     *
     * For example, AcmeBundle:Builder:mainMenu would create and instantiate
     * an Acme\DemoBundle\Menu\Builder class and call the mainMenu() method
     * on it. The method is passed the menu factory.
     *
     * @param string $name The alias name of the menu
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $name, array $options = []): Item_Interface
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(\sprintf('Invalid pattern passed to AliasProvider - expected "bundle:class:method", got "%s".', $name));
        }
        [$bundle_name, $class_name, $method_name] = \explode(':', $name);
        $builder = $this->get_builder($bundle_name, $class_name);
        if (!\method_exists($builder, $method_name)) {
            throw new \InvalidArgumentException(\sprintf('Method "%s" was not found on class "%s" when rendering the "%s" menu.', $method_name, $class_name, $name));
        }
        $menu = $builder->{$method_name}($this->menu_factory, $options);
        if (!$menu instanceof Item_Interface) {
            throw new \InvalidArgumentException(\sprintf('Method "%s" did not return an ItemInterface menu object for menu "%s"', $method_name, $name));
        }
        return $menu;
    }
    /**
     * Verifies if the given name follows the bundle:class:method alias syntax.
     *
     * @param string $name The alias name of the menu
     */
    public function has(string $name, array $options = []): bool
    {
        return 2 === \substr_count($name, ':');
    }
    /**
     * Creates and returns the builder that lives in the given bundle.
     *
     * The convention is to look in the Menu namespace of the bundle for
     * this class, to instantiate it with no arguments, and to inject the
     * container if the class is ContainerAware.
     *
     * @param string $className The class name of the builder
     *
     * @throws \InvalidArgumentException If the class does not exist
     */
    private function get_builder(string $bundle_name, string $class_name): object
    {
        $name = \sprintf('%s:%s', $bundle_name, $class_name);
        if (!isset($this->builders[$name])) {
            $bundle = $this->kernel->get_bundle($bundle_name);
            $try = $bundle->get_namespace() . '\Menu\\' . $class_name;
            if (!\class_exists($try)) {
                throw new \InvalidArgumentException(\sprintf('Unable to find menu builder "%s" in bundle %s.', $try, $name));
            }
            $builder = new $try();
            if ($builder instanceof Container_Aware_Interface) {
                $builder->set_container($this->container);
            }
            $this->builders[$name] = $builder;
        }
        return $this->builders[$name];
    }
}