Feature: I can use it in any language

    Scenario: I can switch to french
        Given I am on "/"
         When I click on "french"
         Then I should see "Tous les runs"
         When I click on "anglais"
         Then I should see "All runs"
