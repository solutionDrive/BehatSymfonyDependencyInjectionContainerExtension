# BehatSymfonyDependencyInjectionContainerExtension

Extension to load additional dependency injection config files for behat

Inspired by [FriendsOfBehat/ServiceContainerExtension](https://github.com/FriendsOfBehat/ServiceContainerExtension)

## Usage

1. Install it:

    ```bash
    $ composer require solutiondrive/behat-symfony-dependency-injection-container-extension --dev
    ```

2. Enable this extension and configure Behat to use it:

    ```yaml
    # behat.yml
    default:
        # ...
        extensions:
            solutionDrive\BehatSymfonyDependencyInjectionContainerExtension:
                identifiers:
                    solutionDrive:
                        imports:
                            - "config/solutionDrive/services.xml"
                            - "config/solutionDrive/services.yml"
                            - "config/solutionDrive/services.php"
    ```
    
3. Write services file definitions:

    ```xml
    <!-- config/solutionDrive/services.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <container xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://symfony.com/schema/dic/services">
        <services>
            <service id="acme.my_service" class="Acme\MyService" />
        </services>
    </container>
    ```
    
    ```yaml
    # config/solutionDrive/services.yml
    services:
        acme.my_service:
            class: Acme\MyService
    ```
    
    ```php
    // config/solutionDrive/services.php
    use Symfony\Component\DependencyInjection\Definition;
    
    $container->setDefinition('acme.my_service', new Definition(\Acme\MyService::class));
    ```
