<?php

declare (strict_types=1);
namespace Knp\Bundle\Menu_Bundle\Templating\Helper;

use Knp\Menu\Item_Interface;
use Knp\Menu\Matcher\Matcher_Interface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Util\Menu_Manipulator;
use Symfony\Component\Templating\Helper\Helper as TemplatingHelper;
class Menu_Helper extends Templating_Helper
{
    public function __construct(private readonly Helper $helper, private readonly Matcher_Interface $matcher, private readonly Menu_Manipulator $menu_manipulator)
    {
    }
    /**
     * Retrieves an item following a path in the tree.
     *
     * @param ItemInterface|string $menu
     *
     * @return ItemInterface
     */
    public function get($menu, array $path = [], array $options = [])
    {
        return $this->helper->get($menu, $path, $options);
    }
    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface|string|array $menu
     * @param string                     $renderer
     *
     * @return string
     */
    public function render($menu, array $options = [], $renderer = null)
    {
        return $this->helper->render($menu, $options, $renderer);
    }
    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param ItemInterface|array|string $menu
     * @param string|array|null          $subItem
     *
     * @return array
     */
    public function get_breadcrumbs_array($menu, $sub_item = null)
    {
        return $this->helper->get_breadcrumbs_array($menu, $sub_item);
    }
    /**
     * A string representation of this menu item.
     *
     * e.g. Top Level 1 > Second Level > This menu
     *
     * @param string $separator
     *
     * @return string
     */
    public function get_path_as_string(Item_Interface $menu, $separator = ' > ')
    {
        return $this->menu_manipulator->get_path_as_string($menu, $separator);
    }
    /**
     * Checks whether an item is current.
     *
     * @return bool
     */
    public function is_current(Item_Interface $item)
    {
        return $this->matcher->is_current($item);
    }
    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param int $depth The max depth to look for the item
     *
     * @return bool
     */
    public function is_ancestor(Item_Interface $item, $depth = null)
    {
        return $this->matcher->is_ancestor($item, $depth);
    }
    /**
     * Returns the current item of a menu.
     *
     * @param ItemInterface|array|string $menu
     *
     * @return ItemInterface|null
     */
    public function get_current_item($menu)
    {
        return $this->helper->get_current_item($menu);
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return 'knp_menu';
    }
}