<?php

namespace App\Form\Task;

use App\Entity\Task;
use App\Form\Task\TaskAddType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status')
        ;
    }
    
    /**
     * @return string
     */
    public function getParent(): string
    {
        return TaskAddType::class;
    }
}
