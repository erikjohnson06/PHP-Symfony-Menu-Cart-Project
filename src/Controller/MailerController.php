<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
//use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController {

    /**
     * @Route("/contact", name="app_contact")
     */
    public function sendEmail(MailerInterface $mailer, Request $request): Response {

        $emailForm = $this->createFormBuilder()
                ->add("message", TextareaType::class, [
                    'attr' => ['rows' => '5']
                ])
                ->add("send", SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-outline-danger float-right'
                    ]
                ])
                ->getForm();

        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted()) {

            $input = $emailForm->getData();

            $table = "table1";
            $text = $input['message'];

            $email = (new TemplatedEmail())
                    ->from("orders@menucart.test.com")
                    ->to("servers@menucart.test.com")
                    ->subject("Order")
                    ->htmlTemplate("mailer/mail.html.twig")
                    ->context([
                "table" => $table,
                "text" => $text
            ]);

            $mailer->send($email);

            $this->addFlash("message", "Message was sent successfully");

            return $this->redirect($this->generateUrl("app_contact"));
        }

        return $this->render("mailer/index.html.twig", [
                    "emailForm" => $emailForm->createView()
        ]);
    }

}
