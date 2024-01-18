import{_ as o,c,b as p,w as a,a as t,r as i,o as l,d as n,e as s}from"./app.cfc21d1f.js";const O='{"title":"Required elements","description":"","frontmatter":{},"headers":[{"level":2,"title":"Minimum required annotations","slug":"minimum-required-annotations"},{"level":2,"title":"Optional elements","slug":"optional-elements"}],"relativePath":"guide/required-elements.md"}',u={},r=t("",6),k=n("div",{class:"language-php"},[n("pre",null,[n("code",null,[n("span",{class:"token php language-php"},[n("span",{class:"token delimiter important"},"<?php"),s(`

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
`)])])])],-1),m=t("",6);function h(_,f,g,y,b,A){const e=i("codeblock");return l(),c("div",null,[r,p(e,{id:"minimal"},{an:a(()=>[k]),at:a(()=>[d]),_:1}),m])}var q=o(u,[["render",h]]);export{O as __pageData,q as default};
