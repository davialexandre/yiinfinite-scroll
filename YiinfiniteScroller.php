<?php

/**
 * This extension uses the infinite scroll jQuery plugin, from
 * http://www.infinite-scroll.com/ to create an infinite scrolling pagination,
 * like in twitter.
 *
 * It uses javascript to load and parse the new pages, but gracefully degrade
 * in cases where javascript is disabled and the users will still be able to
 * access all the pages.
 *
 * @author davi_alexandre
 */
class YiinfiniteScroller extends CBasePager {

    public $contentSelector = '#content';
    public $navigationLinkText = 'next';
    public $contentLoadedCallback = null;

    private $_options = array(
        'loadingImg'            => null,
        'loadingText'           => null,
        'donetext'              => null,
        'itemSelector'          => null,
        'errorCallback'         => null,
    );

    private $_default_options = array(
        'navSelector'   => 'div.infinite_navigation',
        'nextSelector'  => 'div.infinite_navigation a:first',
        'bufferPx'      => '300',
    );

    public function init() {
        $this->getPages()->validateCurrentPage = false;
        parent::init();
    }

    public function run() {
        if($this->getPageCount() > 1) {
            $this->registerClientScript();
            $this->createInfiniteScrollScript();
            echo $this->renderNavigation();
        }

        if($this->currentPageDoesntExists()) {
            throw new CHttpException(404);
        }
    }

    public function __get($name) {
        if(array_key_exists($name, $this->_options)) {
            return $this->_options[$name];
        }

        return parent::__get($name);
    }

    public function __set($name, $value) {
        if(array_key_exists($name, $this->_options)) {
            return $this->_options[$name] = $value;
        }

        return parent::__set($name, $value);
    }

    public function registerClientScript() {
        $url = CHtml::asset(Yii::getPathOfAlias('ext.yiinfinite-scroll.assets').'/jquery.infinitescroll.min.js');
        Yii::app()->clientScript->registerScriptFile($url);
    }

    private function createInfiniteScrollScript() {
        $options = $this->buildInifiniteScrollOptions();
        $contentLoadedCallback = CJavascript::encode($this->contentLoadedCallback);
        Yii::app()->clientScript->registerScript(
            uniqid(),
            "$('{$this->contentSelector}').infinitescroll($options, $contentLoadedCallback);"
        );
    }

    private function buildInifiniteScrollOptions() {
        $options = array_merge($this->_options, $this->_default_options);
        $options = array_filter( $options );
        $options = CJavaScript::encode($options);
        return $options;
    }

    public function renderNavigation() {
        if($this->isntTheLastPage()) {
            $next_link = CHtml::link($this->navigationLinkText, $this->createPageUrl($this->getCurrentPage() + 1));
            return '<div class="infinite_navigation">'.$next_link.'</div>';
        }

        return '';
    }

    public function currentPageDoesntExists() {
        if($this->getPageCount() > 1) {
            return $this->getCurrentPage() >= $this->getPageCount();
        } else {
            return $this->getCurrentPage() > 0;
        }
    }

    private function isntTheLastPage()
    {
        return $this->getCurrentPage() < $this->getPageCount() - 1;
    }

}

?>
