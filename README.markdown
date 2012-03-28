# PoemFormatter #

Contributors: wwerther
Donate link: http://wwerther.de/static/poemformatter
Tags: poem, format, lyrics, poetry
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 0.1.0

A plugin that allows to format poems in a proper and easy way. It provides some small control tags to allow adjustment to the layout.

## Description ##

This plugin allows to format poems in a nice way. The content of the poem is capsuled by the \[poem\]-tag.

It loads a template from the template-directory if specified, else the default template is choosen.

### Poem Syntax ###
The plugin supports a simple formatting syntax. All formatting information has to be put at the line-start

* #c# \-\> Center all lines starting with this line
* #l# \-\> Align left all lines starting with this line
* #r# \-\> Align right all lines starting with this line
* + \-\> Indent the line by count(+) times \<indent\> pixels
* #1#,#2#,#3# \-\> the following text will be put to the text-box number 1, 2, 3.... Text-Box 1 is default text-box

### Template syntax ###

has to be explained and written. Job is on my ToDo-list.

## Installation ##

1. Download the Plugin ZIP file
1. Unpack the Plugin-ZIP
1. Upload the poem\_formatter folder to your wordpress plugin directory
1. Activate the plugin through the 'Plugins' menu in WordPress

After activating the plugin you can use Poem Formatter with these shortcode:

* \[poem title="\<Name of your poem\>"\]Your poem here\[/poem\]

Optional parameters are
poem title="title" (debug) (align="center|left|right") (indent="<pixel>")

* title="title" \-\> specify the title of your poem
* author \-\> Specify the author of the poem.
* date \-\> Specify the creation date of the poem (if you want to)
* align="center|left|right" \-\> default is center, specify the default align of the poem lines (can be changed during the poem)
* indent = "pixel" default is 50, specifies the indent per line in pixel per + sign at the start of the line
* template \-\> The name of the template that shall be used for the poem. If not specified "default" is loaded
* debug \-\> If this is present the PHP-module put some more information into the HTML-file
* ipsum \-\> If this is present the content will be replaced by "Lorem Ipsum"
* titlecolor \-\> If the template supports changing the title-color this can be done with this switch
* textcolor \-\> If the template supports changing the text-color this can be done with this switch

## Frequently Asked Questions ##

### Where do I get the development-version? ###

I use git for my own development. You can find the Trunk-version on [GitHub](https://github.com/wwerther/Wordpress-PoemFormatter). It includes also the scripts to update the subversion-directory on wordpress.

### Are there known bugs? ###

Yes, I'm sure, that there are some known bugs. I had no time to fix or trace them yet.

* to be done...

### Is there a Roadmap? ###

Yes, there is kind of a roadmap. But the order depends on my time.

* improve the documentation
* create some more templates

## Screenshots ##

1. ![Graph1][screenshot1] one column layout
2. ![Graph2][screenshot2] advanced formatting
3. ![Graph3][screenshot3] two column layout
4. ![Graph4][screenshot4] default-layout with advanced format

## Changelog ##

### 0.1.0 ###
* only minor adjustments. Preparation for further changes

### 0.0.6 ###

* support for different templates and text-blocks
* proper replacement of meta-attributes like author, creation date, color replacement
* poem template added

### 0.0.1 ###

* Initial pre-beta version, does not really allow public usage

## Upgrade Notice ##

### 0.1.0 ###
* Smaller bugfixes, updated some URLs

### 0.0.6 ###
* We almost reach the level where it is worth to use this plugin

### 0.0.1 ###

* Initial version

[screenshot1]: https://github.com/wwerther/Wordpress-PoemFormatter/raw/master/screenshots/screenshot-1.png "Graph1"
[screenshot2]: https://github.com/wwerther/Wordpress-PoemFormatter/raw/master/screenshots/screenshot-2.png "Graph2"
[screenshot3]: https://github.com/wwerther/Wordpress-PoemFormatter/raw/master/screenshots/screenshot-3.png "Graph3"
[screenshot4]: https://github.com/wwerther/Wordpress-PoemFormatter/raw/master/screenshots/screenshot-4.png "Graph4"
