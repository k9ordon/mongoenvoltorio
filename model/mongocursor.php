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
		return $this->cursor->limit($num);
	}

	public function count() {

	}

	public function toArray() {
		return iterator_to_array($this->cursor);
	}

	public function toModels() {
		return $this->loadModels($this->modelType, $this->toArray());
	}
}