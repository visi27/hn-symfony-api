<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';
/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * @var User
     */
    private $currentUser;

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }

    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param $categoryName
     *
     * @return Category
     */
    private function createCategory($categoryName)
    {
        $category = new Category();
        $category->setName($categoryName);

        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();

        return $category;
    }

    private function createBlogPosts($count, User $author = null)
    {
        $category = $this->createCategory('Behat Is Awsome');

        if (!$author) {
            $author = $this->currentUser;
        }

        for ($i = 0; $i < $count; ++$i) {
            $blogPost = new BlogPost();
            $blogPost->setTitle('Article  '.$i);
            $blogPost->setSummary('Lorem Ipsum');
            $blogPost->setContent('<b>Lorem Ipsum Dolor Sit Amet</b>');
            $blogPost->setIsPublished(true);
            $blogPost->setCategory($category);
            $blogPost->setPublishedAt(new \DateTime('-1 month'));
            $blogPost->setUser($author);

            $this->getEntityManager()->persist($blogPost);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        $purger = new ORMPurger($this->getContainer()->get('doctrine')->getManager());
        $purger->purge();
    }

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures()
    {
        $loader = new ContainerAwareLoader($this->getContainer());
        $loader->loadFromDirectory(__DIR__.'/../../src/AppBundle/DataFixtures');

        $executor = new ORMExecutor($this->getEntityManager());
        $executor->execute($loader->getFixtures(), true);
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->thereIsAnAdminUserWithPassword('admin', 'admin');

        $this->visitPath('/login');

        $this->getPage()->fillField('Username', 'admin@foo.com');
        $this->getPage()->fillField('Password', 'admin');
        $this->getPage()->pressButton('Login');
    }

    /**
     * @Given there is/are :count article(s)
     */
    public function thereAreArticles($count)
    {
        $this->createBlogPosts($count);
    }

    /**
     * @Given there is a category named :categoryName
     */
    public function thereIsACategoryNamed($categoryName)
    {
        $this->createCategory($categoryName);
    }

    /**
     * @When I click :linkName
     */
    public function iClick($linkName)
    {
        $this->getPage()->clickLink($linkName);
    }

    /**
     * @Then I should see :count articles
     */
    public function iShouldSeeArticles($count)
    {
        $table = $this->getPage()->find('css', 'table.table');
        assertNotNull($table, 'Cannot find a table!');
        assertCount((int) $count, $table->findAll('css', 'tbody tr'));
    }

    /**
     * @Given there is an admin user :username with password :password
     */
    public function thereIsAnAdminUserWithPassword($username, $password)
    {
        $user = new \AppBundle\Entity\User();
        $user->setEmail($username.'@foo.com');
        $user->setPlainPassword($password);

        $user->setRoles(['ROLE_ADMIN']);

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush();

        $this->currentUser =  $user;
    }

    /**
     * @Given twoFA is active
     */
    public function twoFAIsActive()
    {
        $this->currentUser->setTwoFactorAuthentication(true);

        $em = $this->getEntityManager();

        $em->persist($this->currentUser);
        $em->flush();
    }

    /**
     * @Given twoFA method is :method and twoFA code is :code
     */
    public function twoFAMethodIsAndtwoFACodeIs($method, $code)
    {
        $this->currentUser->setDefaultTwoFactorMethod($method);
        $this->currentUser->setTwoFactorCode($code);

        $em = $this->getEntityManager();

        $em->persist($this->currentUser);
        $em->flush();
    }

    /**
     * @When I fill in :field with auth value from db
     */
    public function iFillInWithAuthValueFromDb($field)
    {

        $this->getEntityManager()->refresh($this->currentUser);

        $this->getPage()->fillField($field, $this->currentUser->getTwoFactorCode());
    }


    /**
     * @Given the following articles exist:
     */
    public function theFollowingArticlesExist(TableNode $table)
    {
        $category = new Category();
        $category->setName('Behat Is Awsome');

        foreach ($table as $row) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($row['title']);
            $blogPost->setSummary($row['summary']);

            if (isset($row['is published']) && $row['is published'] === 'yes') {
                $blogPost->setIsPublished(true);
            }

            $blogPost->setContent('<b>Lorem Ipsum Dolor Sit Amet</b>');
            $blogPost->setCategory($category);
            $blogPost->setPublishedAt(new \DateTime('-1 month'));

            $blogPost->setUser($this->currentUser);

            $this->getEntityManager()->persist($blogPost);
        }
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }

    /**
     * @Then the :rowText row should have a check mark
     */
    public function theRowShouldHaveACheckMark($rowText)
    {
        $row = $this->findRowByText($rowText);
        assertContains('fa-check', $row->getHtml(), 'Could not find the fa-check element in the row!');
    }

    /**
     * @When I click :buttonText in the :rowText row
     */
    public function iClickInTheRow($buttonText, $rowText)
    {
        $row = $this->findRowByText($rowText);
        $button = $row->findButton($buttonText);
        assertNotNull($button, 'Cannot find link in row with text '.$buttonText);
        $button->click();
    }

    /**
     * @When I click on :selector in the :rowText row
     */
    public function iClickOnInTheRow($selector, $rowText)
    {
        $row = $this->findRowByText($rowText);
        $button = $row->find("css", $selector);
        assertNotNull($button, 'Cannot find link in row with css selector '.$selector);
        $button->click();
    }

    private function findRowByText($rowText)
    {
        $row = $this->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        assertNotNull($row, 'Cannot find a table row with this text!');

        return $row;
    }
}
