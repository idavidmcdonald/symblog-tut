<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;

class BlogController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comments = $em->getRepository('BloggerBlogBundle:Comment')
                       ->getCommentsForBlog($blog->getId());

        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
            'blog'     => $blog,
            'comments' => $comments
        ));
    }

    public function createAction(Request $request){
        $blog = new Blog();
        $form = $this->createForm(new BlogType(), $blog);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getManager();
                $em->persist($blog);
                $em->flush();

                return $this->redirect($this->generateUrl('BloggerBlogBundle_show', array(
                    'id' => $blog->getId()))
                );
            }
        }

        return $this->render('BloggerBlogBundle:Blog:create.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
