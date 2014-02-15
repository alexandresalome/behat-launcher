Feature: Counter

    Scenario: I can count
        Given I restart counter
         When I add 3
          And I add 2
         Then counter should be 5

    Scenario: I can count two times
        Given I restart counter
         When I add 1
          And I add 1
         Then counter should be 2
        Given I restart counter
         When I add 3
          And I add 3
         Then counter should be 6
