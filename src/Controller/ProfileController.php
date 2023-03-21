<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index()
    {

        $user_data = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user_data' => $user_data,
        ]);
    }


    /**
     * @Route("/profile/change/nickname", name="change_nickname")
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newNickname(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request)
    {
        if ($this->isGranted('ROLE_USERB')) {
            $user = $this->getUser();


            $form = $this->createFormBuilder($user)
                ->add('username', TextType::class, ['label' => 'Wpisz nowy nickname'])
                ->add('save', SubmitType::class, ['label' => 'Zapisz'])
                ->getForm();


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
               //  $user = $form->getData();
                //$user->setUsername($form['username']);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('profile');
            }

            return $this->render('profile/new_nickname.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('profile');
    }


    /**
     * @Route("/profile/change/email", name="change_email")
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newEmail(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request)
    {
        if ($this->isGranted('ROLE_USERB')) {
            $user = $this->getUser();


            $form = $this->createFormBuilder($user)
                ->add('email', TextType::class, ['label' => 'Wpisz nowy adres e-mail'])
                ->add('save', SubmitType::class, ['label' => 'Zapisz'])
                ->getForm();


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                //  $user = $form->getData();
                //$user->setUsername($form['username']);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('profile');
            }

            return $this->render('profile/new_email.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profile/disconnect/fb", name="disconnect_fb")
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Request $request
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function disconnect_fb(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($this->isGranted('ROLE_USERB') && $this->getUser()->getfacebookId() != null) {
            $user = $this->getUser();


            $form = $this->createFormBuilder()
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'Hasło'],
                    'second_options' => ['label' => 'Powtórz hasło']
                ])
                ->add('save', SubmitType::class, ['label' => 'Zapisz'])
                ->getForm();


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                //  $user = $form->getData();
                //$user->setUsername($form['username']);

                $data = $form->getData();

                $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
                $user->setFacebookId(null);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('profile');
            }

            return $this->render('profile/disconnect_fb.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('profile');
    }




}
