<?php
/**
 * The model file of book module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     book
 * @version     $Id: model.php 3403 2015-12-21 09:36:09Z liugang $
 * @link        http://www.ranzhico.com
 */
class bookModel extends model
{
    /**
     * Set the menu for admin.
     * 
     * @access public
     * @return void
     */
    public function setMenu()
    {
        $this->lang->book->menu = new stdclass();

        $books = $this->getBookList();
        foreach($books as $bookID => $book)
        {
            $this->lang->book->menu->$bookID = $book->title . '|book|admin|book=' . $bookID;
        }

        $this->lang->book->menu->createBook = $this->lang->book->createBook . '|book|create|'; 
        $this->lang->menuGroups->tree = 'book';
    }

    /**
     * Get a book by id or alias.
     *
     * @param  string|int $id   the id can be the number id or the alias.
     * @access public
     * @return object
     */
    public function getBookByID($id)
    {
        $book = $this->dao->select('*')->from(TABLE_BOOK)->where('alias')->eq($id)->andWhere('type')->eq('book')->fetch();
        if(!$book) $book = $this->dao->select('*')->from(TABLE_BOOK)->where('id')->eq($id)->fetch();
        return $book;
    }

    /**
     * Get a node's book.
     * 
     * @param  object    $node 
     * @access public
     * @return object
     */
    public function getBookByNode($node)
    {
       return $this->getBookByID($this->extractBookID($node->path));
    }

    /**
     * Extract the book id from a node path.
     * 
     * @param  string    $path 
     * @access public
     * @return int
     */
    public function extractBookID($path)
    {
        $path = explode(',', trim($path, ','));
        return $path[0];
    }

    /**
     * Get the first book.
     * 
     * @access public
     * @return object|bool
     */
    public function getFirstBook()
    {
        return $this->dao->select('*')->from(TABLE_BOOK)->where('type')->eq('book')->orderBy('id_desc')->limit(1)->fetch();
    }

    /**
     * Get book list.
     *
     * @access public
     * @return array
     */
    public function getBookList()
    {
        return $this->dao->select('*')->from(TABLE_BOOK)->where('type')->eq('book')->orderBy('id_desc')->fetchAll('id');
    }

    /**
     * Get book catalog for front.
     * 
     * @param  int    $nodeID 
     * @access public
     * @return string
     */
    public function getFrontCatalog($nodeID, $serials)
    {
        static $catalog = '';
        
        $node = $this->getNodeByID($nodeID);
        if(!$node) return $catalog;

        $book   = $this->getBookByNode($node);
        $serial = $serials[$node->id];

        if($node->type == 'chapter') $link = helper::createLink('book', 'browse', "nodeID=$node->id", "book=$book->alias&node=$node->alias");
        if($node->type == 'article') $link = helper::createLink('book', 'read', "articleID=$node->id", "book=$book->alias&node=$node->alias");

        if($node->type == 'chapter') $catalog .= "<dd class='catalogue chapter'><strong><span class='order'>$serial</span>&nbsp;" . html::a($link, $node->title) . '</strong></dd>';
        if($node->type == 'article') $catalog .= "<dd class='catalogue article'><strong><span class='order'>$serial</span></strong>&nbsp;" . html::a($link, $node->title) . '</dd>';

        $children = $this->getChildren($nodeID);
        if($children) 
        {
            $catalog .= '<dl>';
            foreach($children as $child) $this->getFrontCatalog($child->id, $serials);
            $catalog .= '</dl>';
        }

        return $catalog;
    }

