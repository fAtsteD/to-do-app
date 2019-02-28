<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Document\User;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form for registrate user
 */
class RegistrateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->setMethod('POST')
            ->add('username', TextType::class, [
                'label' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Assert\Type('string'),
                    new Assert\Length([
                        'min' => 6,
                        'max' => 100
                    ]),
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z0-9]+$/',
                        'message' => 'Password should have lower and upper case letters, numbers.',
                    ]),
                ],
                'first_options' => [
                    'label' => false,
                ],
                'second_options' => [
                    'label' => false,
                ],
                'mapped' => false,
            ])
            ->add('email', TextType::class, [
                'label' => false,
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => false,
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Sign up',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
