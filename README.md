Yii-infinte-scroll-2
=====================

This is an updated extension of https://github.com/davialexandre/yiinfinite-scroll 

This extension uses the infinite scroll jQuery plugin, from https://github.com/paulirish/infinite-scroll to create an infinite scrolling pagination, like in twitter. This kind of pagination is also called Endless Scroll.

It uses javascript to load and parse the new pages, but gracefully degrade in cases where javascript is disabled and the users will still be able to access all the pages.

##Requirements

1. https://github.com/yiisoft/yii - 1.1.4 +
2. https://github.com/jquery/jquery ~ 1.7.2 
3. https://github.com/paulirish/infinite-scroll - 2.0

##Usage

These are the properties that can be set for YiinfiniteScroller :
	
	array(
	'loading' => array( // the loading object consisting of 
        'finished' => undefined ,
        'finishedMsg' => "<em>Congratulations, you've reached the end of the internet.</em>", // the message to show at the end of the list,
        'img' =>  // image/gif that should be shown while loading,
        'msg' => null,
        'msgText' => "<em>Loading the next set of posts...</em>", // the message to be shown during loading,
        'selector'=> null, // To which jquery selector should the loading object be attached,
        'speed' => 'fast' // the speed at which loading happens,
        'start'=> undefined,
	),
	'state' => array (  // Different States the plugin can be in returns true for corresponding state.
        'isDuringAjax' => false,
        'isInvalidPage' => false,
        'isDestroyed' => false,
        'isDone'  => false, // For when it goes all the way through the archive.
        'isPaused' => false,
        'currPage' => 1,
	 ),
    'debug' => false, // If set to true, prints debug output to console.log
    'behavior' => undefined,
    'binder' => $(window), // used to cache the selector for the element that will be scrolling
    'contentSelector' => null, // the jquery selector of the content element.
    'extraScrollPx' => 150,
    'itemSelector' => "div.post", // Which item to generate on scroll
    'animate' =>false,
    'pathParse' => undefined,
    'dataType' => 'html', // Whether html or JSON type of data
    'appendCallback' => true //Callback function to append
    'bufferPx' => 40;
    'errorCallback' => 'function () { }',
    'infid' => 0 // ID of the instance of the infinite-scroll plugin
    'pixelsFromNavToBottom' => undefined,
    'path'  => undefined, // Can either be an array of URL parts (e.g. ["/page/", "/"]) or a function that accepts the pge number and returns a URL
    'prefill' => false,  // When the document is smaller than the window, load data until the document is larger or links are exhausted
    'maxPage'=> undefined,   // To manually control maximum page (when maxPage is undefined, maximum page limitation is not work)
	);


The YiinfiniteScroller class extends the CBasePager class, so you will use it the same way as CLinkPager and CListPager.

On your controller you need to create the CPagination class which controls the pages handling:

	class PostController extends Controller
	{
		public function actionIndex()
		{
	            $criteria = new CDbCriteria;
	            $total = Post::model()->count();
	
	            $pages = new CPagination($total);
	            $pages->pageSize = 20;
	            $pages->applyLimit($criteria);
	
	            $posts = Post::model()->findAll($criteria);
	
		    $this->render('index', array(
	                'posts' => $posts,
	                'pages' => $pages,
	            ));
		}
	}

Now on your view you will use it as a widget, like in the following sample:

	$this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'itemSelector' => 'div.post',
	    'pages' => $pages,
	));

Note that this will only output the necessary code to the pager. **It will not render the page items**. Since YiinfiniteScroller extends CBasePager, everything works exactly the same way as if you were using the CListPager or CLinkPager. So, you're need to manually render the items. 

In this example, the items are stored in the $posts variable. In our view, we can render the posts by using a simple foreach loop, like in the example bellow:

	<?php foreach($posts as $post): ?>
	    <div class="post">
	        <p>Autor: <?php echo $post->author; ?></p>
	        <p><?php echo $post->text; ?></p>
	    </div>
	<?php endforeach; ?>

This is how the complete view file will look like:

	<div id="posts">
	<?php foreach($posts as $post): ?>
	    <div class="post">
	        <p>Autor: <?php echo $post->author; ?></p>
	        <p><?php echo $post->text; ?></p>
	    </div>
	<?php endforeach; ?>
	</div>
	<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'contentSelector' => '#posts',
	    'itemSelector' => 'div.post',
	    'pages' => $pages,
	)); ?>

