function getGuideSidebar() {
  return [
    {
      text: 'Introduction',
      items: [
        { text: 'What is Swagger-PHP?', link: '/guide/' },
        { text: 'Installation', link: '/guide/installation' },
        { text: 'Generating OpenAPI documents', link: '/guide/generating-openapi-documents' },
      ]
    },
    {
      text: 'Annotating your code',
      items: [
        { text: 'Using Attributes', link: '/guide/using-attributes' },
        { text: 'Using Annotations', link: '/guide/using-annotations' },
        { text: 'Minimum requirements', link: '/guide/minimum-requirements' },
        { text: 'Augmentation', link: '/guide/augmentation' },
        { text: 'References', link: '/guide/references' },
        { text: 'Enums', link: '/guide/enums' },
        { text: 'Shortcuts', link: '/guide/shortcuts' },
        { text: 'Vendor extensions', link: '/guide/vendor-extensions' },
      ]
    },
    {
      text: 'Upgrading',
      items: [
        { text: 'Migration from 5.x to 6.x', link: '/guide/migrating-to-v6' },
        { text: 'Migration from 4.x to 5.x', link: '/guide/migrating-to-v5' },
        { text: 'Migration from 3.x to 4.x', link: '/guide/migrating-to-v4' },
        { text: 'Migration from 2.x to 3.x', link: '/guide/migrating-to-v3' },
      ]
    },
    {
      text: 'Other',
      items: [
        { text: 'Cookbook', link: '/guide/cookbook' },
        { text: 'Examples', link: '/guide/examples' },
        { text: 'FAQ', link: '/guide/faq' },
        { text: 'Under the hood', link: '/guide/under-the-hood' },
        { text: 'Related Projects', link: '/related-projects' },
      ]
    },
  ]
}

function getReferenceSidebar() {
  return [
    {
      text: 'Reference',
      items: [
        { text: 'Attributes', link: '/reference/attributes' },
        { text: 'Annotations', link: '/reference/annotations' },
      ]
    },
    {
      text: 'Api',
      items: [
        { text: 'Generator', link: '/reference/generator' },
        { text: 'Processors', link: '/reference/processors' },
      ]
    },
  ]
}

module.exports = {
  title: "Swagger-PHP",
  base: "/swagger-php/",
  description: "Generate OpenAPI documentation for your RESTful API.",
  srcExclude: [
      'examples/Readme.md'
  ],
  themeConfig: {
    socialLinks: [
      { icon: 'github', link: 'https://github.com/zircote/swagger-php' },
    ],
    docsDir: 'docs',
    docsBranch: 'master',
    editLinks: false,
    editLinkText: 'Edit this page on GitHub',

    nav: [
      { text: "User Guide", link: "/guide/" },
      { text: "Reference", link: "/reference/" },
      { text: "OpenApi", link: "https://learn.openapis.org/" },
      { text: "Releases", link: "https://github.com/zircote/swagger-php/releases" },
    ],

    sidebar: {
      '/guide/': getGuideSidebar(),
      '/reference/': getReferenceSidebar()
    },

    outline: {
      level: [2, 3, 4]
    }
  }
};
