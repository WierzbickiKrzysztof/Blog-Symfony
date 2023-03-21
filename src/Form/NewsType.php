<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => "Tytuł"])
            ->add('body', TextareaType::class, ['label' => "Treść"])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => "Kategoria",
                'placeholder' => 'Wybierz kategorię'
            ])
            ->add('NewsAccessList', EntityType::class, [
                'class' => User::class, // Wystarczyło wpisać w class User zamiast News
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'username',
                'label' => "Wybierz kto może zobaczyć artykuł"


            ])
            ->add('save', SubmitType::class, ['label' => 'Zatwierdź'])
        ;
    }
}