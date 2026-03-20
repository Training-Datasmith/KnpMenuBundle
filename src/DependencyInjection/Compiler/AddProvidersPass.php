<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Dependency_Injection\Compiler;

use Symfony\Component\Dependency_Injection\Compiler\Compiler_Pass_Interface;
use Symfony\Component\Dependency_Injection\Container_Builder;
/**
 * This compiler pass registers the ChainProvider or the only provider as `knp_menu.menu_provider`.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class Add_Providers_Pass implements Compiler_Pass_Interface
{
    public function process(Container_Builder $container): void
    {
        $providers = [];
        foreach ($container->find_tagged_service_ids('knp_menu.provider') as $id => $tags) {
            $providers[] = $id;
        }
        if (1 === \count($providers)) {
            // Use an alias instead of wrapping it in the ChainProvider for performances
            // when using only one (the default case as the bundle defines one provider)
            $container->set_alias('knp_menu.menu_provider', $providers[0]);
        } else {
            $container->set_alias('knp_menu.menu_provider', 'knp_menu.menu_provider.chain');
        }
    }
}