<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Stadium;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('shortName', TextType::class)
            ->add('code', TextType::class, [
                'constraints' => [new Length(max: 3)],
                'attr' => ['maxlength' => 3],
            ])
            ->add('logo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File(
                        maxSize: '1024k',
                        extensions: ['svg'],
                        extensionsMessage: 'Please upload a valid SVG file',
                    )
                ],
            ])
            ->add('city', TextType::class, ['required' => false])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'placeholder' => '— select country —',
                'required' => false,
            ])
            ->add('stadium', EntityType::class, [
                'class' => Stadium::class,
                'choice_label' => 'name',
                'placeholder' => '— select stadium —',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Team::class]);
    }
}
