# KnpMenuBundle Architecture

## Purpose

A Symfony bundle that integrates the KnpMenu library into Symfony applications,
providing DI container wiring, Twig functions, and a builder-alias provider for
menu registration.

## Directory Structure

```
src/
  Knp_Menu_Bundle.php                        — bundle entry point; registers compiler passes
  DependencyInjection/
    Knp_Menu_Extension.php                   — loads configuration and registers services
    Configuration.php                        — defines the `knp_menu` config tree
    Compiler/
      Add_Extensions_Pass.php                — wires tagged factory extensions into MenuFactory
      Add_Providers_Pass.php                 — wires tagged menu providers into ChainProvider
      Register_Menus_Pass.php                — registers #[AsMenuBuilder]-tagged services
  Provider/
    Builder_Alias_Provider.php               — resolves menus by calling builder methods
  Templating/Helper/
    Menu_Helper.php                          — PHP templating helper (non-Twig environments)
config/
  menu.php                                   — service definitions for factory and providers
  twig.php                                   — Twig extension / runtime service definitions
  templating.php                             — templating helper service definitions
tests/
  DependencyInjection/
    Compiler/  …
  Provider/    …
  Templating/  …
  Stubs/       — test kernel and menu builder stubs
```

## Key Design Decisions

- **Tag-based wiring**: menu providers and factory extensions are discovered via
  Symfony service tags (`knp_menu.menu_builder`, `knp_menu.provider`,
  `knp_menu.factory_extension`), so third-party bundles can contribute menus
  without modifying bundle code.
- **`#[AsMenuBuilder]` attribute**: PHP 8 attribute support for registering
  menu builder classes automatically during container compilation.
- **Lazy providers**: the bundle configures a `Lazy_Provider` wrapping a
  `Chain_Provider` so menus are only built on first access.

## Extension Points

- Tag a service with `knp_menu.menu_builder` (or use `#[AsMenuBuilder]`) to
  expose a menu to the `Builder_Alias_Provider`.
- Tag a service with `knp_menu.factory_extension` to extend how menu options
  are processed.
- Tag a service with `knp_menu.voter` to add custom "current item" detection.

## Dependency Flow

```
Symfony DI container
  └── KnpMenuExtension (load config + compiler passes)
        └── MenuFactory  + ExtensionInterface\*
              └── ChainProvider → Builder_Alias_Provider + other providers
                    └── MenuItem tree → Renderer (Twig / List)
```
