<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Document\Task;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Short form for create task
 */
class AddTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false
            ])
            ->add('dueDate', DateType::class, [
                'label' => false,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class
        ]);
    }
}
