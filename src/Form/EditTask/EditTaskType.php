<?php

namespace App\Form\EditTask;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\Task;
use App\Form\EditTask\SubtaskType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Form for edit task
 */
class EditTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('isDone', CheckboxType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => false
            ])
            ->add('listId', ChoiceType::class, [
                'label' => false,
                'choices' => $options['choicesList'],
            ])
            ->add('dueDate', DateType::class, [
                'label' => false,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('subtasks', CollectionType::class, [
                'label' => false,
                'entry_type' => SubtaskType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'choicesList' => [],
        ]);
    }
}
