<?php
namespace AppZz\CLI;
use AppZz\Helpers\Arr;

class Notifier {

	private $_bin = '/usr/local/bin/terminal-notifier';

    /**
     * Allowed methods
     * @var array
     */
    private $_methods = [
        'title',
        'subtitle',
        'message',
        'sound',
        'group',
        'activate',
        'sender',
        'icon',
        'image',
        'open',
        'timeout',
        'execute',
        'close',
        'actions',
        'reply',
    ];

    private $_params;

	public function __construct ($bin = '')
	{
		if ($bin)
			$this->_bin = $bin;
	}

    public static function factory ($bin = '')
    {
        return new Notifier ();
    }

    /**
     * Set params avoid setters
     * @param  array  $params
     * @return $this
     */
    public function params (array $params = [])
    {
        $this->_params = $params;
        return $this;
    }

    public function __call ($method, $params)
    {
        if ( ! in_array ($method, $this->_methods)) {
            throw new \Exception ('Wrong method: ' . $method);
        }

        $value = Arr::get($params, 0);

        switch ($method) {
            case 'actions':
                $this->_params['dropdownLabel'] = $value;
                $value = implode (',', (array) Arr::get($params, 1));
            break;

            case 'icon':
            	$method = 'appIcon';
            break;

            case 'image':
            	$method = 'contentImage';
            break;

            case 'close':
            	$method = 'closeLabel';
            break;
        }

        $this->_params[$method] = $value;

        return $this;
    }

    public function send ($json = FALSE)
    {
    	$cmd = $this->_bin;

    	if ($json)
    		$this->_params['json'] = '';

    	foreach ($this->_params as $key => $value) {
    		$cmd .= sprintf (' -%s %s', $key, (! empty ($value) ? escapeshellarg($value) : ''));
    		$cmd = rtrim ($cmd);
    	}

    	$result = shell_exec ($cmd);
    	return !empty ($result) ? json_decode($result, TRUE) : TRUE;
    }

}
