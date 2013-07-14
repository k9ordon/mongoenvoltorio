<?

class Model_MongoCursor extends Model_Mongo {

	protected $cursor;

	public function __construct($cursor = false, $modelType = false) {
		if(!$cursor) throw new Exception("No cursor passed selected");
		$this->cursor = $cursor;
		$this->modelType = $modelType;

		return $this;
	}

	public function limit($num) {
		$this->cursor = $this->cursor->limit($num);
		return $this;
	}

	public function sort($fields) {
		$this->cursor = $this->cursor->sort($fields);
		return $this;
	}

	public function count($foundOnly = false) {
		return $this->cursor->count($foundOnly);
	}

	public function toArray() {
		return iterator_to_array($this->cursor);
	}

	public function load() {
		return $this->loadModels($this->modelType, $this->toArray());
	}

	public function random() {
		$count = $this->count(true);
		if($count == 0) throw new Exception('No Random on no records.');
		$random = rand(0, $count - 1);
		$randomItem = $this->cursor->limit(-1)->skip($random)->getNext();
		return $this->loadModel($this->modelType, $randomItem);
	}
}