<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    /**
     * @Route("/{page}", name="blog_list",  defaults={"page": 5}, requirements={"page" ="\d+"})
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);
       $repository = $this->getDoctrine()->getRepository(BlogPost::class);
       $items = $repository->findAll();
  //  return new Response( $limit);
        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function (BlogPost $item){
                return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
            },$items)
        ]
        );
    }
    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id" ="\d+"})
     * @ParamConverter("post", class="App:BlogPost")
     */
    public function post($post)
    {
        // its the same as dologing find($id)
       // return $this->json(self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]);
       return $this->json($post);
    }
    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     * @ParamConverter("post", class="App:BlogPost", options={"mapping" : {"slug": "slug"}})
     */
    public function postBySlug($post)
    {
        //return $this->json(self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]);
 //       return $this->json($this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' =>$slug]));
        return $this->json($post);
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"} )
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
