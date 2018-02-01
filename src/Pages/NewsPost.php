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
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;

class NewsPost extends Page
{

    private static $pages_admin = true;

    private static $db = array(
        'DateTime'          => 'SS_Datetime',
        'Tags'              => 'Varchar(500)',
        'Author'            => 'Varchar(100)',
        'Summary'           => 'HTMLText'
    );


    private static $many_many = array(
        'Categories'        => 'NewsCategory',
        'RelatedArticles'   => 'NewsPost'
    );

    private static $icon = 'silverstripe-news/images/NewsPost.png';


    private static $table_name = 'NewsPost';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if (!Config::inst()->get(NewsPost::class, 'pages_admin')) {
            $arrTypes = NewsPost::GetNewsTypes();
            if (count($arrTypes) > 1) {
                $arrDropDownSource = array();
                foreach ($arrTypes as $strType) {
                    $arrDropDownSource[$strType] = $strType;
                }
                $fields->addFieldToTab('Root.Main',
                    DropdownField::create('ClassName')->setSource($arrDropDownSource)
                        ->setTitle('Type'),
                    'Content');
            }
        }

        $fields->addFieldsToTab('Root.Main',
            [
                DropdownField::create('ParentID')->setSource(NewsIndex::get()->map()->toArray())->setTitle('Parent Page'),
                DatetimeField::create('DateTime'),
                TextField::create('Tags'),
                TextField::create('Author'),
                HTMLEditorField::create('Summary')->setRows(5)
            ],
            'Content');


        if ($this->ID) {
            $fields->addFieldToTab('Root.Main',
                CheckboxSetField::create('Categories')->setSource(NewsCategory::get()->map('ID', 'Title')->toArray()),
            'Content');


            $fields->addFieldToTab('Root.RelatedArticles', GridField::create('RelatedArticles', 'Related Articles')->setList($this->RelatedArticles())
                ->setConfig($relatedArticlesConfig = new GridFieldConfig_RelationEditor()));

        }



        $this->extend('updateNewsPostCMSFields', $fields);

        return $fields;
    }

    public static function GetNewsTypes()
    {
        return ClassInfo::subclassesFor('NewsPost');
    }


    /**
     * @return ArrayList
     * return a list of tags as a list of ArrayData's
     */
    public function TagList()
    {
        $list = new ArrayList();
        $newsIndex = $this->Parent();
        $tags = trim($this->Tags);
        if($tags) {
            foreach (explode(',', $tags) as $tag) {
                $tag = trim($tag);
                $list->push(new ArrayData(array(
                    'Tag' => $tag,
                    'Link' => $newsIndex->Link('tag/' . urlencode($tag))
                )));
            }
        }

        return $list;
    }


    public function ExportContent()
    {
        if(method_exists($this, 'customExportContent')) {
            return $this->customExportContent();
        }
        return $this->Content;
    }

    public function PrevNewsItem()
    {
        return NewsPost::get()->filter(array(
            'DateTime:LessThanOrEqual'      => $this->DateTime
        ))->exclude('ID', $this->ID)->sort('DateTime DESC')->first();
    }

    public function NextNewsItem()
    {
        return NewsPost::get()->filter(array(
            'DateTime:GreaterThanOrEqual'      => $this->DateTime
        ))->exclude('ID', $this->ID)->sort('DateTime')->first();
    }


}


