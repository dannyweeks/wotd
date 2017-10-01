<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Definition;
use AppBundle\Entity\Word;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $today = new \DateTime();

        // If a date wasn't provided, we default to today.
        $date = ($request->query->get('date'))
            ? new \DateTime($request->query->get('date'))
            : $today;

        $entityManager = $this->getDoctrine()->getManager();

        $wordRepository = $entityManager->getRepository('AppBundle:Word');

        // If $date is set, let's get the word for that date.
        // Otherwise, we'll get the word for the current date.
        $word = $wordRepository->findOneBy(['date' => $date]);

        // If the user requested a specific date, $date will be that date.
        // Otherwise it will be $today. The user can also request today,
        // in which case the time will differ but the day (d) will be the same.
        $diff = $today->diff($date);

        // If the word is missing and a specific date, other than today, was
        // requested, then we show a 404 (no duplicate content!)
        if (!$word && $diff->d != 0) {
            throw $this->createNotFoundException();
        }

        if (!$word && $word = $wordRepository->getAFreshWord()) {
            $word->setDate(new \DateTime());

            $entityManager->flush();
        }

        if (!$word) {
            $word = $this->getDefaultWord();
        }

        return $this->render('default/index.html.twig', ['word' => $word]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction()
    {
        return $this->render('default/about.html.twig');
    }

    /**
     * Builds a Word object to be returned when no results are found.
     *
     * @return Word
     */
    private function getDefaultWord()
    {
        $definition = new Definition();
        $definition
            ->setSpeechPart('adjective')
            ->setText('Not present or included when expected or supposed to be.');

        $word = new Word();
        $word
            ->setWord('Missing')
            ->setPronunciation('mis-ing')
            ->setDate(new \DateTime())
            ->addDefinition($definition);

        return $word;
    }
}
