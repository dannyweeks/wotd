<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Word;
use Tests\FunctionalTestCase;

class DefaultControllerTest extends FunctionalTestCase
{
    public function testSeeAboutPage()
    {
        $crawler = $this->client->request('GET', '/about');
        $this->assertResponseOk();
        $this->assertContains('About', $crawler->filter('h1')->text());
    }

    public function testSeeDefaultWordOnIndexPage()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseOk();
        $this->assertContains('Missing', $crawler->filter('h1')->text());
    }

    public function testSeeRandomWordOnIndexPage()
    {
        $word = new Word();
        $word->setWord('Example');
        $this->entityManager->persist($word);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/');
        $this->assertResponseOk();
        $this->assertContains('Example', $crawler->filter('h1')->text());
    }
}
