<?

class Model_MongoCollection extends Model_Mongo {

	public function __construct($collectionName = false) {
		parent::__construct();

		if(!$collectionName) throw new Exception("No collection selected");

		$this->collectionName = $collectionName;
		$this->collection = $this->db->$collection;

		return $this;
	}
}