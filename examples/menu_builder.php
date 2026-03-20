<?php

declare(strict_types=1);

/**
 * Example: Register a menu builder with KnpMenuBundle via the #[AsMenuBuilder] attribute.
 *
 * services.yaml:
 *   App\Menu\MainMenuBuilder:
 *     tags: [{ name: knp_menu.menu_builder, method: createMainMenu, alias: main }]
 *     # Or simply use the #[AsMenuBuilder] attribute (shown below).
 *
 * In Twig: {{ knp_menu_render('main') }}
 */

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class MainMenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Builds the main navigation menu.
     *
     * @param array<string, mixed> $options Optional menu options passed from the template.
     *
     * @return ItemInterface The root menu item.
     */
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Home', ['route' => 'app_home'])
             ->setAttribute('class', 'nav-item');

        $menu->addChild('Products', ['route' => 'app_product_index'])
             ->setAttribute('class', 'nav-item');

        $menu->addChild('About', ['route' => 'app_about'])
             ->setAttribute('class', 'nav-item');

        $menu->addChild('Contact', ['route' => 'app_contact'])
             ->setAttribute('class', 'nav-item');

        return $menu;
    }
}
