<?php
namespace Ec\Qr\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Api extends AbstractHelper
{

    const API_URL = 'emotionalcommerce.com';

    /**
     * ConfigFactory
     *
     * @var \Ec\Qr\Model\ConfigFactory
     */
    protected $configFactory;

    public function __construct(
        \Ec\Qr\Model\ConfigFactory $configFactory
    ) {
        $this->configFactory = $configFactory;
    }

    public function getCampaigns($key, $secret, $domain)
    {
        $headers =  array(
          'Accept: application/json',
          'Content-Type: application/json',
          'key: '.$key,
          'secret: '.$secret,
        );

        $regUrl = $domain. '.' . self::API_URL."/api/campaigns";

        $regCurl = curl_init($regUrl);
        curl_setopt($regCurl, CURLOPT_POST, 0);
        curl_setopt($regCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
          $regCurl,
          CURLOPT_HTTPHEADER,
          $headers
        );
        $regResult = json_decode(curl_exec($regCurl));
        $statusCode = curl_getinfo($regCurl, CURLINFO_HTTP_CODE);
        curl_close($regCurl);

        if ($statusCode != 200) {
            return [
                'success' => false,
                'message' => $regResult->message,
            ];
        }

        $campaigns = [];

        foreach ($regResult->data as $campaign) {
            $campaigns[] = [
                'name' => $campaign->name,
                'id' => $campaign->id,
            ];
        }

        while ($regResult->next_page_url) {
            $regCurl = curl_init($regResult->next_page_url);
            curl_setopt($regCurl, CURLOPT_POST, 0);
            curl_setopt($regCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $regCurl,
                CURLOPT_HTTPHEADER,
                $headers
            );
            $regResult = json_decode(curl_exec($regCurl));

            foreach ($regResult->data as $campaign) {
                $campaigns[] = [
                    'name' => $campaign->name,
                    'id' => $campaign->id,
                ];
            }
            curl_close($regCurl);
        }

        return [
            'success' => true,
            'data' => $campaigns,
        ];

    }

    public function getConfig()
    {
        $configFactory = $this->configFactory->create();
        $collection = $configFactory->getCollection();

        $configData = [
            'key' => false,
            'secret' => false,
            'domain' => false,
            'campaign' => false,
            'template' => false,
            'price' => false,
            'width' => 720,
            'height' => 720,
            'title' => __('Add Video'),
            'subtitle' => false,
            'button-title' => __('Add Video'),
            'button-color' => false,
            'button-background' => false,
            'enabled' => 0,
            'qr-title' => __('Scan the QR to see the <br /> Emotional Message'),
        ];

        foreach ($collection as $config) {
            $configData[$config->getName()] = $config->getValue();
        }

        return $configData;
    }

    public function uploadVideo($filePath)
    {
        $config = $this->getConfig();

        $headers =  array(
            'Accept: application/json',
            'content-type: multipart/form-data',
            'key: '.$config['key'],
            'secret: '.$config['secret'],
        );

        $regUrl = $config['domain']. '.' . self::API_URL."/api/campaigns/video";

         $data = [
            'campaign' => $config['campaign'],
            'file' => curl_file_create($filePath),
        ];

        $regCurl = curl_init($regUrl);
        curl_setopt($regCurl, CURLOPT_POST, 1);
        curl_setopt($regCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($regCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $regCurl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        $regResult = json_decode(curl_exec($regCurl));
        $statusCode = curl_getinfo($regCurl, CURLINFO_HTTP_CODE);
        curl_close($regCurl);

        if ($statusCode != 200) {
            return [
                'success' => false,
                'message' => $regResult->message,
            ];
        }

        return [
            'success' => true,
            'data' => [
                'url' => $regResult->url,
                'qr' => $regResult->qr,
            ],
        ];

    }

    public function validateVideo($filePath)
    {
        $config = $this->getConfig();

        $headers =  array(
            'Accept: application/json',
            'content-type: multipart/form-data',
            'key: '.$config['key'],
            'secret: '.$config['secret'],
        );

        $regUrl = $config['domain']. '.' . self::API_URL."/api/campaigns/video/verify";

        $data = [
            'campaign' => $config['campaign'],
            'file' => curl_file_create($filePath),
        ];

        $regCurl = curl_init($regUrl);
        curl_setopt($regCurl, CURLOPT_POST, 1);
        curl_setopt($regCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($regCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $regCurl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        $regResult = json_decode(curl_exec($regCurl));
        $statusCode = curl_getinfo($regCurl, CURLINFO_HTTP_CODE);
        curl_close($regCurl);

        if ($statusCode != 200) {
            return [
                'success' => false,
                'message' => $regResult->message,
            ];
        }

        return [
            'success' => true,
        ];

    }

    public function createEvent($ecOrder)
    {
        $config = $this->getConfig();

        $headers =  array(
            'Accept: application/json',
            'content-type: multipart/form-data',
            'key: '.$config['key'],
            'secret: '.$config['secret'],
        );

        $slug = $ecOrder->getUrl();
        $slug = explode('/', $slug);
        $slug = end($slug);

        $regUrl = $config['domain']. '.' . self::API_URL."/api/event/create/" . $slug;

        $regCurl = curl_init($regUrl);
        curl_setopt($regCurl, CURLOPT_POST, 0);
        curl_setopt($regCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $regCurl,
            CURLOPT_HTTPHEADER,
            $headers
        );
        $regResult = json_decode(curl_exec($regCurl));
        $statusCode = curl_getinfo($regCurl, CURLINFO_HTTP_CODE);
        curl_close($regCurl);

        if ($statusCode != 200) {
            if ($statusCode == 409) {
                $ecOrder->setPrinted(1);
                $ecOrder->save();

                return [
                    'success' => true,
                ];
            }
            return [
                'success' => false,
                'message' => $regResult->message,
            ];
        }

        $ecOrder->setPrinted(1);
        $ecOrder->save();

        return [
            'success' => true,
        ];
    }

}
