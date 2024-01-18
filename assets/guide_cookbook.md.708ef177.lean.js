import{_ as o,c as p,b as c,w as a,a as e,r as u,o as l,d as n,e as s}from"./app.cfc21d1f.js";const O='{"title":"Cookbook","description":"","frontmatter":{},"headers":[{"level":2,"title":"x-tagGroups","slug":"x-taggroups"},{"level":2,"title":"Adding examples to @OA\\\\Response","slug":"adding-examples-to-oa-response"},{"level":2,"title":"External documentation","slug":"external-documentation"},{"level":2,"title":"Properties with union types","slug":"properties-with-union-types"},{"level":2,"title":"Referencing a security scheme","slug":"referencing-a-security-scheme"},{"level":2,"title":"File upload with headers","slug":"file-upload-with-headers"},{"level":2,"title":"Set the XML root name","slug":"set-the-xml-root-name"},{"level":2,"title":"upload multipart/form-data","slug":"upload-multipart-form-data"},{"level":2,"title":"Default security scheme for all endpoints","slug":"default-security-scheme-for-all-endpoints"},{"level":2,"title":"Nested objects","slug":"nested-objects"},{"level":2,"title":"Documenting union type response data using oneOf","slug":"documenting-union-type-response-data-using-oneof"},{"level":2,"title":"Reusing responses","slug":"reusing-responses"},{"level":2,"title":"mediaType=\\"/\\"","slug":"mediatype"},{"level":2,"title":"Warning about Multiple response with same response=\\"200\\"","slug":"warning-about-multiple-response-with-same-response-200"},{"level":2,"title":"Callbacks","slug":"callbacks"},{"level":2,"title":"(Mostly) virtual models","slug":"mostly-virtual-models"},{"level":2,"title":"Using class name as type instead of references","slug":"using-class-name-as-type-instead-of-references"},{"level":2,"title":"Enums","slug":"enums"},{"level":2,"title":"Multi value query parameter: &q[]=1&q[]=1","slug":"multi-value-query-parameter-q-1-q-1"},{"level":2,"title":"Custom response classes","slug":"custom-response-classes"},{"level":2,"title":"Annotating class constants","slug":"annotating-class-constants"}],"relativePath":"guide/cookbook.md"}',i={},r=e("",32),d=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

`),n("span",{class:"token keyword"},"use"),s(),n("span",{class:"token package"},[s("OpenApi"),n("span",{class:"token punctuation"},"\\"),s("Annotations")]),s(),n("span",{class:"token keyword"},"as"),s(),n("span",{class:"token constant"},"OA"),n("span",{class:"token punctuation"},";"),s(`

`),n("span",{class:"token comment"},`/**
 * @OA\\OpenApi(
 *   security={{"bearerAuth": {}}}
 * )
 *
 * @OA\\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer"
 * )
 */`),s(`
`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApi"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`
`)])])])],-1),k=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

`),n("span",{class:"token keyword"},"use"),s(),n("span",{class:"token package"},[s("OpenApi"),n("span",{class:"token punctuation"},"\\"),s("Attributes")]),s(),n("span",{class:"token keyword"},"as"),s(),n("span",{class:"token constant"},"OAT"),n("span",{class:"token punctuation"},";"),s(`

`),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OAT"),n("span",{class:"token punctuation"},"\\"),s("OpenApi")]),n("span",{class:"token punctuation"},"("),s(`
    `),n("span",{class:"token attribute-class-name class-name"},"security"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token punctuation"},"["),n("span",{class:"token punctuation"},"["),n("span",{class:"token string single-quoted-string"},"'bearerAuth'"),s(),n("span",{class:"token operator"},"=>"),s(),n("span",{class:"token punctuation"},"["),n("span",{class:"token punctuation"},"]"),n("span",{class:"token punctuation"},"]"),n("span",{class:"token punctuation"},"]"),s(`
`),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
`),n("span",{class:"token attribute"},[n("span",{class:"token delimiter punctuation"},"#["),n("span",{class:"token attribute-content"},[n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OAT"),n("span",{class:"token punctuation"},"\\"),s("Components")]),n("span",{class:"token punctuation"},"("),s(`
    `),n("span",{class:"token attribute-class-name class-name"},"securitySchemes"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token punctuation"},"["),s(`
        `),n("span",{class:"token attribute-class-name class-name"},"new"),s(),n("span",{class:"token attribute-class-name class-name class-name-fully-qualified"},[s("OAT"),n("span",{class:"token punctuation"},"\\"),s("SecurityScheme")]),n("span",{class:"token punctuation"},"("),s(`
            `),n("span",{class:"token attribute-class-name class-name"},"securityScheme"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'bearerAuth'"),n("span",{class:"token punctuation"},","),s(`
            `),n("span",{class:"token attribute-class-name class-name"},"type"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'http'"),n("span",{class:"token punctuation"},","),s(`
            `),n("span",{class:"token attribute-class-name class-name"},"scheme"),n("span",{class:"token punctuation"},":"),s(),n("span",{class:"token string single-quoted-string"},"'bearer'"),s(`
        `),n("span",{class:"token punctuation"},")"),s(`
    `),n("span",{class:"token punctuation"},"]"),s(`
`),n("span",{class:"token punctuation"},")")]),n("span",{class:"token delimiter punctuation"},"]")]),s(`
`),n("span",{class:"token keyword"},"class"),s(),n("span",{class:"token class-name-definition class-name"},"OpenApiSpec"),s(`
`),n("span",{class:"token punctuation"},"{"),s(`
`),n("span",{class:"token punctuation"},"}"),s(`
`)])])])],-1),m=e("",65);function h(y,q,g,f,b,A){const t=u("codeblock");return l(),p("div",null,[r,c(t,{id:"minimal"},{an:a(()=>[d]),at:a(()=>[k]),_:1}),m])}var w=o(i,[["render",h]]);export{O as __pageData,w as default};
