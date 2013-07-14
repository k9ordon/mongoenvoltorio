<?

class Model_MongoItem extends Model_Mongo {

	protected $data = null;

	protected $dirtyFields = array();

	protected $externals = array();
	protected $externalObjects = array();	

	public function __construct($collectionName = false, $data = array()) {
		parent::__construct();

		if(!$collectionName) throw new Exception("No collection selected");

		// convert constructor (int)id to $data[_id]
		if(is_int($data)) {
			$data = ['_id' => $data];
		}

		$this->collectionName = $collectionName;
		$this->collection = $this->db->$collectionName;
		
		// if only _id => load record
		if(array_key_exists('_id', $data) && count($data) === 1) {
			$record = $this->collection->findOne(['_id' => $data['_id']]);
			if(!$record)  throw new Exception("Cannot load record with id " . $data['_id']);

			else $this->data = $record;
		} else {
			$this->data = $data;
		}
		return $this;
	}

	// data getter
	public function __get($field) {
		// return externalObject
		if(array_key_exists($field, $this->externalObjects)) return $this->externalObjects[$field];
		// late load external 
		elseif(array_key_exists($field, $this->externals)) return $this->loadExternal($field);
		// return data field
		elseif(array_key_exists($field, $this->data)) return $this->data[$field];
		// fail
		else throw new Exception("Cannot get " . $field);
	}

	// data setter
	public function __set($field, $value) {
		$this->data[$field] = $value;
		$this->setDirty($field, $value);
	}

	public function loadExternal($field) {
		$external = $this->externals[$field];
		// load single model
		if(array_key_exists('id', $external)) {
			$this->externalObjects[$field] = $this->loadModel($external['model'], $this->data[$external['id']]);
		}
		// load model array
		else {
			$this->externalObjects[$field] = $this->loadModels($external['model'], $this->data[$external['id']]);
		}

		return $this->externalObjects[$field];
	}

	private function setDirty($field, $value) {
		$this->dirtyFields[$field] = $value;
	}

	public function isDirty($field = false) {
		if(!$field) return (bool) count($this->dirtyFields);
		else return array_key_exists($field, $this->dirtyFields);
	}

	protected function validate() {}

	public function isUnique($field) {
		return (bool) $this->collection->findOne([$field => $this->$field], ['_id']);
	}

	public function save() {
		// update if id
		if(!array_key_exists('_id', $this->data)) {
			$this->create();
		}
		// create if no id
		else {
			$this->update();
		}

		return $this;
	}

	// create
	public function create() {
		$this->beforeCreate();
		$this->validate();
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
		$this->validate();
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