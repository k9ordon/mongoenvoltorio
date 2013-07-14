<?

class Model_MongoItem extends Model_Mongo {

	protected $data = null;

	protected $dirtyFields = array();

	public function __construct($collectionName = false, $data = array()) {
		parent::__construct();

		if(!$collectionName) throw new Exception("No collection selected");

		// convert constructor (int)id
		if(is_int($data)) {
			$data = ['_id' => $data];
		}

		$this->collectionName = $collectionName;
		$this->collection = $this->db->$collectionName;
		$this->data = $data;

		// if only _id => load record
		if(array_key_exists('_id', $data) && count($data) === 1) {
			$record = $this->collection->findOne(['_id' => $data['_id']]);
			if(!$record)  throw new Exception("Cannot load record with id " . $data['_id']);

			else $this->data = $record;
		}

		// if _id => update
		elseif(array_key_exists('_id', $data)) {+
			$this->update();
		}

		// weak record
		else {}

		return $this;
	}

	// data getter
	public function __get($field) {
		if(array_key_exists($field, $this->data)) return $this->data[$field];
		else throw new Exception("Cannot get " . $field);
	}

	// data setter
	public function __set($field, $value) {
		$this->data[$field] = $value;
		$this->setDirty($field, $value);
	}

	private function setDirty($field, $value) {
		$this->dirtyFields[$field] = $value;
	}

	public function isDirty($field = false) {
		if(!$field) return (bool) count($this->dirtyFields);
		else return array_key_exists($field, $this->dirtyFields);
	}

	protected function validate() {}

	public function save() {
		// must validate
		$this->validate();

		// update if id
		if(!array_key_exists('_id', $this->data)) {
			$this->create();
		}
		// create if no id
		else {
			$this->update();
		}
	}

	// create
	public function create() {
		$this->beforeCreate();
		// unique next id
		if(!array_key_exists('_id', $this->data)) 
			$this->data['_id'] = $this->getNextSequence('_id');

		// do the save
		$r = $this->collection->save($this->data);
		$this->afterCreate();
		return $r;
	}

	// create hook
	protected function beforeCreate() {}
	protected function afterCreate() {}

	// update
	public function update() {
		$this->beforeUpdate();
		$this->collection->save($this->data);
		$this->afterUpdate();
	}

	// update hook
	protected function beforeUpdate() {}
	protected function afterUpdate() {}

	// remove
	public function remove() {
		if(!array_key_exists('_id', $this->data)) die('no id');
		$this->beforeRemove();
		$this->collection->remove(['_id' => $this->data['_id']]);
		$this->afterRemove();
	}

	// remove hook
	protected function beforeRemove() {}
	protected function afterRemove() {}

}