<?php
namespace Netbaseteam\Blog\Block\Cms;
use Magento\Framework\View\Element\Template\Context;

class Postlist extends \Magento\Framework\View\Element\Template
{
    const NUMBER_POST_REQUIRE_DEFAUL = 3;
    const IS_NEW_POSTS_LIST = true;
    protected $_postFactory;

    protected $_categoryFactory;

    protected $_dataHelper;
    protected $_commentFactory;

    protected $_filesystem ;
    protected $_imageFactory;

    public function __construct(
        Context $context,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Netbaseteam\Blog\Model\CommentFactory $commentFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
        $this->_commentFactory = $commentFactory;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        parent::__construct($context,$data);
    }

    /**
     * get store config number post require
     * @return number
     */

    protected function getNumberPostRequire() {
       return !empty($this->getNumberPost()) ? (int)$this->getNumberPost() : static::NUMBER_POST_REQUIRE_DEFAUL;
    }

    /**
     * get store config number post require
     * @return boolean
     */
    protected function getIsNewPostsRequire(){
        return !empty($this->getIsNewPosts()) ? $this->getIsNewPosts() : static::IS_NEW_POSTS_LIST;
    }

    /**
     * todo: filter Posts by require
     * @return []
     */

    public function getPostsCollection() {
        $store_id = $this->getStoreId();
        $numberPost = $this->getNumberPostRequire();
        $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id),
                                array('like'=>'%,'.$store_id),
                                array('like'=>$store_id.',%'),
                                array('like'=>'%,'.$store_id.',%')
                            )
                        );
        $postCollection->setPageSize($numberPost);
        if ($this->getIsNewPostsRequire()) {
            $postCollection->setOrder('creation_time','DESC');
        }

        return $postCollection;
    }

    /**
     * get posts require
     * @return [] | false
     */

    public function getPosts(){
       $post = $this->getPostsCollection();
       if (!empty($post)) {
           return $post;
       }
       return false;

    }

    /**
     * get current strore id
     * @return number
     */

    public function getStoreId(){
        return  $this->_dataHelper->getStoreviewId();
    }

    /**
     * get current strore id
     * @paramer $postId
     * @return []
     */

    public function getCategoryName($postId){
        $categoryCollection = $this->_categoryFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$this->getStoreId())
                        )
                    )->addFieldToFilter('post_ids',array(
                            array('eq'=>$postId),
                            array('like'=>'%&'.$postId),
                            array('like'=>$postId.'&%'),
                            array('like'=>'%&'.$postId.'&%')
                        )
                    )->setOrder('ordering','ASC');

        $data =  $categoryCollection->getData();
        if (empty($data)) {
             return false;
        }
        return $data[0]['name'];

    }

    /**
     * get numbercomment
     * @paramer $postId
     * @return number
     */

    public function getNumberComments($postId){
        $commentCollection = $this->_commentFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('post_id',array('eq'=>$postId))
                    ->setOrder('create_time','DESC');

        return count($commentCollection);
    }

    public function resize($img, $width = null, $height = null)
    {
        $path = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('blog/thumbnail/');
        $absolutePath = $path.$img;
        $width = $width ? $width : 400;
        $height = $height ? $height : 300;
        $imageResized = $path.'resize/'.$width.'/'.$img;
        if (!file_exists($imageResized)) {
            //create image factory.
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize($width,$height);
            //destination folder
            $destination = $imageResized ;
            //save image
            $imageResize->save($destination);
        }
    }

    public function getImagePostUrl($img, $width = null, $height = null){
        $width = $width ? $width : 400;
        $height = $height ? $height : 300;
        $this->resize($img, $width, $height);
        return $this->_dataHelper->getPrePostThumbUrl().'resize/'.$width.'/'.$img;
    }

    public function formatCreatedDate($createdTime){
        return $this->_dataHelper->getFormatDate($createdTime);
    }


    public function getPostUrl($url){
        return $this->_dataHelper->getPreBlogUrl().'/'.$url;
    }

    public function validShortContent($string,$wReturn){
        $wordsreturned = (int) $wReturn;
        return  strlen($string) > $wReturn ? substr($string,0,$wordsreturned).' ...' : substr($string,0,$wordsreturned);
    }

}
