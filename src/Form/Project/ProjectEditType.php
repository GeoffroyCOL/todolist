<?php

namespace App\Form\Project;

use App\Form\Project\ProjectAddType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status')
        ;
    }

    public function getParent()
    {
        return ProjectAddType::class;
    }
}
