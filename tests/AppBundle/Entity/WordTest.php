<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Definition;
use AppBundle\Entity\Word;
use PHPUnit\Framework\TestCase;

class WordTest extends TestCase
{
    /** @var Word */
    private $word;

    public function setUp()
    {
        $this->word = new Word();

        parent::setUp();
    }
    public function testWordIsConstructed()
    {
        $this->assertInstanceOf(Word::class, $this->word);
        $this->assertNull($this->word->getWord());
        $this->assertTrue($this->word->getDefinitions()->isEmpty());
    }

    public function testWordStringHasAString()
    {
        $this->word->setWord('foobar');
        $this->assertSame('foobar', $this->word->getWord());
    }

    public function testWordHasAPronunciation()
    {
        $this->word->setPronunciation('fu:-ba:r');
        $this->assertSame('fu:-ba:r', $this->word->getPronunciation());
    }

    public function testWordHasADate()
    {
        $this->word->setDate(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $this->word->getDate());
    }

    public function testDefinitionCanBeAddedAndRemoved()
    {
        $definition = new Definition();
        $this->word->addDefinition($definition);
        $this->assertSame($definition, $this->word->getDefinitions()->first());

        $this->word->removeDefinition($definition);
        $this->assertCount(0, $this->word->getDefinitions());
    }

    public function testSameDefinitionWontBeAddedMoreThanOnce()
    {
        $definition = new Definition();
        $this->word->addDefinition($definition);
        $this->word->addDefinition($definition);
        $this->assertCount(1, $this->word->getDefinitions());
    }

    public function testTimestampsCanBeUpdated()
    {
        $this->word->updateTimestamps();
        $this->assertInstanceOf(\DateTime::class, $this->word->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $this->word->getUpdatedAt());
    }
}
