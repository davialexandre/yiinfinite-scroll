This extension uses the infinite scroll jQuery plugin, from http://www.infinite-scroll.com/ to create an infinite scrolling pagination, like in twitter. This kind of pagination is also called Endless Scroll.

It uses javascript to load and parse the new pages, but gracefully degrade in cases where javascript is disabled and the users will still be able to access all the pages.

##Requirements

Yii 1.1.4

##Usage

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
	    'loadingText' => 'Loading...',
	    'donetext' => 'This is the end... my only friend, the end',
	    'pages' => $pages,
	)); ?>



There are a few properties that can be set for YiinfiniteScroller:

- contentSelector: The jQuery selector of the container element where the loaded items (from itemSelector) will be appended
- loadingText: The text to be displayed and a new page is being loaded
- donetext: The text to be displayed when all the pages had been loaded
- pages:  The CPagination object
- errorCallback: Javascript function that is called when there are ajax errors or 404s.
- contentLoadedCallback: A Javascript function that is called when new itens are loaded. In its scope, this will be the DOM Element just loaded.
- navigationLinkText: The text to be displayed in the navigation link (Usually visible only when javascript is disabled). The default value is "next".

##Changelog
- Release 1.3 (09/02/2014)
    - Added the contentLoadedCallback
    - Added tests!
    - Fixed bug that loaded the last page twice
    - Removed the navigation link in the last page. That link was point to a non existent page.

- Release 1.2 (11/11/2013)
    - Added the navigationLinkText property
    - Fixed problems when there were 0 or 1 pages to be paged
