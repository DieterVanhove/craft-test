<?php

namespace modules\magicline\controllers\web;

use craft\web\Controller;
use yii\web\Response;
use Craft;

class RobotsController extends Controller
{
    protected array|bool|int $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    public function actionIndex()
    {
        // Add sitemap directive based on the current site URL
        $baseUrl = Craft::$app->getRequest()->getHostInfo();

        if ($baseUrl === 'https://www.jims.lu') {
            $robotsContent = "Sitemap: {$baseUrl}/sitemaps-2-sitemap.xml\n";
        } elseif ($baseUrl === 'https://www.jims.be') {
            $robotsContent = "Sitemap: {$baseUrl}/nl/sitemaps-1-sitemap.xml\n";
            $robotsContent .= "Sitemap: {$baseUrl}/fr/sitemaps-1-sitemap.xml\n";
        } else {
            $robotsContent = "Sitemap: {$baseUrl}/sitemap.xml\n";
        }

        // Add disallow directives
        $robotsContent .= "\n";
        $robotsContent .= "User-agent: *\n";
        $robotsContent .= "Disallow: /cpresources/\n";
        $robotsContent .= "Disallow: /vendor/\n";
        $robotsContent .= "Disallow: /.env\n";
        $robotsContent .= "Disallow: /cache/\n";
        $robotsContent .= "Disallow: /checkout\n";

        // Create a response with plain text content
        $response = new Response([
            'format' => Response::FORMAT_RAW,
            'data' => $robotsContent,
        ]);

        // Set the content type header
        $response->headers->set('Content-Type', 'text/plain');
        $response->setStatusCode(200);

        return $response;
    }
}
