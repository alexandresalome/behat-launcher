Feature: Get all sort of output files

    Scenario: I can get additional formats
        Given test project "format"
          And I am on "/"
         When I click on "xpath=//div[@data-project='TEST-format']//a[contains(., 'Create a run')]"
         When I click on "xpath=//input[@type='checkbox' and @data-path='']"
          And I click on "Create run"
         When I run all units
          And I refresh
         Then I should see "css=h1 .label-succeeded"
          And I should see "html"
          And I should see "failed"
          And I should not see "invalidformat"
