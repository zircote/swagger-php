import{_ as o,c,b as i,w as a,a as s,r as p,o as l,d as n,e}from"./app.cfc21d1f.js";const x='{"title":"Home","description":"","frontmatter":{"home":true,"actionText":"User Guide \u2192","actionLink":"/guide/","features":[{"title":"OpenAPI conformant","details":"Generate OpenAPI documents in version 3.0 or 3.1."},{"title":"Document your API inside PHP source code","details":"Using swagger-php lets you write the API documentation inside the PHP source files which helps keeping the documentation up-to-date."},{"title":"Annotation and Attribute support","details":"Annotations can be either docblocks or PHP 8.1 attributes."}]},"headers":[{"level":3,"title":"1. Install with composer:","slug":"_1-install-with-composer"},{"level":3,"title":"2. Update your code","slug":"_2-update-your-code"},{"level":3,"title":"3. Generate OpenAPI documentation","slug":"_3-generate-openapi-documentation"},{"level":3,"title":"4. Explore and interact with your API","slug":"_4-explore-and-interact-with-your-api"},{"level":2,"title":"Links","slug":"links"}],"relativePath":"index.md"}',r={},u=s(`<h3 id="_1-install-with-composer" tabindex="-1">1. Install with composer: <a class="header-anchor" href="#_1-install-with-composer" aria-hidden="true">#</a></h3><div class="language-shell"><pre><code><span class="token operator">&gt;</span> <span class="token function">composer</span> require zircote/swagger-php
</code></pre></div><h3 id="_2-update-your-code" tabindex="-1">2. Update your code <a class="header-anchor" href="#_2-update-your-code" aria-hidden="true">#</a></h3><p>Add <code>swagger-php</code> annotations or attributes to your source code.</p>`,4),d=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),e(`

`),n("span",{class:"token keyword"},"use"),e(),n("span",{class:"token package"},[e("OpenApi"),n("span",{class:"token punctuation"},"\\"),e("Annotations")]),e(),n("span",{class:"token keyword"},"as"),e(),n("span",{class:"token constant"},"OA"),n("span",{class:"token punctuation"},";"),e(`

`),n("span",{class:"token comment"},`/**
 * @OA\\Info(
 *     title="My First API",
 *     version="0.1"
 * )
 */`),e(`
`),n("span",{class:"token keyword"},"class"),e(),n("span",{class:"token class-name-definition class-name"},"OpenApi"),e(`
`),n("span",{class:"token punctuation"},"{"),e(`
`),n("span",{class:"token punctuation"},"}"),e(`

`),n("span",{class:"token keyword"},"class"),e(),n("span",{class:"token class-name-definition class-name"},"MyController"),e(`
`),n("span",{class:"token punctuation"},"{"),e(`

    `),n("span",{class:"token comment"},`/**
     * @OA\\Get(
     *     path="/api/data.json",
     *     @OA\\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     */`),e(`
    `),n("span",{class:"token keyword"},"public"),e(),n("span",{class:"token keyword"},"function"),e(),n("span",{class:"token function-definition function"},"getResource"),n("span",{class:"token punctuation"},"("),n("span",{class:"token punctuation"},")"),e(`
    `),n("span",{class:"token punctuation"},"{"),e(`
        `),n("span",{class:"token comment"},"// ..."),e(`
    `),n("span",{class:"token punctuation"},"}"),e(`
`),n("span",{class:"token punctuation"},"}"),e(`
`)])])])],-1),k=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),e(`

`),n("span",{class:"token keyword"},"use"),e(),n("span",{class:"token package"},[e("OpenApi"),n("span",{class:"token punctuation"},"\\"),e("Attributes")]),e(),n("span",{class:"token keyword"},"as"),e(),n("span",{class:"token constant"},"OA"),n("span",{class:"token punctuation"},";"),e(`

`),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[e("OA"),n("span",{class:"token punctuation"},"\\"),e("Info")]),n("span",{class:"token punctuation"},"("),n("span",{class:"token attribute-class-name class-name"},"title"),n("span",{class:"token punctuation"},":"),e(),n("span",{class:"token string double-quoted-string"},'"My First API"'),n("span",{class:"token punctuation"},","),e(),n("span",{class:"token attribute-class-name class-name"},"version"),n("span",{class:"token punctuation"},":"),e(),n("span",{class:"token string double-quoted-string"},'"0.1"'),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),e(`
`),n("span",{class:"token keyword"},"class"),e(),n("span",{class:"token class-name-definition class-name"},"OpenApi"),e(`
`),n("span",{class:"token punctuation"},"{"),e(`
`),n("span",{class:"token punctuation"},"}"),e(`

`),n("span",{class:"token keyword"},"class"),e(),n("span",{class:"token class-name-definition class-name"},"MyController"),e(`
`),n("span",{class:"token punctuation"},"{"),e(`

    `),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[e("OA"),n("span",{class:"token punctuation"},"\\"),e("Get")]),n("span",{class:"token punctuation"},"("),n("span",{class:"token attribute-class-name class-name"},"path"),n("span",{class:"token punctuation"},":"),e(),n("span",{class:"token string single-quoted-string"},"'/api/data.json'"),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),e(`
    `),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[e("OA"),n("span",{class:"token punctuation"},"\\"),e("Response")]),n("span",{class:"token punctuation"},"("),n("span",{class:"token attribute-class-name class-name"},"response"),n("span",{class:"token punctuation"},":"),e(),n("span",{class:"token string single-quoted-string"},"'200'"),n("span",{class:"token punctuation"},","),e(),n("span",{class:"token attribute-class-name class-name"},"description"),n("span",{class:"token punctuation"},":"),e(),n("span",{class:"token string single-quoted-string"},"'The data'"),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),e(`
    `),n("span",{class:"token keyword"},"public"),e(),n("span",{class:"token keyword"},"function"),e(),n("span",{class:"token function-definition function"},"getResource"),n("span",{class:"token punctuation"},"("),n("span",{class:"token punctuation"},")"),e(`
    `),n("span",{class:"token punctuation"},"{"),e(`
        `),n("span",{class:"token comment"},"// ..."),e(`
    `),n("span",{class:"token punctuation"},"}"),e(`
`),n("span",{class:"token punctuation"},"}"),e(`

`),n("span",{class:"token keyword"},"class"),e(),n("span",{class:"token class-name-definition class-name"},"OpenApiSpec"),e(`
`),n("span",{class:"token punctuation"},"{"),e(`
`),n("span",{class:"token punctuation"},"}"),e(`
`)])])])],-1),h=s(`<h3 id="_3-generate-openapi-documentation" tabindex="-1">3. Generate OpenAPI documentation <a class="header-anchor" href="#_3-generate-openapi-documentation" aria-hidden="true">#</a></h3><div class="language-shell"><pre><code><span class="token operator">&gt;</span> ./bin/openapi src <span class="token parameter variable">-o</span> openapi.yaml
</code></pre></div><h3 id="_4-explore-and-interact-with-your-api" tabindex="-1">4. Explore and interact with your API <a class="header-anchor" href="#_4-explore-and-interact-with-your-api" aria-hidden="true">#</a></h3><p>Use an OpenAPI tool like <a href="https://swagger.io/tools/swagger-ui/" target="_blank" rel="noopener noreferrer">Swagger UI </a> to explore and interact with your API.</p><h2 id="links" tabindex="-1">Links <a class="header-anchor" href="#links" aria-hidden="true">#</a></h2><ul><li><a href="./guide/">User Guide</a></li><li><a href="./reference/">Reference</a></li><li><a href="https://learn.openapis.org/" target="_blank" rel="noopener noreferrer">OpenApi Documentation</a></li><li><a href="https://spec.openapis.org/oas/v3.1.0.html" target="_blank" rel="noopener noreferrer">OpenApi Specification</a></li><li><a href="https://github.com/zircote/swagger-php/tree/master/Examples" target="_blank" rel="noopener noreferrer">Learn by example</a></li><li><a href="./related-projects.html">Related projects</a></li><li><a href="https://github.com/zircote/swagger-php/tree/2.x/docs" target="_blank" rel="noopener noreferrer">Swagger-php 2.x documentation</a></li></ul>`,6);function m(g,_,f,b,A,w){const t=p("codeblock");return l(),c("div",null,[u,i(t,{id:"minimal"},{an:a(()=>[d]),at:a(()=>[k]),_:1}),h])}var P=o(r,[["render",m]]);export{x as __pageData,P as default};
