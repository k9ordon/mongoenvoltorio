<?

class Model_Mongo extends Model {
	//mongo db
	public $db = null;

	// mongo
	public static $mongo = null;
	
	// mongo collection
	public $collectionName = null;
	public $collection = null;

	public function __construct($db = 'main') {
		parent::__construct();

		if(self::$mongo === null) {
			self::$mongo = new MongoClient();
		}

		$this->db = self::$mongo->selectDB($db);
	}

	public function getNextSequence($field) {
		$r = $this->collection->find()->sort(array($field => -1))->limit(1);
		$r->next();
		$r = $r->current();

	   return $r[$field] + 1;
	}

	public function loadModel($modelName = 'Model_MongoItem', $id) {

	}

	public function loadModels($modelName, $ids = array()) {

	}

}