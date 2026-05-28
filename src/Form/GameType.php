<?php

namespace App\Form;

use App\Entity\CompetitionPhase;
use App\Entity\Game;
use App\Entity\Stadium;
use App\Entity\Team;
use App\Enum\GameStatusEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('competitionPhase', EntityType::class, [
                'class' => CompetitionPhase::class,
                'choice_label' => fn(CompetitionPhase $p) => $p->getCompetition()->getName() . ' — ' . $p->getName(),
                'placeholder' => '— select phase —',
            ])
            ->add('homeTeam', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'placeholder' => '— select team —',
                'required' => false,
            ])
            ->add('awayTeam', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'placeholder' => '— select team —',
                'required' => false,
            ])
            ->add('datetime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('stadium', EntityType::class, [
                'class' => Stadium::class,
                'choice_label' => 'name',
                'placeholder' => '— select stadium —',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => GameStatusEnum::class,
            ])
            ->add('homeScore', IntegerType::class, ['required' => false])
            ->add('awayScore', IntegerType::class, ['required' => false])
            ->add('homePenaltyScore', IntegerType::class, ['required' => false])
            ->add('awayPenaltyScore', IntegerType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Game::class]);
    }
}
