<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Entity\Enquiry;
use Blogger\BlogBundle\Form\EnquiryType;


class PageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getManager();

        $blogs = $em->getRepository('BloggerBlogBundle:Blog')
                    ->getLatestBlogs();

        return $this->render('BloggerBlogBundle:Page:index.html.twig', array(
            'blogs' => $blogs
        ));
    }

    public function aboutAction()
    {
        return $this->render('BloggerBlogBundle:Page:about.html.twig');
    }

    public function contactAction(Request $request){
    	$enquiry = new Enquiry();
	    $form = $this->createForm(new EnquiryType(), $enquiry);

	    if ($request->getMethod() == 'POST') {
	        $form->handleRequest($request);

	        if ($form->isValid()) {
	            $message = \Swift_Message::newInstance()
            		->setSubject('Contact enquiry from symblog')
            		->setFrom('enquiries@symblog.co.uk')
            		->setTo($this->container->getParameter('blogger_blog.emails.contact_email'))
            		->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
        		
        		$this->get('mailer')->send($message);

        		$this->addFlash('notice', 'Your contact enquiry was successfully sent. Thank you!');

	            // Redirect - This is important to prevent users re-posting
	            // the form if they refresh the page
	            return $this->redirect($this->generateUrl('BloggerBlogBundle_contact'));
	        }
	    }

	    return $this->render('BloggerBlogBundle:Page:contact.html.twig', array(
	        'form' => $form->createView()
	    ));
    }
}