<?php

namespace App\Controller;

use App\Entity\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticUrlController extends AbstractController
{
    /**
     * @Route("/statistic/url-hash/{hash}", name="statistic_url")
     */
    public function index(string $hash): Response
    {
        $url = $this->getDoctrine()->getRepository(Url::class)->findOneBy(compact('hash'));
        if (empty($url)) {
            return $this->json([
                'error' => 'Hash not found',
            ], 404);
        }

        $clicks = $url->getStatistics()->count();

        return $this->json([
            'clicks' => $clicks,
        ]);
    }

    /**
     * @Route("/statistic/url-unique-between/{startDate}/{endDate}", name="statistic_between")
     */
    public function statisticBetweenUniqueURL($startDate, $endDate): Response
    {
        $urls = $this->getDoctrine()->getRepository(Url::class)->findBetweenUniqueUrl($startDate, $endDate);

        return $this->json(compact('urls'));
    }

    /**
     * @Route("/statistic/url-unique/{url}", name="statistic_unique")
     */
    public function statisticUniqueURL(string $url): Response
    {
        $urls = $this->getDoctrine()->getRepository(Url::class)->findUniqueUrl($url);

        return $this->json(compact('urls'));
    }
}
