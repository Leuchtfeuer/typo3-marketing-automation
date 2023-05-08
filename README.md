[![Latest Stable Version](https://poser.pugx.org/leuchtfeuer/marketing-automation/v/stable)](https://packagist.org/packages/leuchtfeuer/marketing-automation)
[![Total Downloads](https://poser.pugx.org/leuchtfeuer/marketing-automation/downloads)](https://packagist.org/packages/leuchtfeuer/marketing-automation)
[![Latest Unstable Version](https://poser.pugx.org/leuchtfeuer/marketing-automation/v/unstable)](https://packagist.org/packages/leuchtfeuer/marketing-automation)
[![License](https://poser.pugx.org/leuchtfeuer/marketing-automation/license)](https://packagist.org/packages/leuchtfeuer/marketing-automation)

# TYPO3 Extension "Marketing Automation"

Base TYPO3 extension that allows targeting and personalization of TYPO3 content: Limit pages, content-elements etc. to certain "Marketing Personas". Determination of Personas can come from various sources (requires add-on extensions).

## Installation
Simply require the extension by running:
```
composer require leuchtfeuer/marketing-automation
```

## Usage
1. Go to your root-page and create a new record of type "Persona". Give it a title and configure the persona (e.g. if you use [EXT:mautic](https://github.com/mautic/mautic-typo3/), select some segments in the Mautic tab).
2. Now, edit a page or a content-element. Under the Access tab you'll find the new setting "Limit to targeting personas". Select the Persona created in the first step. The page or content-element will be shown only if the current user belongs to a segment of the Persona (similar to FE Access Rights).

## Contributing
You can contribute by making a **pull request** to the master branch of this repository, by using the "❤️ Sponsor" button on the
top of this page, or just send us some **beers** 🍻...