    /**
     * Get book catalog for admin.
     * 
     * @param  int    $nodeID 
     * @param  array  $serials  the serial number list for all nodes. 
     * @access public
     * @return void
     */
    public function getAdminCatalog($nodeID, $serials)
    {
        static $catalog = '';
        
        $node = $this->getNodeByID($nodeID);
        if(!$node) return $catalog;

        $children = $this->getChildren($nodeID);
        $serial   = $serials[$nodeID];

        $titleLink   = $node->type == 'book' ? $node->title : html::a(helper::createLink('book', 'admin', "bookID=$node->id"), $node->title);
        $editLink    = html::a(helper::createLink('book', 'edit', "nodeID=$node->id"), $this->lang->edit);
        $delLink     = empty($children) ? html::a(helper::createLink('book', 'delete', "bookID=$node->id"), $this->lang->delete, "class='deleter'") : '';
        $filesLink   = html::a(helper::createLink('file', 'browse', "objectType=book&objectID=$node->id"), $this->lang->book->files, "data-toggle='modal' data-width='1000'");
        $catalogLink = html::a(helper::createLink('book', 'catalog', "nodeID=$node->id"), $this->lang->book->catalog);
        $upLink      = html::a(helper::createLink('book', 'up', "nodeID=$node->id"), "<i class='icon-arrow-up'></i>", "class='sort'");
        $downLink    = html::a(helper::createLink('book', 'down', "nodeID=$node->id"), "<i class='icon-arrow-down'></i>", "class='sort'");

        if($node->type == 'book')    $catalog .= "<dt class='book'><strong>" . $titleLink . '</strong><span class="actions">' . $editLink . $catalogLink . $delLink . '</span></dt>';
        if($node->type == 'chapter') $catalog .= "<dd class='catalog chapter'><strong><span class='order'>" . $serial . '</span>&nbsp;' . $titleLink . '</strong><span class="actions">' . $editLink . $catalogLink . $delLink . $upLink . $downLink . '</span></dd>';
        if($node->type == 'article') $catalog .= "<dd class='catalog article'><strong><span class='order'>" . $serial . '</span>&nbsp;' . $node->title . '</strong><span class="actions">' . $editLink . $filesLink . $delLink . $upLink . $downLink . '</span></dd>';

        if($children) 
        {
            $catalog .= '<dl>';
            foreach($children as $child) $this->getAdminCatalog($child->id, $serials);
            $catalog .= '</dl>';
        }

        return $catalog;
    }

    /**
     * Compute the serial number for all nodes of a book.
     * 
     * @param  string    $path 
     * @access public
     * @return void
     */
    public function computeSN($bookID)
    {
        /* Get all children of the startNode. */
        $nodes = $this->dao->select('id, parent, `order`, path')->from(TABLE_BOOK)
            ->where('path')->like(",$bookID,%")
            ->andWhere('type')->ne('book')
            ->orderBy('grade, `order`')
            ->fetchAll('id');

        /* Group them by their parent. */
        $groupedNodes = array();
        foreach($nodes as $node) $groupedNodes[$node->parent][$node->id] = $node;

        $serials = array();
        foreach($nodes as $node)
        {
            $path      = explode(',', $node->path);
            $bookID    = $path[1];
            $startNode = $path[2];

            $serial = '';
            foreach($path as $nodeID)
            {
                /* If the node id is empty or is the bookID, skip. */
                if(!$nodeID) continue;
                if($nodeID == $bookID) continue;

                /* Compute the serial. */
                $parentID = $nodes[$nodeID]->parent;
                $brothers = $groupedNodes[$parentID];
                $serial  .= array_search($nodeID, array_keys($brothers)) + 1 . '.';
            }

            $serials[$node->id] = rtrim($serial, '.');
        }
        return $serials;
    }

    /**
     * Get a node of a book.
     *
     * @param  int      $nodeID
     * @param  bool     $replaceTag
     * @access public
     * @return object
     */
    public function getNodeByID($nodeID, $replaceTag = true)
    {
        $node = $this->dao->select('*')->from(TABLE_BOOK)->where('id')->eq($nodeID)->fetch();
        if(!$node) $node = $this->dao->select('*')->from(TABLE_BOOK)->where('alias')->eq($nodeID)->fetch();
        if(!$node) return false;

        $node->origins = $this->dao->select('id, type, alias, title')->from(TABLE_BOOK)->where('id')->in($node->path)->orderBy('grade')->fetchAll('id');
        $node->book    = current($node->origins);
        $node->files   = $this->loadModel('file')->getByObject('book', $nodeID);
        $node->content = $replaceTag ? $this->loadModel('tag')->addLink($node->content) : $node->content;

        return $node;
    }

    /**
     * Get children nodes of a node.
     * 
     * @param  int    $nodeID 
     * @access public
     * @return array
     */
    public function getChildren($nodeID)
    {
        return $this->dao->select('*')->from(TABLE_BOOK)->where('parent')->eq($nodeID)->orderBy('`order`')->fetchAll('id');
    }

    /**
     * Get the prev and next ariticle.
     * 
     * @param  int    $current  the current article id.
     * @param  int    $parent   the parent id.
     * @access public
     * @return array
     */
    public function getPrevAndNext($current)
    {
       $prev = $this->dao->select('id, title, alias')->from(TABLE_BOOK)
           ->where('parent')->eq($current->parent)
           ->andWhere('type')->eq('article')
           ->andWhere('`order`')->lt($current->order)
           ->orderBy('`order` desc')
           ->limit(1)
           ->fetch();

       $next = $this->dao->select('id, title, alias')->from(TABLE_BOOK)
           ->where('parent')->eq($current->parent)
           ->andWhere('type')->eq('article')
           ->andWhere('`order`')->gt($current->order)
           ->orderBy('`order`')
           ->limit(1)
           ->fetch();

        return array('prev' => $prev, 'next' => $next);
    }

