<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Dependency_Injection\Compiler;

use Symfony\Component\Config\Definition\Exception\Invalid_Configuration_Exception;
use Symfony\Component\Dependency_Injection\Compiler\Compiler_Pass_Interface;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Dependency_Injection\Reference;
/**
 * This compiler pass registers the renderers in the RendererProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class Add_Extensions_Pass implements Compiler_Pass_Interface
{
    public function process(Container_Builder $container): void
    {
        if (!$container->has('knp_menu.factory')) {
            return;
        }
        $tagged_service_ids = $container->find_tagged_service_ids('knp_menu.factory_extension');
        if (0 === \count($tagged_service_ids)) {
            return;
        }
        $definition = $container->find_definition('knp_menu.factory');
        if (!\method_exists($container->get_parameter_bag()->resolve_value($definition->get_class()), 'addExtension')) {
            $msg = 'To use factory extensions, the service of class "%s" registered as knp_menu.factory must implement the "addExtension" method';
            throw new Invalid_Configuration_Exception(\sprintf($msg, $definition->get_class()));
        }
        foreach ($tagged_service_ids as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = $tag['priority'] ?? 0;
                $definition->add_method_call('addExtension', [new Reference($id), $priority]);
            }
        }
    }
}