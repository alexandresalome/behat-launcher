Feature: Project list

    Scenario: I can see empty lists
        Given test project "default"
          And I am on "/"
         Then I should see "No run found."

    Scenario: I can see pending runs
        Given test project "default"
          And following run for "TEST-default":
              | feature         | created_at |
              | counter.feature | PT5M       |
         When I am on "/"
         Then I should see "PENDING"

    Scenario: I can see running runs
        Given test project "default"
          And following run for "TEST-default":
              | feature         | created_at | started_at |
              | counter.feature | PT5M       | PT0M       |
         When I am on "/"
         Then I should see "RUNNING"

    Scenario: I can see succeeded runs
        Given test project "default"
          And following run for "TEST-default":
              | feature         | created_at | finished_at | return_code |
              | counter.feature | PT5M       | PT0M        | 0           |
         When I am on "/"
         Then I should see "SUCCEEDED"

    Scenario: I can see failed runs
        Given test project "default"
          And following run for "TEST-default":
              | feature         | created_at | finished_at | return_code |
              | counter.feature | PT5M       | PT0M        | 1           |
         When I am on "/"
         Then I should see "FAILED"
