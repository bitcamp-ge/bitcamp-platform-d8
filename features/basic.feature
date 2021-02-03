@basic
Feature: Basic
  In order to open the website
  As a non-authenticated user
  I want to see if site works as predicted

  Scenario: User can see the front page
    Given I am not logged in
    When I go to "/"
    Then I should see the text "პროგრამირებისა და ვებ-ტექნოლოგიების სასწავლო პლათფორმა"