    /**
     * Get families of a node.
     * 
     * @param  object    $node 
     * @access public
     * @return array
     */
    public function getFamilies($node)
    {
        return $this->dao->select('*')->from(TABLE_BOOK)->where('path')->like($node->path . '%')->fetchAll('id');
    }

    /**
     * Create a tree menu in <select> tag.
     * 
     * @param  int    $startParent 
     * @param  bool   $removeRoot 
     * @access public
     * @return string
     */
    public function getOptionMenu($startParent = 0, $removeRoot = false)
    {
        /* First, get all catalogues. */
        $treeMenu   = array();
        $stmt       = $this->dbh->query($this->buildQuery($startParent));
        $catalogues = array();
        while($catalogue = $stmt->fetch()) $catalogues[$catalogue->id] = $catalogue;

        /* Cycle them, build the select control.  */
        foreach($catalogues as $catalogue)
        {
            $origins = explode(',', $catalogue->path);
            $catalogueTitle = '/';
            foreach($origins as $origin)
            {
                if(empty($origin)) continue;
                $catalogueTitle .= $catalogues[$origin]->title . '/';
            }
            $catalogueTitle = rtrim($catalogueTitle, '/');
            $catalogueTitle .= "|$catalogue->id\n";

            if(isset($treeMenu[$catalogue->id]) and !empty($treeMenu[$catalogue->id]))
            {
                if(isset($treeMenu[$catalogue->parent]))
                {
                    $treeMenu[$catalogue->parent] .= $catalogueTitle;
                }
                else
                {
                    $treeMenu[$catalogue->parent] = $catalogueTitle;;
                }

                $treeMenu[$catalogue->parent] .= $treeMenu[$catalogue->id];
            }
            else
            {
                if(isset($treeMenu[$catalogue->parent]) and !empty($treeMenu[$catalogue->parent]))
                {
                    $treeMenu[$catalogue->parent] .= $catalogueTitle;
                }
                else
                {
                    $treeMenu[$catalogue->parent] = $catalogueTitle;
                }    
            }
        }

        $topMenu = @array_pop($treeMenu);
        $topMenu = explode("\n", trim($topMenu));
        if(!$removeRoot) $lastMenu[] = '/';

        foreach($topMenu as $menu)
        {
            if(!strpos($menu, '|')) continue;

            $menu        = explode('|', $menu);
            $label       = array_shift($menu);
            $catalogueID = array_pop($menu);
           
            $lastMenu[$catalogueID] = $label;
        }

        return $lastMenu;
    }

    /**
     * Build the sql to execute.
     * 
     * @param  int    $startParent   the start parent id
     * @access public
     * @return string
     */
    public function buildQuery($startParent = 0)
    {
        /* Get the start parent path according the $startParent. */
        $startPath = '';
        if($startParent > 0)
        {
            $startParent = $this->getNodeById($startParent);
            if($startParent) $startPath = $startParent->path . '%';
        }

        return $this->dao->select('*')->from(TABLE_BOOK)
            ->where('type')->ne('article')
            ->beginIF($startPath)->andWhere('path')->like($startPath)->fi()
            ->orderBy('grade desc, `order`')
            ->get();
    }

    /**
     * Create a book.
     *
     * @access public
     * @return bool
     */
    public function createBook()
    {
        $now = helper::now();
        $book = fixer::input('post')
            ->add('parent', 0)
            ->add('grade', 1)
            ->add('type', 'book')
            ->add('createdDate', $now)
            ->add('editedDate', $now)
            ->setForce('alias',    seo::unify($this->post->alias, '-'))
            ->setForce('keywords', seo::unify($this->post->keywords, ','))
            ->get();

        $this->dao->insert(TABLE_BOOK)
            ->data($book)
            ->autoCheck()
            ->batchCheck($this->config->book->require->book, 'notempty')
            ->check('alias', 'unique', "`type`='book'")
            ->exec();

        if(dao::isError()) return false;

        /* Update the path field. */
        $bookID   = $this->dao->lastInsertID();
        $bookPath = ",$bookID,";
        $this->dao->update(TABLE_BOOK)->set('path')->eq($bookPath)->where('id')->eq($bookID)->exec();

        if(dao::isError()) return false;

        /* Save keywrods. */
        $this->loadModel('tag')->save($book->keywords);

        /* Return the book id. */
        return $bookID;
    }

