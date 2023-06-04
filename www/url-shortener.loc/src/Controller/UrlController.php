<?php

namespace App\Controller;

use App\Entity\Statistic;
use App\Entity\Url;
use App\Repository\UrlRepository;
use Cassandra\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UrlController extends AbstractController
{
    /**
     * MAX_LIVE_URL in Day
     */
    const MAX_LIVE_URL = 1;

    /**
     * @Route("/encode-url", name="encode_url")
     */
    public function encodeUrl(Request $request): JsonResponse
    {
        $address = $request->get('url');
        $criteria = ['url' => $address];

        if (count($addressAndDate = explode('/', $address)) == 2) {
            $criteria['url'] = $addressAndDate[0];
            $criteria['createdDate'] = new \DateTimeImmutable($addressAndDate[1]);
        }

        $findURL = $this->getDoctrine()->getRepository(Url::class)->findOneBy($criteria);
        if (!empty($findURL)) {
            return $this->json([
                'hash' => $findURL->getHash(),
            ]);
        }

        $url = new Url();
        $url->setCreatedDateAndUrl($criteria);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($url);
        $entityManager->flush();

        return $this->json([
            'hash' => $url->getHash(),
        ]);
    }

    /**
     * @Route("/decode-url", name="decode_url")
     */
    public function decodeUrl(Request $request): JsonResponse
    {
        $url = $this->serviceDecodeUrl($request->get('hash'));
        if (empty ($url)) {
            return $this->json([
                'error' => 'Non-existent hash.',
            ]);
        }

        return $this->json([
            'url' => $url->getUrl(),
        ]);
    }

    /**
     * @Route("/gourl/{hash}", name="goURL")
     */
    public function goUrl(string $hash)
    {
        $address = $this->serviceDecodeUrl($hash);

        if (empty($address)) {
            return $this->json(['error' => 'Not-existent hash']);
        }

        $live = $address->getCreatedDate()->add(new \DateInterval('P'.self::MAX_LIVE_URL.'D'));
        if ((strtotime($live->format('Y-m-d H:i:s')) - strtotime('now')) < 0) {
            return $this->json(['error' => 'Time hash code died']);
        }

        $statistic = new Statistic();
        $statistic->setUrl($address);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($statistic);
        $entityManager->flush();
        
        $words = [
            'http://',
            'https://',
        ];

        $url = 'http://'.str_replace($words, '', $address->getUrl());

        return $this->redirect($url);
    }

    /**
     * @param  string  $hash
     * @return Url|null
     */
    public function serviceDecodeUrl(string $hash): ?Url
    {
        /** @var UrlRepository $urlRepository */
        $urlRepository = $this->getDoctrine()->getRepository(Url::class);

        return $urlRepository->findOneByHash($hash);
    }
}
