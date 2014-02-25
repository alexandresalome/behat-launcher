Feature: Get all sort of output files

    Scenario: I can get additional formats
        Given test project "format"
          And I am on "/"
         When I click on "xpath=//div[@data-project='TEST-format']//a[contains(., 'Create a run')]"
         When I click on "xpath=//input[@type='checkbox' and @data-path='']"
          And I click on "Create run"
         When I run all units
         Then I should see "css=h1 .label-succeeded"
          And I should see "html"
          And I should see "failed"
          And I should not see "invalidformat"

    Scenario: I can view test running live
        Given test project "slow"
          And I am on "/"
         When I click on "xpath=//div[@data-project='TEST-slow']//a[contains(., 'Create a run')]"
         When I click on "xpath=//input[@type='checkbox' and @data-path='']"
          And I click on "Create run"
         Then I should see "PENDING"
         When I start all units
          And I click on "pretty"
          And I look into output frame
         Then I should see "When step 1 is executed"
         Then I should see "When step 2 is executed"
         Then I should see "When step 3 is executed"
         Then I should see "When step 4 is executed"
         Then I should see "When step 5 is executed"
          And I look into main frame
         Then I should see "SUCCEEDED"

         # HTML format is not refresh automatically
         When I click on "html"
          And I look into output frame
         Then I should see "Feature: Global"
