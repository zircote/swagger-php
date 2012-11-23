# -*- coding: utf-8 -*-

import sys, os

extensions = []

templates_path = ['_templates']

master_doc = 'index'

project = u'Swagger-PHP'
copyright = u'2012, Robert Allen'

version = '0.4.0'

release = '0.4.0-RC1'

exclude_patterns = ['_build']

pygments_style = 'sphinx'

html_theme = 'pyramid'

html_static_path = ['_static']

htmlhelp_basename = 'Swagger-PHPdoc'
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)
pygments_style = 'sphinx'
primary_domain = "php"

latex_elements = {
}

latex_documents = [
  ('index', 'Swagger-PHP.tex', u'Swagger-PHP Documentation',
   u'Robert Allen', 'manual'),
]

man_pages = [
    ('index', 'swagger-php', u'Swagger-PHP Documentation',
     [u'Robert Allen'], 1)
]

texinfo_documents = [
  ('index', 'Swagger-PHP', u'Swagger-PHP Documentation',
   u'Robert Allen', 'Swagger-PHP', 'One line description of project.',
   'Miscellaneous'),
]
