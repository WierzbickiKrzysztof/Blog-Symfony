<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class FacebookRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nazwa użytkownika"])
            ->add('email', TextType::class, [
                'disabled' => true,
                'label' => "Adres e-mail"
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false, // allows this to not be a real property on User
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Hasło'],
                'second_options'  => ['label' => 'Powtórz Hasło'],
            ])

            ->add('save', SubmitType::class, ['label' => 'Zarejestruj się'])
        ;
    }
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'data_class' => 'AppBundle\Entity\User'
//        ));
//    }
//    public function getName()
//    {
//        return 'app_bundle_user_registration_type';
//    }
}