Feature: Usage of BehatSymfonyDependencyInjectionContainerExtension together with CrossContainerExtension
  In order to reference cross-container services and parameters inside Behat container
  As a Behat User
  I need to use BehatSymfonyDependencyInjectionContainerExtension and CrossContainerExtension together

  Scenario: Not crashing Behat
    Given a Behat configuration containing:
        """
        default:
            extensions:
                solutionDrive\BehatSymfonyDependencyInjectionContainerExtension: ~

                FriendsOfBehat\CrossContainerExtension: ~
        """
    And a feature file with passing scenario
    When I run Behat
    Then it should pass
