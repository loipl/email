<?php

$skipOtherTests = 1;
defined('RUN_ALL_TESTS') or require_once '../tests.php';

class TestRssSimplepie extends UnitTestCase
{

    public function testInvalidRssSource()
    {
        $rss = new Vendor_SimplePie('http://www.example.invalid.com');
        $this->assertTrue($rss->isError());
        $this->assertTrue($rss->getTotalPost() == 0);
        $this->assertTrue($rss->getFeedTitle() == null);
        $this->assertTrue($rss->getPostTitle(0) == null);
        $this->assertTrue($rss->getPostTitle(0, 'alterTag') == null);
        $this->assertTrue($rss->getPostDescription(0) == null);
        $this->assertTrue($rss->getPostDescription(0, 'title') == null);
        $this->assertTrue($rss->getPostId(0) == null);
        $this->assertTrue($rss->getPostId(0, 'uuid') == null);
        $this->assertTrue($rss->getPostPermalink(0) == null);
        $this->assertTrue($rss->getPostPermalink(0, 'link') == null);
        $this->assertTrue($rss->getPostPublicDate(0) == null);
        $this->assertTrue($rss->getPostPublicDate(0, 'pDate') == null);
    }
    //--------------------------------------------------------------------------


    public function testValidRssSource()
    {
        $rss = new Vendor_SimplePie('http://us.battle.net/d3/en/feed/news');
        $this->assertFalse($rss->isError());
    }
    //--------------------------------------------------------------------------


    public function testGetTotalPost()
    {
        $rss = new Vendor_SimplePie('http://www.miniclip.com/games/en/feed.xml');
        $this->assertTrue($rss->getTotalPost() >= 0);
    }
    //--------------------------------------------------------------------------


    public function testGetFeedTitle() {
        $rss = new Vendor_SimplePie('http://feeds.armorgames.com/armorgames');
        $this->assertTrue($rss->getFeedTitle() != null);
    }
    //--------------------------------------------------------------------------


    public function testGetPostTitle() {
        $rss = new Vendor_SimplePie('http://www.candystand.com/rss/topgames');
        $totalPost = $rss->getTotalPost();
        $randomIndex = $totalPost == 0 ? -1 : rand(0, $totalPost - 1);
        $this->assertTrue($rss->getPostTitle($randomIndex) != null);
    }
    //--------------------------------------------------------------------------


    public function testGetPostDescription() {
        $rss = new Vendor_SimplePie('http://www.gamesgames.com/rss/popular.xml');
        $totalPost = $rss->getTotalPost();
        $randomIndex = $totalPost == 0 ? -1 : rand(0, $totalPost - 1);
        $this->assertTrue($rss->getPostDescription($randomIndex) != null);
    }
    //--------------------------------------------------------------------------


    public function testGetPostId() {
        $rss = new Vendor_SimplePie('http://www.rockstargames.com/newswire.rss');
        $totalPost = $rss->getTotalPost();
        $randomIndex = $totalPost == 0 ? -1 : rand(0, $totalPost - 1);
        $this->assertTrue($rss->getPostId($randomIndex) != null);
    }
    //--------------------------------------------------------------------------


    public function testGetPostLink() {
        $rss = new Vendor_SimplePie('http://www.greenheartgames.com/feed/');
        $totalPost = $rss->getTotalPost();
        $randomIndex = $totalPost == 0 ? -1 : rand(0, $totalPost - 1);
        $this->assertTrue($rss->getPostPermalink($randomIndex) != null);
    }
    //--------------------------------------------------------------------------


    public function testGetPublicDate() {
        $rss = new Vendor_SimplePie('http://ctrlq.org/rss/');
        $totalPost = $rss->getTotalPost();
        $randomIndex = $totalPost == 0 ? -1 : rand(0, $totalPost - 1);
        $this->assertTrue($rss->getPostPublicDate($randomIndex) != null);
    }
    //--------------------------------------------------------------------------


    public function testUsingAlterTag() {
        $rss = new Vendor_SimplePie('http://news.google.com/news?pz=1&cf=all&ned=us&hl=en&topic=h&num=3&output=rss');
        $totalPost = $rss->getTotalPost();
        $randomIndex = $totalPost == 0 ? -1 : rand(0, $totalPost - 1);

        $this->assertTrue($rss->getPostTitle($randomIndex, 'title') != null);
        $this->assertTrue($rss->getPostDescription($randomIndex, 'description') != null);
        $this->assertTrue($rss->getPostId($randomIndex, 'guid') != null);
        $this->assertTrue($rss->getPostPermalink($randomIndex, 'link') != null);
        $this->assertTrue($rss->getPostPublicDate($randomIndex, 'pubDate') != null);
    }
    //--------------------------------------------------------------------------
}