<?php

namespace Tests\Entity;

use App\Tests\Traits\AssertHasErrors;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ElementTest extends KernelTestCase
{
    use AssertHasErrors;

    /**
     * @return Element
     */
    abstract protected function getEntity();
    
    /**
     * testNotBlankEntity
     *
     * @return void
     */
    public function testNotBlankEntity(): void
    {
        $element = $this->getEntity();
        $element->setName('')
            ->setDescription('')
        ;

        $this->assertHasErrors($element, 3);
    }
    
    /**
     * testLengthName
     * Test la longueur de la propriété name : minimum 6 caractères
     *
     * @return void
     */
    public function testLengthName()
    {
        $element = $this->getEntity();

        $element->setName('un');
        $this->assertHasErrors($element, 1);
    }

    /**
     * testBadLimlitedAt
     * Test si la date de fin d'un projet / tâche ne peut être inférieure à la date de création du projet / tâche
     *
     * @return void
     */
    public function testBadLimlitedAt(): void
    {
        $element = $this->getEntity();
        $date = (new \Datetime)->sub(new \DateInterval('P1D'));
        $element->setLimitedAt($date);

        $this->assertHasErrors($element, 1);
    }

    /**
     * testBadStatus
     * Test si le status possède un choix valide : " terminé, en cours, en attente "
     *
     * @return void
     */
    public function testBadStatus(): void
    {
        $element = $this->getEntity();
        $element->setStatus('nul');

        $this->assertHasErrors($element, 1);
    }
    
    /**
     * @return void
     */
    public function testGoodEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
}
