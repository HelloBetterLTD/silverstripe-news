<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 4/6/15
 * Time: 2:01 PM
 * To change this template use File | Settings | File Templates.
 */
namespace SilverStripers\News\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Versioned\Versioned;
use SilverStripers\News\Extensions\NewsSearchContext;
use SilverStripers\News\Model\NewsCategory;
use SilverStripers\News\Pages\NewsPost;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class NewsAdmin extends ModelAdmin
{

    private static $url_segment = 'news';
    private static $menu_title = 'News';

    private static $menu_icon_class = 'font-icon-book';

    public $showImportForm = false;

    private static $managed_models = [
        NewsPost::class,
        NewsCategory::class
    ];

    private static $exclude_classes = [

    ];


    public function init()
    {
        Versioned::set_reading_mode('stage');
        Config::inst()->update(NewsPost::class, 'pages_admin', false);
        parent::init();
    }

    public function getSearchableClasses()
    {
        $arrRet = array();
        $arrClasses = ClassInfo::subclassesFor(NewsPost::class);
        $arrExclude = self::config()->get('exclude_classes');
        if (!empty($arrExclude)) {
            foreach ($arrClasses as $strClass) {
                if (!in_array($strClass, $arrExclude)) {
                    $arrRet[] = $strClass;
                }
            }
        } else {
            $arrRet = $arrClasses;
        }
        return $arrRet;
    }


    public function getSearchContext()
    {
        if ($this->IsEditingNews()) {
            $context = new NewsSearchContext($this->modelClass, $this);
            foreach ($context->getFields() as $field) {
                $field->setName(sprintf('q[%s]', $field->getName()));
            }

            foreach ($context->getFilters() as $filter) {
                $filter->setFullName(sprintf('q[%s]', $filter->getFullName()));
            }

            $this->extend('updateSearchContext', $context);
            return $context;
        }

        return parent::getSearchContext();
    }



    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        $field = $form->Fields()->dataFieldByName($this->modelClass);
        if ($field) {
            $config = $field->getConfig();
            //if (!ClassInfo::exists('GridFieldBetterButtonsItemRequest') && $this->IsEditingNews()) {
//                $config->getComponentByType(GridFieldDetailForm::class)
//                    ->setItemRequestClass('NewsGridFieldDetailForm_ItemRequest');
            //}

            $singleton = singleton($this->modelClass);
            if (is_a($singleton, NewsPost::class)) {
                $config->addComponent(new GridFieldOrderableRows('Sort'));

                $exportButton = $config->getComponentByType(GridFieldExportButton::class);
                if($exportButton) {
                    $export = array(
                        'Title'         => 'Title',
                        'DateTime'      => 'DateTime',
                        'Author'        => 'Author',
                        'ExportContent' => 'Content'
                    );

                    $this->extend('updateExportColumn', $export);
                    $exportButton->setExportColumns($export);
                }


            }

            $config->removeComponentsByType(GridFieldDeleteAction::class);
            $config->removeComponentsByType(GridFieldPaginator::class);

            $config->addComponent($pagination = new GridFieldPaginator(100));
        }

        return $form;
    }


    public function getList()
    {
        $list = parent::getList();
        if ($this->IsEditingNews()) {
            $list = $list->sort('DateTime DESC')->filter('ClassName', $this->getSearchableClasses());
        }

        $this->extend('updateNewsList', $list);

        return $list;
    }


    public function IsEditingNews()
    {
        return in_array($this->modelClass, ClassInfo::subclassesFor(NewsPost::class));
    }
}
