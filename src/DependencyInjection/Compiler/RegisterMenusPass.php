<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Dependency_Injection\Compiler;

use Symfony\Component\Dependency_Injection\Argument\Service_Closure_Argument;
use Symfony\Component\Dependency_Injection\Compiler\Compiler_Pass_Interface;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Reference;
/**
 * This compiler pass registers the menu builders in the LazyProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class Register_Menus_Pass implements Compiler_Pass_Interface
{
    public function process(Container_Builder $container): void
    {
        if (!$container->has_definition('knp_menu.menu_provider.lazy')) {
            return;
        }
        $menu_builders = [];
        foreach ($container->find_tagged_service_ids('knp_menu.menu_builder', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(\sprintf('The alias is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                if (empty($attributes['method'])) {
                    throw new \InvalidArgumentException(\sprintf('The method is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                $menu_builders[$attributes['alias']] = [new Service_Closure_Argument(new Reference($id)), $attributes['method']];
            }
        }
        foreach ($container->find_tagged_service_ids('knp_menu.menu', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(\sprintf('The alias is not defined in the "knp_menu.menu" tag for the service "%s"', $id));
                }
                $menu_builders[$attributes['alias']] = new Service_Closure_Argument(new Reference($id));
            }
        }
        $container->get_definition('knp_menu.menu_provider.lazy')->replace_argument(0, $menu_builders);
    }
}