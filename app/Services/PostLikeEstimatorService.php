<?php

namespace App\Services;

use App\Models\Post;
use Carbon\Carbon;
use Phpml\Regression\SVR;

class PostLikeEstimatorService
{
    private Post $post;

    function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function predict(): float|int
    {
        try {
            $dataset = $this->constructTrainingData();
            $allKeywords = $this->getAllKeywords($dataset);
            $labelsAndFeatures = $this->groupIntoFeaturesWithLabels($dataset, $allKeywords);

            $regressionModel = new SVR();

            // In a production environment, you would only read the model in here.
            // Training will happen periodically through a job or some other async process
            // For simplicity, training is done before every prediction
            $regressionModel->train($labelsAndFeatures['features'], $labelsAndFeatures['labels']);

            $newPostFeatures = $this->mapPostFeatures($this->post->keywordArray(), $allKeywords);
            return floor($regressionModel->predict([$newPostFeatures])[0] ?? 0);
        }catch (\Exception $exception){
            return 0;
        }
    }

    private function mapPostFeatures(array $keywords, array $allKeywords): array
    {
        $features = [];
        foreach ($allKeywords as $keyword) {
            // Create a feature array by simply indicating if the keyword exists in the post keywords or not
            $features[] = in_array($keyword, $keywords) ? 1 : 0;
        }

        return $features;
    }

    private function constructTrainingData(): array
    {
        $trainingData = [];
        Post::select(['id', 'keywords'])->withCount('likes')
            ->where('created_at', '<=', Carbon::now())
            ->where('id', '!=', $this->post->id)
            ->each(function (Post $post) use (&$trainingData) {
                $trainingData[] = [
                    'keywords' => $post->keywordArray(),
                    'likes'    => $post->likes_count,
                ];
            });


        return $trainingData;
    }

    private function groupIntoFeaturesWithLabels(array $dataset, array $allKeywords): array
    {
        $labelsAndFeatures = ['labels' => [], 'features' => []];
        foreach ($dataset as $data) {
            $labelsAndFeatures['features'][] = $this->mapPostFeatures($data['keywords'], $allKeywords);
            $labelsAndFeatures['labels'][] = $data['likes'];
        }

        return $labelsAndFeatures;
    }

    private function getAllKeywords(array $dataset): array
    {
        // Get the keywords of each post in an array
        $keywordsArray = array_column($dataset, 'keywords');

        // merge into a single array
        $mergedKeywordArray = array_merge(...$keywordsArray);

        // return only unique keywords
        return array_unique($mergedKeywordArray);

    }
}
