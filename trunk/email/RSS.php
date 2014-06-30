<?php

require_once dirname(__FILE__) .  '/vendors/simplepie_1.3.1/php/autoloader.php';

abstract class RSS
{
    protected $simplePie = null;

    public function __construct($rssUrl)
    {
        $this->simplePie = new SimplePie();
        $this->simplePie->set_feed_url($rssUrl);
        $this->simplePie->set_cache_location(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'RSS' . DIRECTORY_SEPARATOR . 'cache');
        $inited = $this->simplePie->init();

        if (!$inited) {
            $this->simplePie == null;
        }
    }
    //--------------------------------------------------------------------------


    public function isError()
    {
        if (is_null($this->simplePie) || $this->simplePie->error()) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------------------------------------


    public function getTotalPost()
    {
        if (!$this->isError()) {
            return $this->simplePie->get_item_quantity();
        }

        return 0;
    }
    //--------------------------------------------------------------------------


    public function getFeedTitle()
    {
        if (!$this->isError()) {
            return $this->simplePie->get_title();
        }

        return null;
    }
    //--------------------------------------------------------------------------


    public function getPostTitle($index, $alternateTag = null)
    {
        if ($this->isError()) {
            return null;
        }

        $title = null;

        if ($index >= 0 && $index < $this->getTotalPost()) {
            $post = $this->simplePie->get_item($index);
            $title = $post->get_title();
        }

        if (is_null($title) && !is_null($alternateTag)) {
            $post = $this->simplePie->get_item($index);
            $title = $this->getPostTag($post, $alternateTag);
        }

        return $title;
    }
    //--------------------------------------------------------------------------


    public function getPostDescription($index, $alternateTag = null)
    {
        if ($this->isError()) {
            return null;
        }

        $description = null;

        if ($index >= 0 && $index < $this->getTotalPost()) {
            $post = $this->simplePie->get_item($index);
            $description = $post->get_description();
        }

        if (is_null($description) && !is_null($alternateTag)) {
            $post = $this->simplePie->get_item($index);
            $description = $this->getPostTag($post, $alternateTag);
        }

        return $description;
    }
    //--------------------------------------------------------------------------


    public function getPostPermalink($index, $alternateTag = null)
    {
        if ($this->isError()) {
            return null;
        }

        $link = null;

        if ($index >= 0 && $index < $this->getTotalPost()) {
            $post = $this->simplePie->get_item($index);
            $link = $post->get_permalink();
        }

        if (is_null($link) && !is_null($alternateTag)) {
            $post = $this->simplePie->get_item($index);
            $link = $this->getPostTag($post, $alternateTag);
        }

        return $link;
    }
    //--------------------------------------------------------------------------


    public function getPostId($index, $alternateTag = null)
    {
        if ($this->isError()) {
            return null;
        }

        $id = null;

        if ($index >= 0 && $index < $this->getTotalPost()) {
            $post = $this->simplePie->get_item($index);
            $id = $post->get_id();
        }

        if (is_null($id) && !is_null($alternateTag)) {
            $post = $this->simplePie->get_item($index);
            $id = $this->getPostTag($post, $alternateTag);
        }

        return $id;
    }
    //--------------------------------------------------------------------------


    public function getPostPublicDate($index, $alternateTag = null)
    {
        if ($this->isError()) {
            return null;
        }

        $date = null;

        if ($index >= 0 && $index < $this->getTotalPost()) {
            $post = $this->simplePie->get_item($index);
            $date = $post->get_gmdate('j F Y, H:i:s');
        }

        if (is_null($date) && !is_null($alternateTag)) {
            $post = $this->simplePie->get_item($index);
            $date = $this->getPostTag($post, $alternateTag);
        }

        return $date;
    }
    //--------------------------------------------------------------------------


    private function getPostTag($post, $tag)
    {
        $tags = $post->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, $tag);

        if (!empty($tags)) {
            return $tags[0]['data'];
        }

        return null;
    }
    //--------------------------------------------------------------------------
}