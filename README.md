# CIL | ACE - Archivum Corporis Electronicum

## About the CIL Project

**The goal of the “Corpus Inscriptionum Latinarum” (CIL) is systematically to collect and publish all ancient Latin inscriptions of the Roman world.**  

Since its inception under Theodor Mommsen in 1853, the ["Corpus Inscriptionum Latinarum" (CIL)](https://cil.bbaw.de/) has been the definitive documentary project of the epigraphic patrimony of Roman antiquity. A systematic and text-critical collection of all known inscriptions of the Roman Imperium, the CIL, with its geographical as well as thematic arrangement, is an indispensable tool in the field of classical studies. The Corpus is continually expanded and updated in international collaboration with various scholars and research institutions to reflect and include the latest developments in classical research.  
 
The online database ["Archivum Corporis Electronicum" (ACE)](https://cil.bbaw.de/ace) contains documentation of the epigraphic work of the CIL, carefully collected since the beginning of the project: photos, squeezes (or ‘rubbings’ / ectypa), drawings and sketches, as well as index cards relating to individual inscriptions. The Archive is continually amplified with materials from all phases of the project. Not only would the corresponding materials of the Archive represent the sole remaining descriptive source should any particular inscription become lost, but they frequently also contain more information than was printed in the CIL volumes of the 19th and early 20th centuries, particularly regarding the nature of the objects which carry the inscriptions.  
 
The CIL has globally at its disposal the largest existent collection of photos and squeezes of Latin inscriptions, which can be researched according to various criteria when used alongside superordinate epigraphic databanks.  

This long-term Academy Project is part of the [Research Centre for Primary Sources of the Ancient World](https://www.bbaw.de/forschung/zentren/zentrum-alte-welt) at the [Berlin-Brandenburg Academy of Sciences and Humanities](https://www.bbaw.de/).  
 
The Academy research project “Corpus Inscriptionum Latinarum” is part of the [Academies' Programme](https://www.akademienunion.de/en/research/the-academies-programme), a research funding programme co-financed by the German federal government and individual federal states. Coordinated by the [Union of the German Academies of Sciences and Humanities](https://www.akademienunion.de/en/union/about-us), the Programme intends to retrieve and explore our cultural heritage, to make it accessible and highlight its relevance to the present, as well as to preserve it for the future.

## About the ACE App

The app provides a minimalist access to the inscriptions published by the CIL and their digital resources. It is designed to be as basic and modular as possible, consisting of a MySQL PHP Backend/JSON-API and a Vue.js Single Page Application as Frontend.  

Since the network infrastructure of the BBAW requires a special server configuration, it is not easily possible to use the app in another environment, especially as the database is currently not accessible externally.  
However, feel free to reuse the code for your personal projects as you wish.

## Dependencies

* [Laravel ^7.29](https://laravel.com/)
* [PHP 7.2.5](https://www.php.net/)
* [MySQL 5.6](https://www.mysql.com/)
* [Vue.js 5.6](https://vuejs.org/)
* [vuetify 2.3.7](https://vuetifyjs.com/en/)
* [Vue Router 3.0.1](https://router.vuejs.org/)
* [Vuex 3.0.1](https://vuex.vuejs.org/)

## Realization and Licensing

2021 [Berlin-Brandenburg Academy of Sciences and Humanities](https://www.bbaw.de/), [TELOTA - IT/DH](https://www.bbaw.de/en/bbaw-digital/telota), [Jan Köster](https://orcid.org/0000-0003-2713-5207)   

[APACHE LICENSE, VERSION 2.0](https://www.apache.org/licenses/LICENSE-2.0) 