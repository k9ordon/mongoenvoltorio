<?

class Model_MongoCollection extends Model_Mongo {

	protected $modelType = null;

	protected $cursor;

	public function __construct($collectionName = false, $modelType = false) {
		parent::__construct();

		if(!$collectionName) throw new Exception("No collection selected");

		$this->collectionName = $collectionName;
		$this->collection = $this->db->$collectionName;
		$this->modelType = $modelType;

		return $this;
	}

	public function findOne() {}

	public function find($query = array(), $fields = array()) {
		$this->cursor = new Model_MongoCursor($this->collection->find($query, $fields), $this->modelType);
		return $this->cursor;
	}
}