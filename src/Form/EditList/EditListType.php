<?php

namespace App\Form\EditList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Model\TasksList\ListModel;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Edit list for add people for sharing
 */
class EditListType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, $options)
    {
        $builder
            ->setMethod('POST')
            ->add('title', TextType::class, [
                'label' => false,
            ])
            ->add('users', CollectionType::class, [
                'label' => false,
                'entry_type' => UserType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListModel::class,
        ]);
    }
}
