<?

class Model_MongoItem extends Model_Mongo {

	protected $data = null;

	protected $dirtyFields = array();

	protected $references = array();
	protected $referenceObjects = array();

	protected $defaultData = array();

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
			$resultItem = $this->collection->findOne(['_id' => $data['_id']]);
			if(!$resultItem)  throw new Exception("Cannot load record with id " . $data['_id']);
			else $data = $resultItem;
		}

		$this->data = array_merge($this->defaultData, $data);
		return $this;
	}

	// data getter
	public function __get($field) {
		// return referenceObject
		if(array_key_exists($field, $this->referenceObjects)) return $this->referenceObjects[$field];
		// late load reference 
		elseif(array_key_exists($field, $this->references)) return $this->loadReference($field);
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

	// array push
	public function push($field, $value, $unique = false) {
		if(!is_array($this->data[$field])) throw new Exception("Cannot push to non array " . $field);
		$this->data[$field][] = $value;
		if($unique) $this->data[$field] = array_unique($this->data[$field]);
	}

	// remove array by value
	public function removeByValue($field, $value) {
		if(!is_array($this->data[$field])) throw new Exception("Cannot removeByValue from non array " . $field);
		if(($key = array_search($value, $this->data[$field])) !== false) {
		    unset($this->data[$field][$key]);
		    $this->data[$field] = array_values($this->data[$field]);
		}
	} 

	public function loadReference($field) {
		$reference = $this->references[$field];
		// load single model
		if(array_key_exists('id', $reference)) {
			$this->referenceObjects[$field] = $this->loadModel($reference['model'], $this->data[$reference['id']]);
		}
		// load collection by ids
		else {
			$collection = new $reference['model']();
			$this->referenceObjects[$field] = $collection->queryIds($this->data[$reference['ids']]);
		}

		return $this->referenceObjects[$field];
	}

	private function setDirty($field, $value) {
		$this->dirtyFields[$field] = $value;
	}

	public function isDirty($field = false) {
		if(!$field) return (bool) count($this->dirtyFields);
		else return array_key_exists($field, $this->dirtyFields);
	}

	// validate methods @todo do not null || not default fields
	protected function validate() {}
	protected function validateCreate() {}
	protected function validateUpdate() {}

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
		$this->validateCreate();
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
		$this->validateUpdate();
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