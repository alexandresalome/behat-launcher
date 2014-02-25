Feature: I can manage my runs

    Scenario: I can stop a run
        Given test project "default"
          And following run for "TEST-default":
              | feature     | created_at | finished_at | return_code |
              | bar.feature | @null      | @null       | @null       |
         When I am on "/"
          And I click on "#"
         Then I should see "1 PENDING"
         When I click on "Stop"
         Then I should see "1 FAILED"

    Scenario: I can restart all units
        Given test project "default"
          And following run for "TEST-default":
              | feature     | created_at | finished_at | return_code |
              | foo.feature | PT5M       | PT0M        | 0           |
              | bar.feature | PT5M       | PT0M        | 1           |
         When I am on "/"
          And I click on "#"
         Then I should see "1 FAILED"
         Then I should see "1 SUCCEEDED"
         When I click on "Restart all"
         Then I should see "2 PENDING"

    Scenario: I can restart failed units
        Given test project "default"
          And following run for "TEST-default":
              | feature     | created_at | finished_at | return_code |
              | foo.feature | PT5M       | PT0M        | 0           |
              | bar.feature | PT5M       | PT0M        | 1           |
         When I am on "/"
          And I click on "#"
         Then I should see "1 FAILED"
         Then I should see "1 SUCCEEDED"
         When I click on "Restart failed"
         Then I should see "1 PENDING"
         Then I should see "1 SUCCEEDED"

