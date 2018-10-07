<?php
/**
 * Created by Pavel Burylichau
 * Company: EPAM Systems
 * User: pavel_burylichau@epam.com
 * Date: 10/7/18
 * Time: 10:06 PM
 */


namespace App\Form;


use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EntryFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldOptions = [
            'constraints' => [new NotBlank()],
            'attr'        => ['class' => 'form-control'],
        ];

        $builder->add(
            'title',
            TextType::class,
            $fieldOptions
        )->add(
            'slug',
            TextType::class,
            $fieldOptions
        )->add(
            'description',
            TextareaType::class,
            $fieldOptions
        )->add(
            'body',
            TextareaType::class,
            $fieldOptions
        )->add(
            'create',
            SubmitType::class,
            [
                'attr'  => ['class' => 'form-control btn-primary pull-right'],
                'label' => 'make!',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefault('data_class', 'App\Entity\BlogPost');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'creator_form';
    }
}
