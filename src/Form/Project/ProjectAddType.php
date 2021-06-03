<?php

namespace App\Form\Project;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
            ])
            ->add('description', TextareaType::class, [
                'required'  => false,
                'label'     => 'Description',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
                'attr' => [
                    'rows' => 6
                ],
            ])
            ->add('limitedAt', DateType::class, [
                'required'  => false,
                'label'     => 'Date limite de réalisation',
                'help'      => 'Optionel',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
