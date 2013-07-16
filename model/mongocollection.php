<?

class Model_MongoCollection extends Model_Mongo {

	protected $modelType = null;

	protected $cursor;

	protected $query = [];

	public function __construct($collectionName = false, $modelType = false) {
		parent::__construct();

		if(!$collectionName) throw new Exception("No collection selected");

		$this->collectionName = $collectionName;
		$this->collection = $this->db->$collectionName;
		$this->modelType = $modelType;

		return $this;
	}

	public function findOne($query = array()) {
		$data = $this->collection->findOne($query);
		if(is_array($data)) {
			return $this->loadModel($this->modelType, $data);
		}
		return false;
	}

	public function find($query = array(), $fields = array()) {
		$this->cursor = new Model_MongoCursor($this->collection->find($query, $fields), $this->modelType);
		return $this->cursor;
	}

	public function query($query, $mergeToQuery = true) {
		if($mergeToQuery) $this->query = array_merge($this->query, $query);
		else $this->query = $query;

		return $this;
	}

	// shorthand
	public function queryIds($ids = array()) {
		return $this->query(['_id' => ['$in' => $ids]]);
	}

	// load cursor on call
	public function __call($func, $args) {
		$this->find($this->query);
		return call_user_func_array([$this->cursor, $func], $args);
	}
}