<?php

/**
 * Themeconfig data helper
 */
namespace Netbaseteam\Themeconfig\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	const MEDIA_PATH = 'Themeconfig';
	protected $_assetRepo;
	protected $_directory_list;
	protected $_authSession;
	protected $_cacheTypeList;
	protected $_cacheFrontendPool;
	protected $_filesystem;
	protected $_imageFactory;
	protected $_scopeConfig;
	private $_objectManager;
	protected $statusCollectionFactory;
	protected $fontOptions;
	protected $aclRetriever;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 */
	public function __construct(
		\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\File\Size $fileSize,
		\Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Framework\View\Asset\Repository $assetRepo,
		\Magento\Framework\App\Filesystem\DirectoryList $directory_list,
		\Magento\Backend\Model\Auth\Session $authSession,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$this->httpFactory = $httpFactory;
		$this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_ioFile = $ioFile;
		$this->_storeManager = $storeManager;
		//$this->_imageFactory = $imageFactory;
		$this->layoutFactory = $layoutFactory;
		$this->_assetRepo = $assetRepo;
		$this->_authSession = $authSession;
		$this->aclRetriever = $aclRetriever;
		$this->_directory_list = $directory_list;
		$this->_cacheTypeList = $cacheTypeList;
		$this->_cacheFrontendPool = $cacheFrontendPool;
		$this->_filesystem = $filesystem;
		$this->_imageFactory = $imageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_objectManager = $objectmanager;
		$this->statusCollectionFactory = $statusCollectionFactory;
		parent::__construct($context);
	}

	public function getIsAllowed($resource) {
		// like a string, e.g. "Magento_Backend::cache" for Cache Management
		$role = $this->_authSession->getUser()->getRole();
		$resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
		return in_array($resource, $resources) or in_array("Magento_Backend::all", $resources);
	}

	/**
	 * Return the base media directory for Orderupload Item images
	 *
	 * @return string
	 */
	public function getBaseDir() {
		$path = $this->_filesystem->getDirectoryRead(
			DirectoryList::MEDIA
		)->getAbsolutePath(self::MEDIA_PATH);
		return $path;
	}

	public function getMediaUrl() {
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::MEDIA_PATH . "/";
		return $mediaUrl;
	}

	public function _writeFile($target_file, $source_file) {
		$path = $this->_assetRepo->getUrl($target_file);
		$pub_dir = $this->_directory_list->getPath('pub');

		$path_after_pub = explode("adminhtml", $path);
		$file_path = $pub_dir . "/static/adminhtml" . $path_after_pub[1];

		//$file_path = str_replace("/","\\", $file_path);
		$file_path = realpath($file_path);

		$file = fopen($file_path, 'w'); //<--------- file handler
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $source_file);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_FILE, $file); //<------- this is your magic line
		curl_exec($ch);
		curl_close($ch);
		fclose($file);
	}

	public function _writeContent2File($target_file, $content, $folder = '') {

		$path = $this->_assetRepo->getUrl($target_file);
		$pub_dir = $this->_directory_list->getPath('pub');
		$path_after_pub = explode("adminhtml", $path);
		$file_path = $pub_dir . "/static/adminhtml" . $path_after_pub[1];

		if ($folder != '') {
			$pathFolder = $this->_assetRepo->getUrl($folder);
			$pathFolderAfterPub = explode("adminhtml", $pathFolder);
			$folder_path = $pub_dir . "/static/adminhtml" . $pathFolderAfterPub[1];
			if (!is_dir($folder_path)) {
				mkdir($folder_path, 0777, true);
			}
		}

		$myfile = fopen($file_path, 'w+');
		$file_path = realpath($file_path);

		fwrite($myfile, $content);
		fclose($myfile);
	}

	public function _readFile($path_file) {
		$path = $this->_assetRepo->getUrl($path_file);
		curl_setopt($ch = curl_init(), CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public function _getUrlFile($path_file) {
		return $this->_assetRepo->getUrl($path_file);
	}

	public function getMenuPosition() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('core_config_data');

		$sql = "Select * FROM " . $tableName . " where `path`='themeconfig/menu/type'";
		$result = $connection->fetchAll($sql);

		if (count($result) > 0) {
			return $result[0]['value'];
		} else {
			return "";
		}

		// return $this->_scopeConfig->getValue('themeconfig/menu/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getStatusProduct() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('core_config_data');

		$sql = "Select * FROM " . $tableName . " where `path`='themeconfig/status/product'";
		$result = $connection->fetchAll($sql);

		return $result;
	}

	public function getAdminUserData() {
		if (is_object($this->_authSession->getUser())) {
			$usData = $this->_authSession->getUser()->getData();
		}

		if (isset($usData)) {
			return $usData;
		}

	}

	public function cleanCache() {
		$types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');
		foreach ($types as $type) {
			$this->_cacheTypeList->cleanType($type);
		}
		foreach ($this->_cacheFrontendPool as $cacheFrontend) {
			$cacheFrontend->getBackend()->clean();
		}
		return;
	}

	// pass imagename, width and height
	public function resize($image, $width = null, $height = null, $folder_path = "") {
		$absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($folder_path) . $image;
		$imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($folder_path) . $image;
		//create image factory...
		$imageResize = $this->_imageFactory->create();
		$imageResize->open($absolutePath);
		$imageResize->constrainOnly(TRUE);
		$imageResize->keepTransparency(TRUE);
		$imageResize->keepFrame(FALSE);
		$imageResize->keepAspectRatio(TRUE);
		$imageResize->resize($width, $height);
		//destination folder
		$destination = $imageResized;
		//save image
		$imageResize->save($destination);

		$resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $folder_path . $image;
		return $resizedURL;
	}

	public function addConfigAdmin($arr_input = array(), $arr_config = array(), $key = "", $value = "") {
		if (count($arr_input) > 0 && $key != '') {
			$arr_config[$key . '_header'] = $value;
			if (isset($arr_input['cb_use_footer'])) {
				$arr_config[$key . '_footer'] = $value;
			}
			if (isset($arr_input['cb_use_content'])) {
				$arr_config[$key . '_content'] = $value;
			}
		}
		return $arr_config;
	}

	public function isJson($str) {
		$json = json_decode($str);
		return $json && $str != $json;
	}

	public function jsonDecode($json, $assoc = false) {
		$ret = json_decode($json, $assoc);
		if ($error = json_last_error()) {
			$errorReference = [
				JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded.',
				JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON.',
				JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded.',
				JSON_ERROR_SYNTAX => 'Syntax error.',
				JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
				JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded.',
				JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded.',
				JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.',
			];
			$errStr = isset($errorReference[$error]) ? $errorReference[$error] : "Unknown error ($error)";
			throw new \Exception("JSON decode error ($error): $errStr");
		}
		return $ret;
	}

	public function mergerCssConfigFile($position = '', $content_new = '') {
		$result = '';
		$content_old = $this->_readFile('Netbaseteam_Themeconfig/css/style_config.min.css');

		$arr_part = array('content', 'footer', 'header', 'menu', 'main', 'general');
		preg_match_all("/[a-i\.#]( ?+[^\/])*+/", $content_old, $arr_content);
		if (count($arr_content) > 0 && $content_new != '') {
			if (count($arr_part) == count($arr_content[0])) {
				for ($i = 0; $i < count($arr_content[0]); $i++) {
					$result .= '/*' . strtoupper($arr_part[$i]) . '*/
					';
					if ($position == $arr_part[$i]) {
						$result .= $content_new;
					} else {
						$result .= $arr_content[0][$i];
					}
				}
			} else {
				$result = $content_old;
			}
		} else {
			$result = $content_old;
		}

		return $result;
	}

	public function replaceBgcolorConfigFile($position = array(), $color = '') {
		$result = '';
		$content_old = $this->_readFile('Netbaseteam_Themeconfig/css/style_config.min.css');

		$arr_part = array('content', 'footer', 'header', 'menu', 'main', 'general');
		preg_match_all("/[a-i\.#]( ?+[^\/])*+/", $content_old, $arr_content);

		if (count($arr_content) > 0 && $color != '') {
			if (count($arr_part) == count($arr_content[0])) {
				for ($i = 0; $i < count($arr_content[0]); $i++) {
					$result .= '/*' . strtoupper($arr_part[$i]) . '*/
					';
					if (in_array($arr_part[$i], $position)) {
						$result .= preg_replace("/(background\:) (#[0-9a-f]{3,6})(\;)/", "$1 " . $color . "$3", $arr_content[0][$i]);
					} else {
						$result .= $arr_content[0][$i];
					}
				}
			}
		} else {
			$result = $content_old;
		}

		return $result;
	}

	public function exportConfig($position = array(), $temp = array()) {
		$arr_part = array('content', 'footer', 'header', 'menu', 'main', 'general');
		$style_config = $this->_readFile('Netbaseteam_Themeconfig/css/style_config.min.css');
		preg_match_all("/[a-i\.#]( ?+[^\/])*+/", $style_config, $arr_content);
		if (count($arr_content) > 0) {
			if (count($arr_part) == count($arr_content[0])) {
				$j = 0;
				for ($i = 0; $i < count($arr_content[0]); $i++) {
					if (in_array($arr_part[$i], $position)) {
						$temp += array('netbase_' . $position[$j] => base64_encode(trim(preg_replace('/\/\*\w+\*\//', '', $arr_content[0][$i]))));
						$temp += array('netbasekey_' . $position[$j] => $this->createKeyString(base64_encode(trim(preg_replace('/\/\*\w+\*\//', '', $arr_content[0][$i])))));
						++$j;
					}
				}
			}
		}
		return $temp;
	}

	public function createKeyString($str = '') {
		return md5(sha1(sha1(sha1($str))));
	}

	public function uploadFileWithUser($folder = '', $fileid = '', $curr_url_image = '') {
		$load_bgr_url = "";

		if ($folder != '' && $fileid != '') {

			$base_dir = $this->getBaseDir();
			if (!file_exists($base_dir)) {
				mkdir($base_dir, 0777);
			}

			$bg_header_folder = $this->getBaseDir() . "/background/" . $folder . "/";
			if (!file_exists($bg_header_folder)) {
				mkdir($bg_header_folder, 0777, true);
			}

			$userData = $this->getAdminUserData();
			$admin_header_folder = $bg_header_folder . "/" . $userData["username"] . "/";
			if (!file_exists($admin_header_folder)) {
				mkdir($admin_header_folder, 0777, true);
			}

			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => $fileid]
				);

				$files = glob($admin_header_folder . '*');
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}

				}

				$file = $uploader->validateFile();

				if (isset($file)) {
					$ret = array();
					$error = $file["error"];
					if (!is_array($file["name"])) {
						$bgimage_header = $file["name"];
						move_uploaded_file($file["tmp_name"], $admin_header_folder . $bgimage_header);
					}
				}

				$background = $this->getMediaUrl() . "background/" . $folder . "/" . $userData["username"] . "/";
				$background_folder = $this->getBaseDir() . "/background/" . $folder . "/" . $userData["username"] . "/";

				if (file_exists($background_folder)) {
					$files = array_diff(scandir($background_folder), array('.', '..'));
					foreach ($files as $file) {
						$load_bgr_url = $background . $file;
						break;
					}
				}
			} catch (\Exception $e) {
			}
		}

		if ($load_bgr_url == '' && $curr_url_image == '') {
			$files = glob($admin_header_folder . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}

			}
		}

		return $load_bgr_url;
	}

	public function getStatusOptions() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('sales_order_status');

		$sql = "Select * FROM " . $tableName . " order by `label` ASC";
		$result = $connection->fetchAll($sql);

		return $result;

		// $options = $this->statusCollectionFactory->create()->toOptionArray();
		// return $options;
	}

	public function toOptionArray() {
		if ($this->fontOptions !== null) {
			return $this->fontOptions;
		}

		$fonts = $this->getFontsList();
		$fontOptions = [];
		foreach ($fonts as $value) {
			$fontOptions[] = [
				'label' => $value,
				'value' => $value,
			];
		}
		$this->fontOptions = $fontOptions;

		return $this->fontOptions;
	}

	public function getTypeFont($font = '') {
		$result = '';
		if (strpos($font, ',') !== false) {
			$tmp = explode(',', $font);
			$result = '"' . $tmp[0] . '", ' . $tmp[1];
		} else {
			$result = '"' . $font . '", sans-serif';
		}
		return $result;
	}

	public function copyBGImage($path = '', $folder_source = '', $folder_des = '') {
		$test = true;
		$bg_des_folder = $this->getBaseDir() . "/background/" . $folder_des . "/";
		if (!file_exists($bg_des_folder)) {
			mkdir($bg_des_folder, 0777, true);
		}

		$userData = $this->getAdminUserData();
		$admin_des_folder = $bg_des_folder . "/" . $userData["username"] . "/";
		if (!file_exists($admin_des_folder)) {
			mkdir($admin_des_folder, 0777, true);
		}

		if ($path != '') {
			if ($folder_des != '' && $folder_source != '') {
				$content_arr = explode('/pub/', $path);
				if (count($content_arr) > 0) {
					$result = str_replace('/' . $folder_source . '/', '/' . $folder_des . '/', 'pub/' . $content_arr[1]);
					$test = copy('pub/' . $content_arr[1], $result);
				}
			}
		} else {
			$files = glob($admin_des_folder . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}

			}
		}
		return $test;
	}

	/**
	 * Get google fonts list
	 *
	 * @return array
	 */
	public function getFontsList() {
		return array(
			'ABeeZee',
			'Abel',
			'Abril Fatface',
			'Aclonica',
			'Acme',
			'Actor',
			'Adamina',
			'Advent Pro',
			'Aguafina Script',
			'Akronim',
			'Aladin',
			'Aldrich',
			'Alef',
			'Alegreya',
			'Alegreya SC',
			'Alegreya Sans',
			'Alegreya Sans SC',
			'Alex Brush',
			'Alfa Slab One',
			'Alice',
			'Alike',
			'Alike Angular',
			'Allan',
			'Allerta',
			'Allerta Stencil',
			'Allura',
			'Almendra',
			'Almendra Display',
			'Almendra SC',
			'Amarante',
			'Amaranth',
			'Amatic SC',
			'Amethysta',
			'Anaheim',
			'Andada',
			'Andika',
			'Angkor',
			'Annie Use Your Telescope',
			'Anonymous Pro',
			'Antic',
			'Antic Didone',
			'Antic Slab',
			'Anton',
			'Arapey',
			'Arbutus',
			'Arbutus Slab',
			'Architects Daughter',
			'Archivo Black',
			'Archivo Narrow',
			'Arimo',
			'Arizonia',
			'Armata',
			'Artifika',
			'Arvo',
			'Asap',
			'Asset',
			'Astloch',
			'Asul',
			'Atomic Age',
			'Aubrey',
			'Audiowide',
			'Autour One',
			'Average',
			'Average Sans',
			'Averia Gruesa Libre',
			'Averia Libre',
			'Averia Sans Libre',
			'Averia Serif Libre',
			'Bad Script',
			'Balthazar',
			'Bangers',
			'Basic',
			'Battambang',
			'Baumans',
			'Bayon',
			'Belgrano',
			'Belleza',
			'BenchNine',
			'Bentham',
			'Berkshire Swash',
			'Bevan',
			'Bigelow Rules, cursive',
			'Bigshot One',
			'Bilbo',
			'Bilbo Swash Caps',
			'Bitter',
			'Black Ops One',
			'Bokor',
			'Bonbon',
			'Boogaloo',
			'Bowlby One',
			'Bowlby One SC',
			'Brawler',
			'Bree Serif',
			'Bubblegum Sans',
			'Bubbler One',
			'Buda',
			'Buenard',
			'Butcherman',
			'Butterfly Kids',
			'Cabin',
			'Cabin Condensed',
			'Cabin Sketch',
			'Caesar Dressing',
			'Cagliostro',
			'Calligraffitti',
			'Cambo',
			'Candal',
			'Cantarell',
			'Cantata One',
			'Cantora One',
			'Capriola',
			'Cardo',
			'Carme',
			'Carrois Gothic',
			'Carrois Gothic SC',
			'Carter One',
			'Caudex',
			'Cedarville Cursive',
			'Ceviche One',
			'Changa One',
			'Chango',
			'Chau Philomene One',
			'Chela One',
			'Chelsea Market',
			'Chenla',
			'Cherry Cream Soda',
			'Cherry Swash',
			'Chewy',
			'Chicle',
			'Chivo',
			'Cinzel',
			'Cinzel Decorative',
			'Clicker Script',
			'Coda',
			'Coda Caption',
			'Codystar',
			'Combo',
			'Comfortaa',
			'Coming Soon',
			'Concert One',
			'Condiment',
			'Content',
			'Contrail One',
			'Convergence',
			'Cookie',
			'Copse',
			'Corben',
			'Courgette',
			'Cousine',
			'Coustard',
			'Covered By Your Grace',
			'Crafty Girls',
			'Creepster',
			'Crete Round',
			'Crimson Text',
			'Croissant One',
			'Crushed',
			'Cuprum',
			'Cutive',
			'Cutive Mono, monospace',
			'Damion',
			'Dancing Script',
			'Dangrek',
			'Dawning of a New Day',
			'Days One',
			'Delius',
			'Delius Swash Caps',
			'Delius Unicase',
			'Della Respira',
			'Denk One',
			'Devonshire',
			'Didact Gothic',
			'Diplomata',
			'Diplomata SC',
			'Domine',
			'Donegal One',
			'Doppio One',
			'Dorsa',
			'Dosis',
			'Dr Sugiyama',
			'Droid Sans',
			'Droid Sans Mono',
			'Droid Serif',
			'Duru Sans',
			'Dynalight',
			'EB Garamond',
			'Eagle Lake',
			'Eater',
			'Economica',
			'Ek Mukta',
			'Electrolize',
			'Elsie',
			'Elsie Swash Caps',
			'Emblema One',
			'Emilys Candy, cursive',
			'Engagement',
			'Englebert',
			'Enriqueta',
			'Erica One',
			'Esteban',
			'Euphoria Script',
			'Ewert',
			'Exo',
			'Exo 2',
			'Expletus Sans',
			'Fanwood Text',
			'Fascinate',
			'Fascinate Inline',
			'Faster One',
			'Fasthand',
			'Fauna One',
			'Federant, cursive',
			'Federo',
			'Felipa',
			'Fenix',
			'Finger Paint',
			'Fira Mono',
			'Fira Sans',
			'Fjalla One',
			'Fjord One',
			'Flamenco',
			'Flavors',
			'Fondamento',
			'Fontdiner Swanky',
			'Forum',
			'Francois One',
			'Freckle Face',
			'Fredericka the Great',
			'Fredoka One',
			'Freehand',
			'Fresca',
			'Frijole',
			'Fruktur',
			'Fugaz One',
			'GFS Didot',
			'GFS Neohellenic',
			'Gabriela',
			'Gafata',
			'Galdeano',
			'Galindo',
			'Gentium Basic',
			'Gentium Book Basic',
			'Geo',
			'Geostar',
			'Geostar Fill',
			'Germania One',
			'Gilda Display',
			'Give You Glory',
			'Glass Antiqua',
			'Glegoo',
			'Gloria Hallelujah',
			'Goblin One',
			'Gochi Hand',
			'Gorditas',
			'Goudy Bookletter 1911',
			'Graduate',
			'Grand Hotel',
			'Gravitas One',
			'Great Vibes',
			'Griffy',
			'Gruppo',
			'Gudea',
			'Habibi',
			'Halant',
			'Hammersmith One',
			'Hanalei',
			'Hanalei Fill',
			'Handlee',
			'Hanuman',
			'Happy Monkey',
			'Headland One',
			'Henny Penny',
			'Herr Von Muellerhoff',
			'Hind',
			'Holtwood One SC',
			'Homemade Apple',
			'Homenaje',
			'IM Fell DW Pica',
			'IM Fell DW Pica SC',
			'IM Fell Double Pica',
			'IM Fell Double Pica SC',
			'IM Fell English',
			'IM Fell English SC',
			'IM Fell French Canon',
			'IM Fell French Canon SC',
			'IM Fell Great Primer',
			'IM Fell Great Primer SC',
			'Iceberg',
			'Iceland',
			'Imprima',
			'Inconsolata',
			'Inder',
			'Indie Flower',
			'Inika',
			'Irish Grover',
			'Istok Web',
			'Italiana',
			'Italianno',
			'Jacques Francois',
			'Jacques Francois Shadow',
			'Jim Nightshade',
			'Jockey One',
			'Jolly Lodger',
			'Josefin Sans',
			'Josefin Slab',
			'Joti One',
			'Judson',
			'Julee',
			'Julius Sans One',
			'Junge',
			'Jura',
			'Just Another Hand',
			'Just Me Again Down Here',
			'Kalam',
			'Kameron',
			'Kantumruy',
			'Karla',
			'Karma',
			'Kaushan Script',
			'Kavoon',
			'Kdam Thmor',
			'Keania One',
			'Kelly Slab',
			'Kenia',
			'Khand',
			'Khmer',
			'Kite One',
			'Knewave',
			'Kotta One',
			'Koulen',
			'Kranky',
			'Kreon',
			'Kristi',
			'Krona One',
			'La Belle Aurore',
			'Laila',
			'Lancelot',
			'Lato',
			'League Script',
			'Leckerli One',
			'Ledger',
			'Lekton',
			'Lemon',
			'Libre Baskerville',
			'Life Savers',
			'Lilita One',
			'Lily Script One',
			'Limelight',
			'Linden Hill',
			'Lobster',
			'Lobster Two',
			'Londrina Outline',
			'Londrina Shadow',
			'Londrina Sketch',
			'Londrina Solid',
			'Lora',
			'Love Ya Like A Sister',
			'Loved by the King',
			'Lovers Quarrel',
			'Luckiest Guy',
			'Lusitana',
			'Lustria',
			'Macondo',
			'Macondo Swash Caps',
			'Magra',
			'Maiden Orange',
			'Mako',
			'Marcellus',
			'Marcellus SC',
			'Marck Script',
			'Margarine',
			'Marko One',
			'Marmelad',
			'Marvel',
			'Mate',
			'Mate SC',
			'Maven Pro',
			'McLaren',
			'Meddon',
			'MedievalSharp',
			'Medula One',
			'Megrim',
			'Meie Script',
			'Merienda',
			'Merienda One',
			'Merriweather',
			'Merriweather Sans',
			'Metal',
			'Metal Mania',
			'Metamorphous',
			'Metrophobic',
			'Michroma',
			'Milonga',
			'Miltonian',
			'Miltonian Tattoo',
			'Miniver',
			'Miss Fajardose',
			'Modern Antiqua',
			'Molengo',
			'Molle',
			'Monda',
			'Monofett',
			'Monoton',
			'Monsieur La Doulaise',
			'Montaga',
			'Montez',
			'Montserrat',
			'Montserrat Alternates',
			'Montserrat Subrayada',
			'Moul',
			'Moulpali',
			'Mountains of Christmas',
			'Mouse Memoirs',
			'Mr Bedfort',
			'Mr Dafoe',
			'Mr De Haviland',
			'Mrs Saint Delafield',
			'Mrs Sheppards',
			'Muli',
			'Mystery Quest',
			'Neucha',
			'Neuton',
			'New Rocker',
			'News Cycle',
			'Niconne',
			'Nixie One',
			'Nobile',
			'Nokora',
			'Norican',
			'Nosifer',
			'Nothing You Could Do',
			'Noticia Text',
			'Noto Sans',
			'Noto Serif',
			'Nova Cut',
			'Nova Flat',
			'Nova Mono',
			'Nova Oval',
			'Nova Round',
			'Nova Script',
			'Nova Slim',
			'Nova Square',
			'Numans',
			'Nunito',
			'Odor Mean Chey',
			'Offside',
			'Old Standard TT',
			'Oldenburg',
			'Oleo Script',
			'Oleo Script Swash Caps',
			'Open Sans',
			'Open Sans Condensed',
			'Oranienbaum',
			'Orbitron',
			'Oregano',
			'Orienta',
			'Original Surfer',
			'Oswald',
			'Over the Rainbow',
			'Overlock',
			'Overlock SC',
			'Ovo',
			'Oxygen',
			'Oxygen Mono',
			'PT Mono',
			'PT Sans',
			'PT Sans Caption',
			'PT Sans Narrow',
			'PT Serif',
			'PT Serif Caption',
			'Pacifico',
			'Paprika',
			'Parisienne',
			'Passero One',
			'Passion One',
			'Pathway Gothic One',
			'Patrick Hand',
			'Patrick Hand SC',
			'Patua One',
			'Paytone One',
			'Peralta',
			'Permanent Marker',
			'Petit Formal Script',
			'Petrona',
			'Philosopher',
			'Piedra',
			'Pinyon Script',
			'Pirata One',
			'Plaster',
			'Play',
			'Playball',
			'Playfair Display',
			'Playfair Display SC',
			'Podkova',
			'Poiret One',
			'Poller One',
			'Poly',
			'Pompiere',
			'Pontano Sans',
			'Poppins',
			'Port Lligat Sans',
			'Port Lligat Slab',
			'Prata',
			'Preahvihear',
			'Press Start 2P',
			'Princess Sofia',
			'Prociono',
			'Prosto One',
			'Puritan',
			'Purple Purse',
			'Quando',
			'Quantico',
			'Quattrocento',
			'Quattrocento Sans',
			'Questrial',
			'Quicksand',
			'Quintessential',
			'Qwigley',
			'Racing Sans One',
			'Radley',
			'Rajdhani',
			'Raleway',
			'Raleway Dots',
			'Rambla',
			'Rammetto One',
			'Ranchers',
			'Rancho',
			'Rationale',
			'Redressed',
			'Reenie Beanie',
			'Revalia',
			'Ribeye',
			'Ribeye Marrow',
			'Righteous',
			'Risque',
			'Roboto',
			'Roboto Condensed',
			'Roboto Slab',
			'Rochester',
			'Rock Salt',
			'Rokkitt',
			'Romanesco',
			'Ropa Sans',
			'Rosario',
			'Rosarivo',
			'Rouge Script',
			'Rozha One',
			'Rubik Mono One',
			'Rubik One',
			'Ruda',
			'Rufina',
			'Ruge Boogie',
			'Ruluko',
			'Rum Raisin',
			'Ruslan Display',
			'Russo One',
			'Ruthie',
			'Rye',
			'Sacramento',
			'Sail',
			'Salsa',
			'Sanchez',
			'Sancreek',
			'Sansita One',
			'Sarina',
			'Sarpanch',
			'Satisfy',
			'Scada',
			'Schoolbell',
			'Seaweed Script',
			'Sevillana',
			'Seymour One',
			'Shadows Into Light',
			'Shadows Into Light Two',
			'Shanti',
			'Share',
			'Share Tech',
			'Share Tech Mono',
			'Shojumaru',
			'Short Stack',
			'Siemreap',
			'Sigmar One',
			'Signika',
			'Signika Negative',
			'Simonetta',
			'Sintony',
			'Sirin Stencil',
			'Six Caps',
			'Skranji',
			'Slabo 13px',
			'Slabo 27px',
			'Slackey',
			'Smokum',
			'Smythe',
			'Sniglet',
			'Snippet',
			'Snowburst One',
			'Sofadi One',
			'Sofia',
			'Sonsie One',
			'Sorts Mill Goudy',
			'Source Code Pro',
			'Source Sans Pro',
			'Source Serif Pro',
			'Special Elite',
			'Spicy Rice',
			'Spinnaker',
			'Spirax',
			'Squada One',
			'Stalemate',
			'Stalinist One',
			'Stardos Stencil',
			'Stint Ultra Condensed',
			'Stint Ultra Expanded',
			'Stoke',
			'Strait',
			'Sue Ellen Francisco',
			'Sunshiney',
			'Supermercado One',
			'Suwannaphum',
			'Swanky and Moo Moo',
			'Syncopate',
			'Tangerine',
			'Taprom',
			'Tauri',
			'Teko',
			'Telex',
			'Tenor Sans',
			'Text Me One',
			'The Girl Next Door',
			'Tienne',
			'Tinos',
			'Titan One',
			'Titillium Web',
			'Trade Winds',
			'Trocchi',
			'Trochut',
			'Trykker',
			'Tulpen One',
			'Ubuntu',
			'Ubuntu Condensed',
			'Ubuntu Mono',
			'Ultra',
			'Uncial Antiqua',
			'Underdog',
			'Unica One',
			'UnifrakturCook',
			'UnifrakturMaguntia',
			'Unkempt',
			'Unlock',
			'Unna',
			'VT323',
			'Vampiro One',
			'Varela',
			'Varela Round',
			'Vast Shadow',
			'Vesper Libre',
			'Vibur',
			'Vidaloka',
			'Viga',
			'Voces',
			'Volkhov',
			'Vollkorn',
			'Voltaire',
			'Waiting for the Sunrise',
			'Wallpoet',
			'Walter Turncoat',
			'Warnes',
			'Wellfleet',
			'Wendy One',
			'Wire One',
			'Yanone Kaffeesatz',
			'Yellowtail',
			'Yeseva One',
			'Yesteryear',
			'Zeyada',
		);
	}

}
