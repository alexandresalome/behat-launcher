Feature: Create a run

    Scenario: I can't create an empty run
        Given test project "default"
          And I am on "/"
         When I click on "xpath=//div[@data-project='TEST-default']//a[contains(., 'Create a run')]"
         Then I should see "Create a run for project TEST-default"
         When I click on "Create run"
         Then I should see "You must select at least one feature"

    Scenario: Execute a regular run
        Given test project "default"
          And I am on "/"
         When I click on "xpath=//div[@data-project='TEST-default']//a[contains(., 'Create a run')]"
         Then I should see "Create a run for project TEST-default"
         When I click on "xpath=//input[@type='checkbox' and @data-path='']"
          And I click on "Create run"
         Then I should see "3 PENDING"
          And I should see "0 RUNNING"
          And I should see "css=h1 .label-pending"
         When I run all units
          And I refresh
         Then I should see "css=h1 .label-succeeded"
          And I should see "0 PENDING"
          And I should see "3 SUCCEEDED"

    Scenario: Execute subset of features
        Given test project "default"
          And I am on "/"
         When I click on "xpath=//div[@data-project='TEST-default']//a[contains(., 'Create a run')]"
         Then I should see "Create a run for project TEST-default"
         When I click on "xpath=//a[@class='expand' and @data-path='']"
         When I click on "xpath=//input[@type='checkbox' and @data-path='sub']"
          And I click on "Create run"
         Then I should see "2 PENDING"
          And I should see "0 RUNNING"
          And I should see "css=h1 .label-pending"
         When I run all units
          And I refresh
         Then I should see "css=h1 .label-succeeded"
          And I should see "0 PENDING"
          And I should see "2 SUCCEEDED"
