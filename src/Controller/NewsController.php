<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\News;
use App\Entity\NewsHistory;
use App\Entity\User;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Service\NewsAccessChecker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param NewsRepository $newsRepository
     * @param NewsAccessChecker $newsAccessChecker
     * @return Response
     */
    public function index(NewsRepository $newsRepository, NewsAccessChecker $newsAccessChecker)
    {
        $this->denyAccessUnlessGranted('ROLE_USERB', null, 'User tried to access a page without having ROLE_USERB');

        $news = $newsRepository->findBy([], ['publishedAt' => 'DESC']); //Zastąpiono findAll na findBy aby móc sortować wyniki i wyświetlać najnowszy post u góry (najwyższy id)

        //TODO: isGranted przenieść do funkcji getNews_with_Access (sprawdzić czy jest sens takiego przeniesienia w dobrych praktykach Symfony)
        if (!$this->isGranted("ROLE_ADMIN")) {
            //Fuszerka która tu była zastąpiona została funkcją z Services NewsAccessChecker
            $news = $newsAccessChecker->getNews_with_Access($news);
        } else {
            $news = $newsAccessChecker->getNews_without_Delete($news);
        }

        if(count($news)<1) {
            return $this->render('news/no_news.html.twig');
        }else {
            return $this->render('news/index.html.twig', [
                'news' => $news
            ]);
        }
    }


    /**
     * @Route("/news/cat/delete", name="news_by_cat_del")
     * @param $category
     * @param NewsRepository $newsRepository
     * @param NewsAccessChecker $newsAccessChecker
     * @return Response
     */
    public function newsByDel(NewsRepository $newsRepository)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $news = $newsRepository->findBy(['isDelete' => true], ['publishedAt' => 'DESC']);

        if (!$this->isGranted("ROLE_ADMIN")) {
            return false;
        }

        if(count($news)<1) {
            return $this->render('news/no_news.html.twig');
        }else{
            return $this->render('news/show_cat.html.twig', [
                'news' => $news,
                'category' => 'Usunięte'
            ]);
        }
    }


    /**
     * @Route("/news/cat/{category}", name="news_by_cat")
     * @param $category
     * @param NewsRepository $newsRepository
     * @param NewsAccessChecker $newsAccessChecker
     * @return Response
     */
    public function newsByCat($category, NewsRepository $newsRepository, NewsAccessChecker $newsAccessChecker)
    {

        $news = $newsRepository->findByCategory($category);

        //TODO: isGranted przenieść do funkcji getNews_with_Access (sprawdzić czy jest sens takiego przeniesienia w dobrych praktykach Symfony)
        if (!$this->isGranted("ROLE_ADMIN")) {
            //Fuszerka która tu była zastąpiona została funkcją z Services NewsAccessChecker
            $news = $newsAccessChecker->getNews_with_Access($news);
        } else {
            $news = $newsAccessChecker->getNews_without_Delete($news);
        }

        if(count($news)<1){
            return $this->render('news/no_news.html.twig');

        }else{

            return $this->render('news/show_cat.html.twig', [
                'news' => $news,
                'category' => $category
            ]);
        }
    }


    /**
     * @Route("news/add", name="news_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws \Exception
     */

    public function addNews(Request $request, EntityManagerInterface $entityManager)
    {

        // creates a task and gives it some dummy data for this example
        $news = new News();

        $news->setPublishedAt(new \DateTime('now'));


        $form = $this->createForm(NewsType::class, $news);

//        $form = $this->createFormBuilder($news)
//            ->add('title', TextType::class, ['label' => "Tytuł"])
//            ->add('body', TextareaType::class, ['label' => "Treść"])
//            ->add('category', EntityType::class, [
//                'class' => Category::class,
//                'label' => "Kategoria"
//            ])
//            ->add('NewsAccessList', EntityType::class, [
//                'class' => User::class, // Wystarczyło wpisać w class User zamiast News
//                'multiple' => true,
//                'expanded' => true,
//                'choice_label' => 'username',
//                'label' => "Wybierz kto może zobaczyć artykuł"
//
//
//            ])
//            ->add('save', SubmitType::class, ['label' => 'Create Task'])
//            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $news = $form->getData();


            $news->setIsDelete(false);

            //TODO: Sprawdzić czemu wystarczy wpisać samo getUser() zamiast username lub id
            $news->setAuthor($this->getUser());

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            //$entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
        }

        return $this->render('news/add.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("news/edit/{id}", name="news_edit")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param News $news
     * @param NewsHistory $newsHistory
     * @return Response
     */

    public function editNews(Request $request, EntityManagerInterface $entityManager, News $news)
    {
        if($news->getIsDelete()){
            return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
        } else {
            
            if(!$this->isGranted('ROLE_ADMIN')){
                $this->denyAccessUnlessGranted('edit', $news);
            }


            //$news->setPublishedAt(new \DateTime('now'));

            $newsHistory = new NewsHistory();
            $newsHistory->setNewsId($news->getId());
            $newsHistory->setTitle($news->getTitle());
            $newsHistory->setBody($news->getBody());
            $newsHistory->setPublishedAt($news->getPublishedAt());
            $newsHistory->setCategoryId($news->getCategory()->getId());
            $newsHistory->setAuthorId($news->getAuthor()->getId());
            $newsHistory->setEditedAt($news->getEditedAt());

            $form = $this->createForm(NewsType::class, $news);

//            $form = $this->createFormBuilder($news)
//                ->add('title', TextType::class)
//                ->add('body', TextareaType::class)
//                ->add('category', EntityType::class, [
//                    'class' => Category::class
//                ])
//                ->add('NewsAccessList', EntityType::class, [
//                    'class' => User::class, // Wystarczyło wpisać w class User zamiast News
//                    'multiple' => true,
//                    'expanded' => true,
//                    'choice_label' => 'username'
//
//
//                ])
//                ->add('save', SubmitType::class, ['label' => 'Zaktualizuj'])
//                ->getForm();


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                //$news = $form->getData();




                $entityManager->persist($newsHistory);
                $entityManager->flush();
                // $news->setIsDelete(false);

                //TODO: Sprawdzić czemu wystarczy wpisać samo getUser() zamiast username lub id
                //$news->setAuthor($this->getUser());

                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                //$entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($news);
                $entityManager->flush();

                return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
            }

            return $this->render('news/edit.html.twig', [
                'form' => $form->createView(),
                'news' => $news
            ]);
        }

    }



    /**
     * @Route ("/news/{id}", name="news_show")
     * @param News $news
     * @return Response
     */
    public function showNews(News $news)
    {

        //TODO: isGranted przenieść do Votera (sprawdzić czy jest sens takiego przeniesienia w dobrych praktykach Symfony)
        if (!$this->isGranted("ROLE_ADMIN")) {
            $this->denyAccessUnlessGranted('view', $news);

        }
        //Sprawdzenie czy użytkownik jest autorem newsa przeniesiono do template Twig
        //$edit_p = $this->isGranted('edit', $news);


        //Prosty system wyświetlenia nicknamów z przecinakmi
        $nicknames_array = [];
        foreach($news->getNewsAccessList() as $key){
            $nicknames_array[] = $key->getUsername();
        }

        $nicknames = implode(", ", $nicknames_array);




        return $this->render('news/show.html.twig', [
            'news' => $news,
            'nicknames' => $nicknames
        ]);
    }


    /**
     * @Route ("news/del/{id}", name="news_delete")
     * @param News $news
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteNews(News $news, EntityManagerInterface $em)
    {

        if(!$this->isGranted('ROLE_ADMIN')){
            $this->denyAccessUnlessGranted('edit', $news);
        }


        $news->setIsDelete(true);
        $em->flush();
        return $this->redirectToRoute('home');

    }

    /**
     * @Route ("news/restore/{id}", name="news_restore")
     * @param News $news
     * @param EntityManagerInterface $em
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function restoreNews(News $news, EntityManagerInterface $em, $id)
    {
        $news->setIsDelete(false);
        $em->flush();
        return $this->redirectToRoute('news_show', ['id' => $id]);

    }


    //Cztery poniższe funkcje są do przepisania, tak samo blokada folderu upload musi być w inny sposób; to rozwiązanie tymczasowe w celu zapewnienia kompatybilności z starą wersją silnika

    /**
     * @Route ("download/{loc}/{file}", name="download")
     */
    public function downloadFile($loc, $file)
    {
        header('Content-type: application/*');

        // ustawiamy jego nazwę na downloaded.pdf
        header('Content-Disposition: attachment; filename='.$file);

        // treść znajduje się w pliku pdf/original.pdf
        readfile($loc.'/'.$file);

    }


    /**
     * @Route ("pdf/{loc}/{file}", name="pdf")
     * @param $loc
     * @param $file
     */
    public function pdfFile($loc, $file)
    {
        header('Content-type: application/pdf');

        // ustawiamy jego nazwę na downloaded.pdf
        header('Content-Disposition: inline; filename='.$file);

        // treść znajduje się w pliku pdf/original.pdf
        readfile($loc.'/'.$file);

    }


    /**
     * @Route ("video/{loc}/{file}", name="video")
     * @param $loc
     * @param $file
     */
    public function videoFile($loc, $file)
    {
        header("Content-type: video/mp4");

        // treść znajduje się w pliku pdf/original.pdf
        readfile($loc.'/'.$file);

    }

    /**
     * @Route ("audio/{loc}/{file}", name="audio")
     * @param $loc
     * @param $file
     */
    public function audioFile($loc, $file)
    {
        header("Content-type: audio/wav");

        // treść znajduje się w pliku pdf/original.pdf
        readfile($loc.'/'.$file);

    }



}
