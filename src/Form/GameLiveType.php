<?php

namespace App\Form;

use App\Entity\Game;
use App\Enum\GameStatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameLiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EnumType::class, ['class' => GameStatusEnum::class])
            ->add('homeScore', IntegerType::class, ['required' => false, 'attr' => ['min' => 0]])
            ->add('awayScore', IntegerType::class, ['required' => false, 'attr' => ['min' => 0]])
            ->add('homePenaltyScore', IntegerType::class, ['required' => false, 'attr' => ['min' => 0]])
            ->add('awayPenaltyScore', IntegerType::class, ['required' => false, 'attr' => ['min' => 0]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Game::class]);
    }
}
