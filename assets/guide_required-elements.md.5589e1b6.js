import{_ as o,c,b as p,w as a,a as t,r as i,o as l,d as n,e as s}from"./app.cfc21d1f.js";const O='{"title":"Required elements","description":"","frontmatter":{},"headers":[{"level":2,"title":"Minimum required annotations","slug":"minimum-required-annotations"},{"level":2,"title":"Optional elements","slug":"optional-elements"}],"relativePath":"guide/required-elements.md"}',u={},r=t('<h1 id="required-elements" tabindex="-1">Required elements <a class="header-anchor" href="#required-elements" aria-hidden="true">#</a></h1><p>The OpenAPI specification defines a minimum set of information for a valid document.</p><p>For the most part that consists of some general information about the API like <code>name</code>, <code>version</code> and at least one endpoint.</p><p>The endpoint, in turn, needs to have a path and at least one response.</p><h2 id="minimum-required-annotations" tabindex="-1">Minimum required annotations <a class="header-anchor" href="#minimum-required-annotations" aria-hidden="true">#</a></h2><p>With the above in mind a minimal API with a single endpoint could look like this</p>',6),k=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

`),n("span",{class:"token keyword"},"use"),s(),n("span",{class:"token package"},[s("OpenApi"),n("span",{class:"token punctuation"},"\\"),s("Annotations")]),s(),n("span",{class:"token keyword"},"as"),s(),n("span",{class:"token constant"},"OA"),n("span",{class:"token punctuation"},";"),s(`

`),n("span",{class:"token comment"},`/**
 * @OA\\Info(
 *     title="My First API",
 *     version="0.1"
 * )
 */`),s(`
`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApi"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`

`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"MyController"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`

    `),n("span",{class:"token comment"},`/**
     * @OA\\Get(
     *     path="/api/data.json",
     *     @OA\\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     */`),s(`
    `),n("span",{class:"token keyword"},"public"),s(),n("span",{class:"token keyword"},"function"),s(),n("span",{class:"token function-definition function"},"getResource"),n("span",{class:"token punctuation"},"("),n("span",{class:"token punctuation"},")"),s(`
    `),n("span",{class:"token punctuation"},"{"),s(`
        `),n("span",{class:"token comment"},"// ..."),s(`
    `),n("span",{class:"token punctuation"},"}"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`
`)])])])],-1),d=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

`),n("span",{class:"token keyword"},"use"),s(),n("span",{class:"token package"},[s("OpenApi"),n("span",{class:"token punctuation"},"\\"),s("Attributes")]),s(),n("span",{class:"token keyword"},"as"),s(),n("span",{class:"token constant"},"OA"),n("span",{class:"token punctuation"},";"),s(`

`),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OA"),n("span",{class:"token punctuation"},"\\"),s("Info")]),n("span",{class:"token punctuation"},"("),n("span",{class:"token attribute-class-name class-name"},"title"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string double-quoted-string"},'"My First API"'),n("span",{class:"token punctuation"},","),s(),n("span",{class:"token attribute-class-name class-name"},"version"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string double-quoted-string"},'"0.1"'),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApi"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`

`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"MyController"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`

    `),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OA"),n("span",{class:"token punctuation"},"\\"),s("Get")]),n("span",{class:"token punctuation"},"("),n("span",{class:"token attribute-class-name class-name"},"path"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'/api/data.json'"),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
    `),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OA"),n("span",{class:"token punctuation"},"\\"),s("Response")]),n("span",{class:"token punctuation"},"("),n("span",{class:"token attribute-class-name class-name"},"response"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'200'"),n("span",{class:"token punctuation"},","),s(),n("span",{class:"token attribute-class-name class-name"},"description"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'The data'"),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
    `),n("span",{class:"token keyword"},"public"),s(),n("span",{class:"token keyword"},"function"),s(),n("span",{class:"token function-definition function"},"getResource"),n("span",{class:"token punctuation"},"("),n("span",{class:"token punctuation"},")"),s(`
    `),n("span",{class:"token punctuation"},"{"),s(`
        `),n("span",{class:"token comment"},"// ..."),s(`
    `),n("span",{class:"token punctuation"},"}"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`

`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApiSpec"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`
`)])])])],-1),m=t(`<p>with the resulting OpenAPI document like this</p><div class="language-yaml"><pre><code><span class="token key atrule">openapi</span><span class="token punctuation">:</span> 3.0.0
<span class="token key atrule">info</span><span class="token punctuation">:</span>
  <span class="token key atrule">title</span><span class="token punctuation">:</span> <span class="token string">&#39;My First API&#39;</span>
  <span class="token key atrule">version</span><span class="token punctuation">:</span> <span class="token string">&#39;0.1&#39;</span>
<span class="token key atrule">paths</span><span class="token punctuation">:</span>
  <span class="token key atrule">/api/data.json</span><span class="token punctuation">:</span>
    <span class="token key atrule">get</span><span class="token punctuation">:</span>
      <span class="token key atrule">operationId</span><span class="token punctuation">:</span> 236f26ae21b015a60adbce41f8f316e3
      <span class="token key atrule">responses</span><span class="token punctuation">:</span>
        <span class="token key atrule">&#39;200&#39;</span><span class="token punctuation">:</span>
          <span class="token key atrule">description</span><span class="token punctuation">:</span> <span class="token string">&#39;The data&#39;</span>
</code></pre></div><div class="warning custom-block"><p class="custom-block-title">Code locations</p><p>Attributes and annotations can be added anywhere on declarations in code as defined by the PHP docs. These are limited to the extent of what the PHP Reflection APIs supports.</p></div><h2 id="optional-elements" tabindex="-1">Optional elements <a class="header-anchor" href="#optional-elements" aria-hidden="true">#</a></h2><p>Looking at the generated document you will notice that there are some elements that <code>swagger-php</code> adds automatically when they are missing.</p><p>For the most part those are <code>@OA\\OpenApi</code>, <code>@OA\\Components</code> and <code>@OA\\PathItem</code>.</p>`,6);function h(_,f,g,y,b,A){const e=i("codeblock");return l(),c("div",null,[r,p(e,{id:"minimal"},{an:a(()=>[k]),at:a(()=>[d]),_:1}),m])}var q=o(u,[["render",h]]);export{O as __pageData,q as default};
