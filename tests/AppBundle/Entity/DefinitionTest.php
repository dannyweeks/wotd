<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Definition;
use AppBundle\Entity\Word;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    /** @var Definition */
    private $definition;

    public function setUp()
    {
        $this->definition = new Definition();

        parent::setUp();
    }

    public function testSpeechPartIsAString()
    {
        $this->definition->setSpeechPart('foobar definition');
        $this->assertSame('foobar definition', $this->definition->getSpeechPart());
    }

    public function testTextIsAString()
    {
        $this->definition->setText('foobar text');
        $this->assertSame('foobar text', $this->definition->getText());
    }

    public function testWordCanBeSet()
    {
        $word = new Word();
        $this->definition->setWord($word);
        $this->assertInstanceOf(Word::class, $this->definition->getWord());
    }

    public function testTimestampsCanBeUpdated()
    {
        $this->definition->updateTimestamps();
        $this->assertInstanceOf(\DateTime::class, $this->definition->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $this->definition->getUpdatedAt());
    }
}
