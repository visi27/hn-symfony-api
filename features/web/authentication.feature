Feature: Authentication
  In order to gain access to the site management area
  As an admin
  I need to be able to login and logout

  Scenario: Logging In
    Given there is an admin user "evis" with password "admin"
    And twoFA is active
    And twoFA method is "email" and twoFA code is "1234"
    And I am on "/"
    When I follow "Login"
    And I fill in "Username" with "evis@foo.com"
    And I fill in "Password" with "admin"
    And I press "Login"
    Then I should see "Logout"
    And I should see "Authenticate!"
    When I fill in "_auth_code" with auth value from db
    And I press "Login"
    Then I should see "Logout"
    And I should not see "Authenticate!"
    And I should be on "/"