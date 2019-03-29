<?php

namespace App\Form\EditList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Model\TasksList\UserInListModel;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Build form for each user with username and permission
 */
class UserType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
            ])
            ->add('permission', ChoiceType::class, [
                'label' => false,
                'choices' => $options['choicesList'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInListModel::class,
            'choicesList' => [
                'No permission' => UserInListModel::NO_PERMISSION,
                'View' => UserInListModel::VIEW,
                'Edit' => UserInListModel::EDIT,
            ],
        ]);
    }
}
