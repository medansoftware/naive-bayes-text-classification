# Naive Bayes - Text Classification | PHP Algorithm


## Installation


## Through [Composer](https://getcomposer.org)

```bash
composer require medansoftware/c45-algorithm-php
```

## Standalone File

You can download standalone file [here](https://github.com/medansoftware/naive-bayes-text-classification/releases/download/1.0.0/standalone.zip)

## Usage

```php
require ('vendor/autoload.php');

$data = array(
	array(
		'text' => 'Hello, Good morning',
		'class' => 'greeting'
	),
	array(
		'text' => 'Hello, Good afternoon',
		'class' => 'greeting'
	),
	array(
		'text' => 'Hello, Good night',
		'class' => 'greeting'
	),
	array(
		'text' => 'How are you?',
		'class' => 'about-me'
	),
	array(
		'text' => 'How old are you?',
		'class' => 'about-me'
	),
	array(
		'text' => 'Where are you',
		'class' => 'about-me'
	),
	array(
		'text' => 'What\'s the time now?',
		'class' => 'information-time'
	),
	array(
		'text' => 'What\'s day today?',
		'class' => 'information-time'
	),
	array(
		'text' => 'What\'s day today?',
		'class' => 'information-time'
	)
);

$naive_bayes = new Algorithm\Naive_Bayes\Text_Classification;

$naive_bayes->training($data);

echo $naive_bayes->predict('Hello sir What\'s time now'); // information-time
```