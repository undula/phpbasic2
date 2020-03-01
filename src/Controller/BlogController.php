<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    private const POSTS = [
        ['id' => 1,
            'slug'=>'hello-world',
            'title'=> 'Hello World!'],
        ['id' => 2,
            'slug'=>'hello-world2',
            'title'=> 'Hello World!2'],
        ['id' => 3,
        'slug'=>'hello-world3',
        'title'=> 'Hello World!3']
    ];


    /**
     * @Route("/{page}", name="blog_list",  defaults={"page": 5}, requirements={"page" ="\d+"})
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);
   //     $repository = $this->getDoctrine()->getRepository(BlogPost::class);
   //     $items = $repository->findAll();
  //  return new Response( $limit);
        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function ($item){
                return $this->generateUrl('blog_by_slug', ['slug' =>$item['slug']]);
            },self::POSTS)
        ]
        );
    }
    /**
     * @Route("/{id}", name="blog_by_id", requirements={"id" ="\d+"})
     */
    public function post($id)
    {
        return $this->json(self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]);
       // return $this->json($this->getDoctrine()->getRepository(BlogPost::class)->find($id));
    }
    /**
     * @Route("/{slug}", name="blog_by_slug", requirements={"slug" = "^[a-z0-9-]+$"})
     */
    public function postBySlug($slug)
    {return $this->json(self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]);
        //return $this->json($this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' =>$slug]));
    }

    /**
     * @Route("/add=", name="blog_add", methods={"POST"} )
     */
    public function add(Request $request){
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);

    }
}