    /**
     * Manage a node's catalog.
     *
     * @param  int    $parentNode 
     * @access public
     * @return bool
     */
    public function manageCatalog($parentNode)
    {
        $parentNode = $this->getNodeByID($parentNode);

        /* Init the catalogue object. */
        $now = helper::now();
        $node = new stdclass();
        $node->parent     = $parentNode ? $parentNode->id : 0;
        $node->grade      = $parentNode ? $parentNode->grade + 1 : 1;
        $node->createdDate  = $now;
        $node->editedDate = $now;

        foreach($this->post->title as $key => $nodeTitle)
        {
            if(empty($nodeTitle)) continue;
            $mode = $this->post->mode[$key];

            /* First, save the child without path field. */
            $node->title    = $nodeTitle;
            $node->type     = $this->post->type[$key];
            $node->author   = $this->post->author[$key];
            $node->alias    = $this->post->alias[$key];
            $node->keywords = $this->post->keywords[$key];
            $node->order    = $this->post->order[$key];
            $node->alias    = seo::unify($node->alias, '-');
            $node->keywords = seo::unify($node->keywords, ',');

           if($mode == 'new')
            {
                $this->dao->insert(TABLE_BOOK)->data($node)->exec();

                /* After saving, update it's path. */
                $nodeID   = $this->dao->lastInsertID();
                $nodePath = $parentNode->path . "$nodeID,";
                $this->dao->update(TABLE_BOOK)->set('path')->eq($nodePath)->where('id')->eq($nodeID)->exec();
            }
            else
            {
                $nodeID = $key;
                unset($node->createdDate);
                $this->dao->update(TABLE_BOOK)->data($node)->autoCheck()->where('id')->eq($nodeID)->exec();
            }

            /* Save keywords. */
            $this->loadModel('tag')->save($node->keywords);
        }

        return !dao::isError();
    }

    /**
     * Check if alias available.
     *
     * @access public
     * @return void
     */
    public function checkAlias()
    {
        /* Define the return var. */
        $return = array();
        $return['result'] = true;

        /* Count the chapter alias's counts. */
        $chapterAlias = array();
        foreach($this->post->type as $key => $type)
        {
            if($type == 'chapter') $chapterAlias[] = seo::unify($this->post->alias[$key], '-'); 
        }
        $chapterAlias = array_count_values($chapterAlias);

        foreach($this->post->title as $key => $title)
        {
            $type  = $this->post->type[$key];
            $alias = seo::unify($this->post->alias[$key], '-');
            $mode  = $this->post->mode[$key];

            if($type == 'article' or $alias == '' or $title == '') continue;

            /* Check the alias exists in database or not. */
            $dbExists = $this->dao->select('count(*) AS count')
                ->from(TABLE_BOOK)
                ->where('type')->eq('chapter')
                ->andWhere('alias')->eq($alias)
                ->beginIF($mode == 'update')->andWhere('id')->ne($key)->fi()
                ->fetch('count');

            if($dbExists or $chapterAlias[$alias] > 1)
            {
                $return['result']      = false;
                $return['alias'][$key] = $alias;
            }
        }

        return $return;
    }

    /**
     * Update a node.
     *
     * @param int $nodeID
     * @access public
     * @return bool
     */
    public function update($nodeID)
    {
        $oldNode = $this->getNodeByID($nodeID);

        $node = fixer::input('post')
            ->add('id',            $nodeID)
            ->add('editor',        $this->app->user->account)
            ->add('editedDate',    helper::now())
            ->setForce('keywords', seo::unify($this->post->keywords, ','))
            ->setForce('alias',    seo::unify($this->post->alias, '-'))
            ->setForce('type',     $oldNode->type)
            ->stripTags('content', $this->config->allowedTags->admin)
            ->get();

        $node = $this->loadModel('file')->processEditor($node, $this->config->book->editor->edit['id']);
        $this->dao->update(TABLE_BOOK)
            ->data($node, $skip = 'uid,referer')
            ->autoCheck()
            ->batchCheckIF($node->type == 'book', $this->config->book->require->book, 'notempty')
            ->batchCheckIF($node->type != 'book', $this->config->book->require->node, 'notempty')
            ->checkIF($node->type == 'book', 'alias', 'unique', "`type` = 'book' AND id != '$nodeID'")
            ->where('id')->eq($nodeID)
            ->exec();

        if(dao::isError()) return false;

        $this->fixPath($oldNode->book->id);
        if(dao::isError()) return false;

        $this->loadModel('tag')->save($node->keywords);
        if(dao::isError()) return false;

        if($node->type == 'article')
        {
            $this->file->updateObjectID($this->post->uid, $nodeID, 'book');
            if(dao::isError()) return false;
        }

        return true;
    }

