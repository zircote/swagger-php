function getGuideSidebar() {
  return [
    {
      text: 'Introduction',
      children: [
        { text: 'What is Swagger-PHP?', link: '/guide/' },
        { text: 'Installation', link: '/guide/installation' },
        { text: 'Generating OpenAPI documents', link: '/guide/generating-openapi-documents' },
      ]
    },
    {
      text: 'Annotating your code',
      children: [
        { text: 'Attributes', link: '/guide/attributes' },
        { text: 'Annotations', link: '/guide/annotations' },
        { text: 'Required elements', link: '/guide/required-elements' },
        { text: 'Common techniques', link: '/guide/common-techniques' },
      ]
    },
    {
      text: 'Upgrading',
      children: [
        { text: 'Migration from 3.x to 4.x', link: '/guide/migrating-to-v4' },
        { text: 'Migration from 2.x to 3.x', link: '/guide/migrating-to-v3' },
      ]
    },
    {
      text: 'Other',
      children: [
        { text: 'Cookbook', link: '/guide/cookbook' },
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
      children: [
        { text: 'Attributes', link: '/reference/attributes' },
        { text: 'Annotations', link: '/reference/annotations' },
      ]
    },
    {
      text: 'Api',
      children: [
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
  themeConfig: {
    repo: 'zircote/swagger-php',
    docsDir: 'docs',
    docsBranch: 'master',
    editLinks: false,
    editLinkText: 'Edit this page on GitHub',

    nav: [
      { text: "User Guide", link: "/guide/" },
      { text: "Reference", link: "/reference/" },
      { text: "OpenApi", link: "https://oai.github.io/Documentation/" },
      { text: "Releases", link: "https://github.com/zircote/swagger-php/releases" },
    ],

    sidebar: {
      '/guide/': getGuideSidebar(),
      '/reference/': getReferenceSidebar()
    }
  }
};
