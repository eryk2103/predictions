<?php

namespace App\Form;

use App\Entity\Prediction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PredictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('homeScore', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ],
            ])
            ->add('awayScore', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('homePenalty', IntegerType::class, ['required' => false, 'attr' => ['min' => 0]])
            ->add('awayPenalty', IntegerType::class, ['required' => false, 'attr' => ['min' => 0]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Prediction::class]);
    }
}
