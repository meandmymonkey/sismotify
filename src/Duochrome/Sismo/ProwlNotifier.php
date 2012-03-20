<?php

namespace Duochrome\Sismo;

use Sismo\Notifier;
use Sismo\Commit;
use Buzz\Message\Request;
use Buzz\Browser;

class ProwlNotifier extends Notifier
{
    private $apiKeys;

    /**
     * @param string|array $apiKeys
     */
    public function __construct($apiKeys, $options = array())
    {
        if (!is_array($apiKeys)) {
            $apiKeys = (array) $apiKeys;
        }

        $this->apiKeys = $apiKeys;
    }

    public function notify(Commit $commit)
    {
        $browser = new Browser();
        $response = $browser->submit(
            'https://api.prowlapp.com/publicapi/add',
            array(
                'apikey'      => implode(',', $this->apiKeys),
                'application' => 'Sismo',
                'description' => $this->formatMessage($commit),
                'event'       => $commit->isSuccessful() ? 'Build successful' : 'Build failed',
                'url'         => ''
            ),
            Request::METHOD_POST
        );

        return $response->getStatusCode() === 200;
    }

    protected function formatMessage(Commit $commit)
    {

    }

    protected function getDefaultOptions()
    {
        return array(
            'url_template'   => null,
            'message_format' => '{{ description }}',
            'title_pass'     => 'Build successful',
            'title_fail'     => 'Build failed',
            'application'    => 'Sismo',
            'browser'        => new Buzz\Browser()
        );
    }
}
