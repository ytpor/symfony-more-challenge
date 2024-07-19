<?php

namespace App\Service;

use OpenCF\RecommenderService;
use App\Entity\Order;
use App\Entity\OrderItem;
use DateTime;

/**
 * Reference: https://github.com/phpjuice/opencf
 */

class RecommendService
{
    public const SCHEME_COSINE = 'cosine';
    public const SCHEME_WEIGHTED_COSINE = 'weighted_cosine';
    public const SCHEME_WEIGHTED_SLOPEONE = 'eighted_slopeone';

    public $scheme;
    // public $product;

    public function product()
    {
        $dataset = $this->dummyDataset();
        $recommenderService = new RecommenderService($dataset);

        switch ($this->scheme) {
            case self::SCHEME_WEIGHTED_COSINE:
                $recommender = $recommenderService->weightedCosine();
                break;
            case self::SCHEME_COSINE:
                $recommender = $recommenderService->cosine();
                break;
            case self::SCHEME_WEIGHTED_SLOPEONE:
            default:
                $recommender = $recommenderService->weightedSlopeone();
        }

        // Predict future ratings
        $results = $recommender->predict([
            "brownie" => 0.4,
            "flower" => 0.4,
        ]);

        return $results;
    }

    /**
     * This is a dummy dataset
     */
    public function dummyDataset()
    {
        return [
            "flower" => [
                "user1" => 1,
                "user2" => 1,
                "user3" => 0.2,
            ],
            "cake" => [
                "user1" => 0.5,
                "user3" => 0.4,
                "user4" => 0.9,
            ],
            "cookie" => [
                "user1" => 0.2,
                "user2" => 0.5,
                "user3" => 1,
                "user4" => 0.4,
            ],
            "brownie" => [
                "user2" => 0.2,
                "user3" => 0.4,
                "user4" => 0.5,
            ],
            "donut" => [
                "user2" => 0.2,
                "user3" => 0.6,
                "user4" => 0.8,
            ],
            "scone" => [
                "user2" => 1,
                "user2" => 0.8,
                "user3" => 0.2,
                "user4" => 0.1,
            ],
        ];
    }
}