    /**
     * Fix the path, grade fields according to the id and parent fields.
     *
     * @param  string    $type 
     * @access public
     * @return void
     */
    public function fixPath($bookID)
    {
        /* Get all nodes grouped by parent. */
        $groupNodes = $this->dao->select('id, parent')->from(TABLE_BOOK)
            ->where('path')->like(",$bookID,%")
            ->fetchGroup('parent', 'id');
        $nodes = array();

        /* Cycle the groupNodes until it has no item any more. */
        while(count($groupNodes) > 0)
        { 
            /* Record the counts before processing. */
            $oldCounts = count($groupNodes);

            foreach($groupNodes as $parentNodeID => $childNodes)
            {
                /** 
                 * If the parentNode doesn't exsit in the nodes, skip it. 
                 * If exists, compute it's child nodes. 
                 */
                if(!isset($nodes[$parentNodeID]) and $parentNodeID != 0) continue;

                if($parentNodeID == 0)
                {
                    $parentNode = new stdclass();
                    $parentNode->grade = 0;
                    $parentNode->path  = ',';
                }
                else
                {
                    $parentNode = $nodes[$parentNodeID];
                }

                /* Compute it's child nodes. */
                foreach($childNodes as $childNodeID => $childNode)
                {
                    $childNode->grade = $parentNode->grade + 1;
                    $childNode->path  = $parentNode->path . $childNode->id . ',';

                    /**
                     * Save child node to nodes, 
                     * thus the child of child can compute it's grade and path.
                     */
                    $nodes[$childNodeID] = $childNode;
                }

                /* Remove it from the groupNodes.*/
                unset($groupNodes[$parentNodeID]);
            }

            /* If after processing, no node processed, break the cycle. */
            if(count($groupNodes) == $oldCounts) break;
        }

        /* Save nodes to database. */
        foreach($nodes as $node)
        {
            $this->dao->update(TABLE_BOOK)->data($node)
                ->where('id')->eq($node->id)
                ->exec();
        }
    }
    /**
     * Delete a book.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id, $null = null)
    {
        $book = $this->getNodeByID($id);
        if(!$book) return false;

        $this->dao->delete()->from(TABLE_BOOK)->where('id')->eq($id)->exec();
        return !dao::isError();
    }

    /**
     * Print files.
     * 
     * @param  object $files 
     * @access public
     * @return void
     */
    public function printFiles($files)
    {
        if(empty($files)) return false;

        $imagesHtml = '';
        $filesHtml  = '';
        foreach($files as $file)
        {
            $file->title = $file->title . ".$file->extension";
            if($file->isImage)
            {
                $imagesHtml .= "<li class='file-image file-{$file->extension}'>" . html::a(helper::createLink('file', 'download', "fileID=$file->id&mose=left"), html::image($file->fullURL), "target='_blank' data-toggle='lightbox'") . '</li>';
            }
            else
            {
                $filesHtml .= "<li class='file file-{$file->extension}'>" . html::a(helper::createLink('file', 'download', "fileID=$file->id&mouse=left"), $file->title, "target='_blank'") . '</li>';
            }
        }
        echo "<ul class='article-files clearfix'>" . $imagesHtml . $filesHtml . '</ul>';
    }

    /**
     * Create content navigation according the content. 
     * 
     * @param  int    $content 
     * @access public
     * @return string;
     */
    public function addMenu($content)
    {
        $nav = "<ul class='nav nav-content'>";
        $content = str_replace('<h3', '<h4', $content);
        $content = str_replace('h3>', 'h4>', $content);
        preg_match_all('|<h4.*>(.*)</h4>|isU', $content, $result);
        if(count($result[0]) >= 2)
        {
            foreach($result[0] as $id => $item)
            {
                $nav .= "<li><a href='#$id'>" . strip_tags($item) . "</a></li>";
                $replace = str_replace('<h4', "<h4 id=$id", $item);
                $content = str_replace($item, $replace, $content);
            }
            $nav .= "</ul>";
            $content = $nav . "<div class='content'>" . $content . '</div>';
        }

        return $content;
    }
}
