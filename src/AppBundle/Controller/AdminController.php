<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Word;
use AppBundle\Form\WordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("/admin/users", name="admin_users")
     *
     * @Method({"GET"})
     *
     * @return Response
     */
    public function userAction()
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @param User|null $user
     *
     * @Route("/admin/users/{user}", name="admin_user_delete")
     *
     * @Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteUserAction(User $user = null)
    {
        if (!$user) {
            throw $this->createNotFoundException('Who?');
        }

        $this->get('fos_user.user_manager')->deleteUser($user);

        return new Response(null);
    }

    /**
     * @param User|null $user
     *
     * @Route("/admin/users/{user}/toggle", name="admin_user_toggle")
     *
     * @Method({"POST"})
     *
     * @return Response
     */
    public function toggleUserAction(User $user = null)
    {
        if (!$user) {
            throw $this->createNotFoundException('Who?');
        }

        $user->setEnabled(!$user->isEnabled());

        $this->get('fos_user.user_manager')->updateUser($user);

        return new Response(null);
    }

    /**
     * @Route("/admin/words", name="admin_words")
     *
     * @Method({"GET"})
     *
     * @return Response
     */
    public function wordAction()
    {
        $wordRepository = $this->getDoctrine()->getRepository('AppBundle:Word');

        return $this->render('admin/word/index.html.twig', [
            'words' => $wordRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @param Word|null $word
     *
     * @Route("/admin/words/{word}", name="admin_word_new")
     *
     * @Method({"GET", "POST"})
     *
     * @return Response
     */
    public function editWordAction(Request $request, Word $word = null)
    {
        if (!$word) {
            $word = new Word();
        }

        $form = $this->createForm(WordType::class, $word);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if (!$entityManager->contains($word)) {
                $word = $form->getData();
            }

            $entityManager->persist($word);

            $entityManager->flush();

            return $this->redirectToRoute('admin_words');
        }

        return $this->render('admin/word/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Word|null $word
     *
     * @Route("/admin/words/{word}", name="admin_word_delete")
     *
     * @Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteWordAction(Word $word = null)
    {
        if (!$word) {
            throw $this->createNotFoundException('What?');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($word);

        $entityManager->flush();

        return new Response(null);
    }
}
