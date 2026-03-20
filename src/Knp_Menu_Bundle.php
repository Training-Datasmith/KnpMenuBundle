<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle;

use Knp\Bundle\Menu_Bundle\Dependency_Injection\Compiler\Add_Extensions_Pass;
use Knp\Bundle\Menu_Bundle\Dependency_Injection\Compiler\Add_Providers_Pass;
use Knp\Bundle\Menu_Bundle\Dependency_Injection\Compiler\Register_Menus_Pass;
use Symfony\Component\Dependency_Injection\Container_Builder;
use Symfony\Component\Http_Kernel\Bundle\Bundle;
final class Knp_Menu_Bundle extends Bundle
{
    public function build(Container_Builder $container): void
    {
        parent::build($container);
        $container->add_compiler_pass(new Register_Menus_Pass());
        $container->add_compiler_pass(new Add_Extensions_Pass());
        $container->add_compiler_pass(new Add_Providers_Pass());
    }
    public function get_path(): string
    {
        return \dirname(__DIR__);
    }
}