<?php
/**
 * The control file of book module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     book
 * @version     $Id: control.php 3138 2015-11-09 07:32:18Z chujilu $
 * @link        http://www.ranzhico.com
 */
class book extends control
{
    /**
     * The default catalog counts when create. 
     */
    const NEW_CATALOG_COUNT = 5;

    /**
     * Index page, locate to browse default.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $book = $this->book->getFirstBook();
        $this->locate(inlink('browse', "nodeID=$book->id", "book=$book->alias"));
    }

    /**
     * Browse a node of a book.
     * 
     * @param  int    $nodeID 
     * @access public
     * @return void
     */
    public function browse($nodeID)
    {
        $node = $this->book->getNodeByID($nodeID);
        $book = $this->book->getBookByNode($node);

        $this->view->title    = $book->title;
        $this->view->keywords = trim($node->keywords);
        $this->view->node     = $node;
        $this->view->book     = $book;
        $this->view->books    = $this->book->getBookList();
        $this->view->catalog  = $this->book->getFrontCatalog($node->id, $this->book->computeSN($book->id));
        $this->display();
    }

    /**
     * Read an article.
     * 
     * @param  int    $articleID 
     * @access public
     * @return void
     */
    public function read($articleID)
    { 
        $article = $this->book->getNodeByID($articleID);
        $parent  = $article->origins[$article->parent];
        $book    = $article->book;
        $content = $this->book->addMenu($article->content);

        $this->view->title    = $article->title . ' - ' . $book->title;;
        $this->view->keywords = trim($article->keywords);
        $this->view->desc     = trim($article->summary);
        $this->view->article  = $article;
        $this->view->content  = $content;

        $this->view->parent      = $parent;
        $this->view->book        = $book;
        $this->view->prevAndNext = $this->book->getPrevAndNext($article);

        $this->dao->update(TABLE_BOOK)->set('views = views + 1')->where('id')->eq($articleID)->exec(false);

        $this->display();
    }

    /**
     * Admin a book or a chapter.
     * 
     * @params int    $nodeID
     * @access public
     * @return void
     */
    public function admin($nodeID = '')
    {
        $this->book->setMenu();

        if($nodeID)  ($node = $this->book->getNodeByID($nodeID))   && $book   = $node->book;
        if(!$nodeID or !$node)   ($node = $book = $this->book->getFirstBook()) && $nodeID = $node->id;
        if(!$node)  $this->locate(inlink('create'));

        $this->view->title   = $this->lang->book->common;
        $this->view->book    = $book;
        $this->view->node    = $node;
        $this->view->catalog = $this->book->getAdminCatalog($nodeID, $this->book->computeSN($book->id));

        $this->display();
    }

    /**
     * Create a book.
     *
     * @access public 
     * @return void
     */
    public function create()
    {
        $this->book->setMenu();

        if($_POST)
        {
            $bookID = $this->book->createBook();
            if($bookID)  $this->send(array('result' => 'success', 'message'=>$this->lang->saveSuccess, 'locate' => inlink('admin', "bookID=$bookID")));
            if(!$bookID) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        }
        $this->view->title = $this->lang->book->createBook;

        $this->display(); 
    }

    /**
     * Manage catalog of a book or a chapter.
     *
     * @param  int    $node   the node to manage.
     * @access public
     * @return void
     */
    public function catalog($node)
    {
        if($_POST)
        {
            /* First I need to check alias. */
            $return = $this->book->checkAlias();
            if(!$return['result']) 
            {
                $message =  sprintf($this->lang->book->aliasRepeat, join(',', array_unique($return['alias'])));
                $this->send(array('result' => 'fail', 'message' => $message));
            }

            /* No error, save to database. */
            $result = $this->book->manageCatalog($node);
            if($result) $this->send(array('result' => 'success', 'message'=>$this->lang->saveSuccess, 'locate' => $this->post->referer));
            $this->send(array('result' => 'fail', 'message' => dao::getError()));
        }

        $this->book->setMenu();
        unset($this->lang->book->typeList['book']);

        $this->view->title    = $this->lang->book->catalog;
        $this->view->node     = $this->book->getNodeByID($node);
        $this->view->children = $this->book->getChildren($node);

        $this->display(); 
    }

    /**
     * Edit a book, a chapter or an article.
     *
     * @param int $nodeID
     * @access public
     * @return void
     */
    public function edit($nodeID)
    {
        $this->book->setMenu();
        $node = $this->book->getNodeByID($nodeID);
        $book = $node->book;

        if($_POST)
        {
            $result = $this->book->update($nodeID);
            if($result) $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->post->referer));
            $this->send(array('result' => 'fail', 'message' => dao::getError()));
        }

        /* Get option menu without this node's family nodes. */
        $optionMenu = $this->book->getOptionMenu($book->id, $removeRoot = true);
        $families   = $this->book->getFamilies($node);
        foreach($families as $member) unset($optionMenu[$member->id]);

        $this->view->title      = $this->lang->edit . $this->lang->book->typeList[$node->type];
        $this->view->node       = $node;
        $this->view->optionMenu = $optionMenu;

        $this->display();
    }

    /**
     * Delete a node.
     *
     * @param int $nodeID
     * @retturn void
     */
    public function delete($nodeID)
    {
        if($this->book->delete($nodeID)) $this->send(array('result' => 'success'));
        $this->send(array('result' => 'fail', 'message' => dao::getError()));
    }

    /**
     * sort up.
     * 
     * @param  int    $id 
     * @access public
     * @return string;
     */
    public function up($id)
    {
        $node = $this->book->getNodeByID($id);
        $prev = $this->dao->select('id, `order`')
            ->from(TABLE_BOOK)
            ->where('parent')->eq($node->parent)
            ->andWhere('`order`')->lt($node->order)
            ->orderBy('`order` desc')
            ->limit(1)
            ->fetch();
        if(!$prev) return false;

        $this->dao->update(TABLE_BOOK)->set('`order`')->eq($node->order)->where('id')->eq($prev->id)->exec();
        $this->dao->update(TABLE_BOOK)->set('`order`')->eq($prev->order)->where('id')->eq($node->id)->exec();

        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * sort down.
     * 
     * @param  int    $id 
     * @access public
     * @return string;
     */
    public function down($id)
    {
        $node = $this->book->getNodeByID($id);
        $next = $this->dao->select('id, `order`')
            ->from(TABLE_BOOK)
            ->where('parent')->eq($node->parent)
            ->andWhere('`order`')->gt($node->order)
            ->orderBy('`order`')
            ->limit(1)
            ->fetch();
        if(!$next) return false;

        $this->dao->update(TABLE_BOOK)->set('`order`')->eq($node->order)->where('id')->eq($next->id)->exec();
        $this->dao->update(TABLE_BOOK)->set('`order`')->eq($next->order)->where('id')->eq($node->id)->exec();

        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }
}
