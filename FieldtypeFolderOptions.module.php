<?php
/**
 *  FieldtypeFolderOptions
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *
 *
*/
class FieldtypeFolderOptions extends Fieldtype {

	public static $defaultOptionValues = array();

	public static function getModuleInfo() {
		return array(
		'title' => 'Folder Options',
		'version' => 100,
		'summary' => 'Use files from specified folder as options'
		);
	}

	public function ___getConfigInputfields(Field $field) {

		$inputfields = $this->wire(new InputfieldWrapper());

		$f = $this->wire('modules')->get("InputfieldText");
		$f->attr('name', 'folder');
		$f->label = 'Folder Path';
		$f->value = $field->folder;
		$f->placeholder = "Folder path...";
		$f->required = true;
		$f->columnWidth = "100%";
		$f->description = "Folder path, relative to templates folder. Dont forget to put forward slash `/` the end.";
		$inputfields->add($f);

		$f = $this->wire('modules')->get("InputfieldRadios");
		$f->attr('name', 'input_type');
		$f->label = 'Inputfield';
		$f->options = [
			'InputfieldSelect' => "Select",
			'InputfieldRadios' => "Radios",
		];
		$f->value = $field->input_type;
		$f->optionColumns = "1";
		$f->required = true;
		$f->defaultValue = "InputfieldSelect";
		$f->columnWidth = "100%";
		$inputfields->add($f);

		return $inputfields;

	}

	public function getInputfield(Page $page, Field $fields) {
		
		if(empty($fields->folder)) return;

		// Folder
		$folder = $this->config->paths->templates . $fields->folder;
		
		// Get options
        $fodler_options = scandir($folder);

        // store options here
        $options = [];

		foreach($fodler_options as $opt) {
			$option = substr($opt, 0, -4);
			if($opt != "." && $opt != ".." && $opt != "inc" && $opt[0] != "_") {
				$options[$option] = ucfirst($option);
			}
		}

		$inputfield = $this->modules->get("{$fields->input_type}");
		if($fields->input_type == "InputfieldRadios") $inputfield->optionColumns = "1";

		foreach($options as $value => $label) {
			$value = trim($value);
			$label = !empty($label) ? $label : $value;
			$inputfield->addOption($value, $label);
		}
		
		// Add some attributes to the field
		// $inputfield->addOptionAttributes("my_field_name", ["selected" => "selected"]);

		return $inputfield;


	}

	public function getDatabaseSchema(Field $field) {
		$schema = parent::getDatabaseSchema($field);
		$schema['data'] = 'text NOT NULL';
		$schema['keys']['data_exact'] = 'KEY `data_exact` (`data`(255))';
		$schema['keys']['data'] = 'FULLTEXT KEY `data` (`data`)';
		return $schema;
	}

	public function sanitizeValue(Page $page, Field $field, $value) {
		return $value;
	}

}
