Tecnosenior FAQ Manager
=======================

A Wordpress FAQ Manager plugin based on IndiaNIC FAQs Manager. It's a rewrite from scratch, not a fork. 
The plugin is mostly MVC based, at least I've tried - Wordpress is not that great for creating something like this. 
And PHP has its quirks too. 

Features
--------
* MVC Based
* AJAX Administration Interfaces
* Skins for shortcode tag
* Translatable
* Searchable using mysql FULLTEXT support
* AJAX Search
* Users can post new questions
* Notify on new questions
* Notify who asked when answered

To Do
-----
* Fix fulltext search
* Allow customisation of email messages

Feel free to ask new features! I'll add them whenever possible. Bugfixes are also welcomed.

Skins
=====
To create a skin, just add a new directory under skins with your new skin. Inside the directory there should be a PHP
file with a custom header and two functions. Those functions are called by the shortcode renderer, don't change the name of
the functions!

Skin Header
-----------
The file header is composed of the following fields, just like any Wordpress theme or plugin - inside a multi line comment on 
the first 8kb of the file.

* Skin Name
* Description
* Version
* Author
* Author URI

Functions
---------
The following functions should be on your skin file:

	function tsc_skin_get_headers()
	function tsc_skin_render($group, $questions)

The first function is responsible for adding to the headers any needed javascript or css. It must return an array with the keys
`js` and `css`. The code below include jquery, jquery-ui and a custom javascript file. It also include the needed css file.

	function tsc_skin_get_headers()
	{
    	$headers = array(
        	"js" => array(
            	"jquery",
            	"jquery-ui",
            	array("name" => "tecnosenior-faq", "file" => plugin_dir_url(__FILE__) . "/tecnosenior-faq.js")
        		),
        	"css" => array("tecnosenior-faq" => plugin_dir_url(__FILE__) . "/tecnosenior-faq.css")
    		);
	
    	return $headers;
	}	

The second function receive two objects containing the data that should be rendered. It must return a string. 

### Group
A single group object with this fields:
- Id
- GroupName
- SearchBox
- AskBox
- Status
- CreationDate

### Questions
An array of question objects with this fields available:
- Id
- GroupId
- QuestionOrder
- Question
- WhoAsked
- Answer
- Status
- Type
- CreationDate

Sample code, for skin renderer: 

	function tsc_skin_render($group, $questions)
	{
    	$html = "";
    	if ($group->SearchBox) {
        	$html .= render_search_box($group->Id);
    	}
	
    	$i = 0;
    	$html .= "<div class=\"span-14 prepend-1 append-1 last faq-questions\" id=\"faq-questions-list\">\n";
    	foreach ($questions as $q) {
        	$html .= render_question($q, ($i % 2 == 1));
        	$i++;
    	}
    	$html .= "</div>";
	
    	if ($group->AskBox) {
        	$html .= render_ask_box($group->Id);
    	}
	
    	return $html;
	}

Translation
===========
Currently only available in English and Brazilian Portuguese. The pot file for translation is under the directory languages.