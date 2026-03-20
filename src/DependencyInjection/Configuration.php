<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Dependency_Injection;

use Symfony\Component\Config\Definition\Builder\Tree_Builder;
use Symfony\Component\Config\Definition\Configuration_Interface;
/**
 * This class contains the configuration information for the bundle.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Configuration implements Configuration_Interface
{
    /**
     * Generates the configuration tree.
     */
    public function get_config_tree_builder(): Tree_Builder
    {
        $tree_builder = new Tree_Builder('knp_menu');
        $root_node = $tree_builder->get_root_node();
        $root_node->children()->array_node('providers')->add_defaults_if_not_set()->children()->boolean_node('builder_alias')->default_true()->end()->end()->end()->array_node('twig')->add_defaults_if_not_set()->can_be_unset()->children()->scalar_node('template')->default_value('@KnpMenu/menu.html.twig')->end()->end()->end()->boolean_node('templating')->default_false()->end()->scalar_node('default_renderer')->cannot_be_empty()->default_value('twig')->end()->end();
        return $tree_builder;
    }
}