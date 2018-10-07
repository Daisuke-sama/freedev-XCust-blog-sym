<?php
/**
 * Created by Pavel Burylichau
 * Company: EPAM Systems
 * User: pavel_burylichau@epam.com
 * Date: 10/5/18
 * Time: 3:39 PM
 */


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreatorFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cssClass = 'form-control';

        $builder->add(
            'name',
            TextType::class,
            [
                'constraints' => [new NotBlank()],
                'attr'        => ['class' => $cssClass],
            ]
        )->add(
            'biography',
            TextType::class,
            [
                'constraints' => [new NotBlank()],
                'attr'        => ['class' => $cssClass],
            ]
        )->add(
            'facebook',
            TextType::class,
            [
                'attr'     => ['class' => $cssClass],
                'required' => false,
            ]
        )->add(
            'twitter',
            TextType::class,
            [
                'attr'     => ['class' => $cssClass],
                'required' => false,
            ]
        )->add(
            'instagram',
            TextType::class,
            [
                'attr'     => ['class' => $cssClass],
                'required' => false,
            ]
        )->add(
            'submit',
            SubmitType::class,
            [
                'attr'  => ['class' => $cssClass.' btn-primary pull-right'],
                'label' => 'Start',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => 'App\Entity\Creator',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'creator_form';
    }
}
