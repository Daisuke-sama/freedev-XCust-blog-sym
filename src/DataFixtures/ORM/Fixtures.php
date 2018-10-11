<?php
/**
 * Created by Pavel Burylichau
 * Company: EPAM Systems
 * User: pavel_burylichau@epam.com
 * Date: 10/4/18
 * Time: 6:03 PM
 */


namespace App\DataFixtures\ORM;


use App\Entity\BlogPost;
use App\Entity\Creator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class Fixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $creator = new Creator();
        $creator->setName('Kesha')
            ->setBiography('I am a blog owner and advertisement dealer.')
            ->setUsername('test-admin')
            ->setFacebook('thebeatles')
            ->setTwitter('thebeatles')
            ->setInstagram('thebeatles');
        $manager->persist($creator);

        $blogPost = new BlogPost();
        $strTemp
                  = 'This is a small description of the post, where you could find a lot of very usefull information and data for future use.';
        $blogPost->setCreator($creator)
            ->setTitle('The very new post')
            ->setDescription($strTemp)
            ->setBody(
                array_reduce(
                    explode(' ', $strTemp),
                    function ($prev, $v) {
                        $prev .= str_repeat($v, 3).' ';

                        return $prev;
                    },
                    'this'
                )
            )
            ->setSlug('my-new-post');
        $manager->persist($blogPost);
        $manager->flush();
    }
}