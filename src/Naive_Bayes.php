<?php

namespace Algorithm\Naive_Bayes;

use \Text\Stemmer;

class Text_Classification
{
	/**
	* Data class or target.
	*
	* @var array
	*/
	public $class;

	/**
	* Data training.
	*
	* @var array
	*/
	public $data;

	/**
	* Stemmed data training.
	*
	* @var array
	*/
	public $stemmed;

	/**
	* All words.
	*
	* @var array
	*/
	public $words;

	/**
	* Group of words each class or target.
	*
	* @var array
	*/
	public $words_class;

	/**
	* Set words and computing data for each class.
	*
	* @param string $class
	* @return void
	*/
	protected function setWordsClass($class)
	{
		$this->words_class = [];

		foreach ($class as $item)
		{
			$this->words_class[] = array(
				'class' => $item,
				'words' => array(),
				'posterior' => 0,
				'computed' => array()
			);
		}
	}

	/**
	* Filter data by class or target.
	*
	* @param string $class
	* @return array
	*/
	public function getDataByClass(string $class)
	{
		return array_filter($this->data, function ($item) use ($class) {
			return ($item['class'] === $class);
		});
	}

	/**
	* Set stemmed words.
	*
	* @param array $words
	* @return void
	*/
	public function setWords(array $words)
	{
		$this->words = $words;
	}

	/**
	* Find wordsClass index by class.
	*
	* @param string $class
	* @return int
	*/
	public function findWordsClassIndex(string $class)
	{
		foreach ($this->words_class as $index => $item)
		{
			foreach ($item as $key => $value)
			{
				if ($item['class'] === $class)
				{
					return $index;
				}
			}
		}

		return -1;
	}

	/**
	* Training data.
	*
	* @param array $data
	* @return void
	*/
	public function training(array $data)
	{
		$this->data = $data;
		$this->class = array_map(function($data) {
			return (isset($data['class'])) ? $data['class'] : FALSE;
		}, $data);

		$this->class = array_values(array_filter(array_unique($this->class)));
		$this->setWordsClass($this->class);

		$stemmer = new Stemmer();

		foreach ($this->data as $index => $item)
		{
			$stemmed = $stemmer->stem($item['text']);
			$this->stemmed[$index] = $stemmed;
			$this->data[$index]['text'] = $stemmed;
		}

		$this->setWords($stemmer->getWords());

		foreach ($this->class as $item)
		{
			$class = $this->getDataByClass($item);
			$index = $this->findWordsClassIndex($item);

			foreach ($this->words as $word)
			{
				$this->words_class[$index]['words'][] = ['word' => $word, 'count' => 0];
			}

			foreach ($class as $item)
			{
				$splits = explode(' ', $item['text']);

				foreach ($this->words_class[$index]['words'] as $key => $word)
				{
					foreach ($splits as $split)
					{
						if ($word['word'] === $split)
						{
							$this->words_class[$index]['words'][$key]['count']++;
						}
					}
				}
			}

			$this->words_class[$index]['posterior'] = count($class) / count($data);
			$wordsCount = count(array_filter($this->words_class[$index]['words'], function ($item) {
				return ($item['count'] !== 0);
			}));

			foreach ($this->words_class[$index]['words'] as $word)
			{
				$this->words_class[$index]['computed'][] = array(
					'word' => $word['word'],
					'value' => ($word['count'] + 1) / ($wordsCount + count($this->words))
				);
			}
		}
	}

	/**
	* Predict data.
	*
	* @param string|array $data
	* @return string
	*/
	public function predict($data)
	{
		$stemmer = new Stemmer();
		$stemmed = $stemmer->stem($data);
		$words = explode(' ', $stemmed);

		$test_class = [];

		foreach ($this->class as $class)
		{
			$index = $this->findWordsClassIndex($class);

			foreach ($words as $word)
			{
				$match = array_filter($this->words_class[$index]['computed'], function ($item) use ($word) {
					return ($item['word'] === $word);
				});

				if ($match)
				{
					$test_class[$class]['computed'][] = reset($match)['value'];
				}
				else
				{
					$test_class[$class]['computed'][] = 0;
				}
			}

			$test_class[$class]['result'] = 1;
		}

		foreach ($test_class as $key => $value)
		{
			if (isset($value['computed']))
			{
				foreach ($value['computed'] as $val)
				{
					$test_class[$key]['result'] *= $val;
				}
			}
		}

		$result = [];

		foreach ($this->class as $class)
		{
			$result[] = $test_class[$class]['result'];
		}

		$max = max($result);

		foreach ($test_class as $key => $item)
		{
			if ($item['result'] === $max)
			{
				return $key;
			}
		}

		return FALSE;
	}
}
