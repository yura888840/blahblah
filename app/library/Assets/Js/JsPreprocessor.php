<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 01.04.15
 * Time: 13:53
 */

namespace Crm\Assets\Js;

use Phalcon\Assets\FilterInterface;

/**
 * Pre- processing js files
 *
 * @param string $contents
 * @return string
 */
class JsPreprocessor implements FilterInterface
{

    protected $_options;

    const OPEN_TAG = '{{';

    const CLOSE_TAG = '}}';

    /**
     * JsPreprocessor constructor
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->_options = $options;
    }

    /**
     * Do the filtering
     *
     * @param string $contents
     * @return string
     */
    public function filter($contents)
    {

        foreach($this->_options as $k => $v)
        {
            $contents = str_replace(self::OPEN_TAG . rtrim(ltrim($k)) . self::CLOSE_TAG, $v, $contents);
        }

        return $contents;
    }
}