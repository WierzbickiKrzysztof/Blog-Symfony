<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;

class NewsAccessChecker
{


    private $security;


    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;

    }




    public function getNews_with_Access($news_link)
    {



        $user = $this->security->getUser();
        $news = [];

        foreach ($news_link as $news_one){

            if($user->getUsername() == $news_one->getAuthor()->getUsername() && !$news_one->getIsDelete()){
                $news[] = $news_one;
            }
            if($contains = $news_one->getNewsAccessList()->contains($user) && !$news_one->getIsDelete()){
                $news[] = $news_one;
            }
        }

        return $news;

        //Ta fuszerka kończy się tutaj
    }


    public function getNews_without_Delete($news_link)
    {

        $news = [];

        foreach ($news_link as $news_one){

            if(!$news_one->getIsDelete()){
                $news[] = $news_one;
            }else{

            }
        }

        return $news;

        //Ta fuszerka kończy się tutaj
    }

}