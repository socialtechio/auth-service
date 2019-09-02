<?php


namespace Services;


use SocialTech\SlowStorage;

class DataTracker
{
    protected $slowStorage;

    /**
     * DataTracker constructor.
     * @param SlowStorage $slowStorage
     */
    public function __construct(SlowStorage $slowStorage)
    {
        $this->slowStorage = $slowStorage;
    }

    /**
     * @param array $data
     * @return array
     */
    public function process(array $data): array
    {
        // TODO needs data validation
        $path = getenv('ANALYTICS_STORAGE_LOCATION');

        $trackingData = [
            'id' => uniqid('id', true),
            'id_user' => $data['id_user'] ?? 'unknown',
            'source_label' => $data['source_label'] ?? 'unknown',
            'date_created' => date('Y-m-d H:i:s'),
        ];

        $this->slowStorage->store($path, json_encode($trackingData));

        return $trackingData;
    }
}