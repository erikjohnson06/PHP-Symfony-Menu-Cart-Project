<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController {

    /**
     * @Route("/registration", name="app_registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, ManagerRegistry $doctrine): Response {

        $form = $this->createFormBuilder()
                ->add('username', TextType::class, [
                    'label' => 'Employee ID',
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter your first name.',
                                ]),
                        new Length([
                            'min' => 1,
                            'minMessage' => 'Please enter your first name',
                            'max' => 100,
                            'maxMessage' => 'Please limit your first name to {{ limit }} characters',
                                ]),
                        new Regex([
                            "pattern" => "/^[A-Za-z0-9\-\_\#\.\/\s\!\;\&]+$/",
                            "message" => "Employee ID contains invalid characters."
                                ])
                    ]
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => '6-20 Characters'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                                ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 20,
                            'maxMessage' => 'Please limit your password to {{ limit }} characters',
                                ]),
                        new Regex([
                            "pattern" => "/^(?=[-_a-zA-Z0-9]*?[A-Za-z])(?=[-_a-zA-Z0-9]*?[0-9])[!.-_a-zA-Z0-9]{6,20}$/",
                            "message" => "Passwords must consist of 6 to 20 characters and at least one numeric character. Special characters may include hyphens, underscores, exclamation points and periods."
                                ])
                    ],
                ])
                ->add('registration', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $data = $form->getData();

            try {
                $user = new User();
                $user->setUsername($data['username']);
                $user->setRawPassword($data['password']);
                $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));

                $errors = $validator->validate($user);

                if (count($errors)) {

                    return $this->render('registration/index.html.twig', [
                                'errors' => $errors,
                                'regForm' => $form->createView()
                    ]);
                }

                //Entity Manager
                $entityMgr = $doctrine->getManager();
                $entityMgr->persist($user);
                $entityMgr->flush();

                $this->addFlash('success', "Registration successful! Please login with your username and password.");

                return $this->redirect($this->generateUrl("app_registration"));
            }
            catch (UniqueConstraintViolationException $ex) {
                $this->addFlash('error', "Error: Employee ID is already in use. Please enter a unique employee ID.");
                return $this->redirect($this->generateUrl("app_registration"));
            }
        }

        return $this->render('registration/index.html.twig', [
                    'errors' => null,
                    'regForm' => $form->createView()
        ]);
    }

}
