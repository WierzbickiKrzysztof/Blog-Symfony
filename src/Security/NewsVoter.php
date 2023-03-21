<?php
namespace App\Security;

use App\Entity\News;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NewsVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::VIEW, self::EDIT])) {
    return false;
    }

    // only vote on Post objects inside this voter
    if (!$subject instanceof News) {
    return false;
    }

    return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
    $user = $token->getUser();

    if (!$user instanceof User) {
    // the user must be logged in; if not, deny access
    return false;
    }

    // you know $subject is a Post object, thanks to supports
    /** @var Post $post */
    $news = $subject;

    switch ($attribute) {
    case self::VIEW:
    return $this->canView($news, $user);
    case self::EDIT:
    return $this->canEdit($news, $user);
    }

    throw new \LogicException('This code should not be reached!');
    }

    private function canView(News $news, User $user)
    {


        if($user->getUsername() == $news->getAuthor()->getUsername() && !$news->getIsDelete()){
            return true;
        }


        //To polecenie sprawdza czy collection zawiera (contains), user. Znaczy to mniej więcej tyle: getNewsAccessList to array(chyba kolekcja) w, której przechowywane są pełne profile użytkowników dozwolonych do widzenia postu, a $user to własnie pełny profil zalogowanego użytkownika. Pełny profil to array z id, username i hasłem itd.
        if($contains = $news->getNewsAccessList()->contains($user) && !$news->getIsDelete()){
            return true;
        } else {
            return false;
        }

    }

    private function canEdit(News $news, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $news->getAuthor();
    }
}