<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 4/6/15
 * Time: 2:29 PM
 * To change this template use File | Settings | File Templates.
 */
namespace SilverStripers\News\Model;


use SilverStripe\GraphQL\Controller;
use SilverStripe\ORM\DataObject;
use SilverStripers\News\Pages\NewsIndex;

class NewsCategory extends DataObject
{

    private static $db = array(
        'Title'            => 'Varchar(200)',
        'SortOrder'        => 'Int'
    );

    private static $default_sort = 'SortOrder';

    public function Link()
    {
        $page = NewsIndex::get()->first();
        return $page ? $page->Link('category/' . $this->ID) : '';
    }

    public function IsActive()
    {
        if(Controller::has_curr()) {
            $controller = Controller::curr();
            if(is_a($controller, 'NewsIndex_Controller') && $controller->IsCategory()) {
                return $controller->getRequest()->param('ID') == $this->ID;
            }
        }
        return false;
    }
}
