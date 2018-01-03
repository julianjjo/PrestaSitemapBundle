<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Sitemap\Url;

use Presta\SitemapBundle\Exception;

/**
 * Decorate url with images
 *
 * @see http://support.google.com/webmasters/bin/answer.py?hl=en&answer=178636&topic=20986&ctx=topic
 *
 * @author David Epely
 */
class GoogleVideosUrlDecorator extends UrlDecorator
{
    const LIMIT_ITEMS = 1000;

    protected $videoXml = '';
    protected $customNamespaces = array('video' => 'http://www.google.com/schemas/sitemap-video/1.1');
    protected $limitItemsReached = false;
    protected $countItems = 0;

    public function addVideo(GoogleVideo $video)
    {
        if ($this->isFull()) {
            throw new Exception\GoogleImageUrlDecorator('The image limit has been exceeded');
        }

        $this->videoXml .= $video->toXml();

        //---------------------
        //Check limits
        if ($this->countItems++ >= self::LIMIT_ITEMS) {
            $this->limitItemsReached = true;
        }
        //---------------------
        return $this;
    }

    /**
     * add image elements before the closing tag
     *
     * @return string
     */
    public function toXml()
    {
        $baseXml = $this->urlDecorated->toXml();
        return str_replace('</url>', $this->videoXml . '</url>', $baseXml);
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        return $this->limitItemsReached;
    }
}
