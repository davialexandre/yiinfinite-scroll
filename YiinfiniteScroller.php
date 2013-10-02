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

    private $_options = array(
        'loading'=> array(
            'finished'=> null,
            'finishedMsg'=> null,
            'img'=> null,
            'msg'=> null,
            'msgText'=> null,
            'selector'=> null,
            'speed'=> null,
            'start'=> null,
        ),
        'state'=> array (
            'isDuringAjax'=> null,
            'isInvalidPage'=> null,
            'isDestroyed'=> null,
            'isDone'=> null, 
            'isPaused'=> null,
            'currPage'=> null,
        ),
        'debug'=> null,
        'behavior'=> null,
        'binder'=> null,
        'contentSelector'=> null,
        'extraScrollPx'=> null,
        'itemSelector'=> null,
        'animate'=> null,
        'pathParse'=> null,
        'dataType'=> null,
        'appendCallback'=> null,
        'bufferPx'=> null,
        'errorCallback'=> null,
        'infid'=> null,
        'pixelsFromNavToBottom'=> null,
        'path'=> null, 
        'prefill'=> null,
        'maxPage'=>null,
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
        $this->registerClientScript();
        $this->createInfiniteScrollScript();
        $this->renderNavigation();

        if($this->getPages()->getPageCount() > 0 && $this->theresNoMorePages()) {
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
        Yii::app()->clientScript->registerScript(
                uniqid(),
                "$('{$this->contentSelector}').infinitescroll(".$this->buildInifiniteScrollOptions().");"
        );
    }

    private function buildInifiniteScrollOptions() {
        $options = array_merge($this->_options, $this->_default_options);
        $options = array_filter( $options );
        $options = CJavaScript::encode($options);
        return $options;
    }

    private function renderNavigation() {
        $next_link = CHtml::link('next',$this->createPageUrl($this->getCurrentPage(false)+1));
        echo '<div class="infinite_navigation">'.$next_link.'</div>';
    }

    private function theresNoMorePages() {
        return $this->getPages()->getCurrentPage() >= $this->getPages()->getPageCount();
    }

}

?>
