<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 4/6/15
 * Time: 2:00 PM
 * To change this template use File | Settings | File Templates.
 */
namespace SilverStripers\News\Pages;

use Page;
use SilverStripe\Control\Director;
use SilverStripe\Forms\TextField;

class NewsIndex extends Page
{

    private static $db = array(
        'ItemsPerPage'    => 'Int',
    );

    private static $table_name = 'NewsIndex';

    private static $description = 'News index, your news items will be added under this page.';

    private static $allowed_children = [
        NewsPost::class,
        BlogPage::class,
        LinkPost::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        $fields->addFieldToTab('Root.Main',
            TextField::create('ItemsPerPage')
                ->setTitle('Number of items per page'),
            'Metadata'
        );

        return $fields;
    }

    public function getNewsItemsEditLink()
    {
        return Director::baseURL() . '/admin/news?ParentID=' . $this->ID;
    }

    public function getTreeEditLinkText()
    {
        return 'Use News admin to manage pages of this tree';
    }
}

