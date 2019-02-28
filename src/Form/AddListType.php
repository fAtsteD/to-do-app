<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\TasksList;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Short for create list
 */
class AddListType extends AbstractType
{
    /** @var UrlGeneratorInterface $router */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add("title", TextType::class, [
                'label' => false,
            ])
            ->add("save", SubmitType::class, [
                'label' => '+',
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TasksList::class,
        ]);
    }
}
