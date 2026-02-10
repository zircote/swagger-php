import{_ as h,C as p,o as k,c as d,aj as n,E as t,w as e,j as s,a as i}from"./chunks/framework.CCC87fQL.js";const m=JSON.parse('{"title":"Augmentation","description":"","frontmatter":{},"headers":[],"relativePath":"guide/augmentation.md","filePath":"guide/augmentation.md"}'),r={name:"guide/augmentation.md"};function o(c,a,g,y,E,A){const l=p("codeblock");return k(),d("div",null,[a[4]||(a[4]=n("",7)),t(l,{id:"context-awareness"},{at:e(()=>[...a[0]||(a[0]=[s("div",{class:"language-php vp-adaptive-theme"},[s("button",{title:"Copy Code",class:"copy"}),s("span",{class:"lang"},"php"),s("pre",{class:"shiki shiki-themes github-light github-dark vp-code",tabindex:"0"},[s("code",null,[s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"<?"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"php")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"namespace"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Openapi\\Snippets\\Augmentation\\Aware"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"use"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}}," OpenApi\\Attributes"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," as"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," OA"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"#["),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"OA\\Schema"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"]")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"class"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Product")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"{")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"    /**")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * The product name,.")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * "),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"@var"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," string")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     */")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"    #["),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"OA\\Property"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"]")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"    public"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}}," $name;")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"}")])])])],-1)])]),an:e(()=>[...a[1]||(a[1]=[s("div",{class:"language-php vp-adaptive-theme"},[s("button",{title:"Copy Code",class:"copy"}),s("span",{class:"lang"},"php"),s("pre",{class:"shiki shiki-themes github-light github-dark vp-code",tabindex:"0"},[s("code",null,[s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"<?"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"php")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"namespace"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Openapi\\Snippets\\Augmentation\\Aware"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"use"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}}," OpenApi\\Annotations"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," as"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," OA"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"/**")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}}," * @OA\\Schema")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}}," */")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"class"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Product")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"{")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"    /**")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * The product name,.")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * "),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"@var"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," string")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * @OA\\Property")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     */")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"    public"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}}," $name;")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"}")])])])],-1)])]),_:1}),a[5]||(a[5]=n("",3)),t(l,{id:"explicit-context"},{at:e(()=>[...a[2]||(a[2]=[s("div",{class:"language-php vp-adaptive-theme"},[s("button",{title:"Copy Code",class:"copy"}),s("span",{class:"lang"},"php"),s("pre",{class:"shiki shiki-themes github-light github-dark vp-code",tabindex:"0"},[s("code",null,[s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"<?"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"php")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"namespace"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Openapi\\Snippets\\Augmentation\\Explicit"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"use"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}}," OpenApi\\Attributes"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," as"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," OA"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"#["),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"OA\\Schema"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"]")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"class"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Product")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"{")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"    /**")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * The product name.")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     */")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"    #["),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"OA\\Property"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"(")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}},"        property"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},": "),s("span",{style:{"--shiki-light":"#032F62","--shiki-dark":"#9ECBFF"}},"'name'"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},",")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}},"        type"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},": "),s("span",{style:{"--shiki-light":"#032F62","--shiki-dark":"#9ECBFF"}},"'string'"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},",")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}},"        description"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},": "),s("span",{style:{"--shiki-light":"#032F62","--shiki-dark":"#9ECBFF"}},"'The product name'")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"    )]")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"    public"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," string"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}}," $name;")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"}")])])])],-1)])]),an:e(()=>[...a[3]||(a[3]=[s("div",{class:"language-php vp-adaptive-theme"},[s("button",{title:"Copy Code",class:"copy"}),s("span",{class:"lang"},"php"),s("pre",{class:"shiki shiki-themes github-light github-dark vp-code",tabindex:"0"},[s("code",null,[s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"<?"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}},"php")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"namespace"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Openapi\\Snippets\\Augmentation\\Explicit"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"use"),s("span",{style:{"--shiki-light":"#005CC5","--shiki-dark":"#79B8FF"}}," OpenApi\\Annotations"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," as"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," OA"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},";")]),i(`
`),s("span",{class:"line"}),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"/**")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}}," * @OA\\Schema")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}}," */")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"class"),s("span",{style:{"--shiki-light":"#6F42C1","--shiki-dark":"#B392F0"}}," Product")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"{")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"    /**")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * The product name.")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * "),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"@var"),s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}}," string")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     *")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * @OA\\Property(")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},'     *     property="name",')]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},'     *     type="string",')]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},'     *     description="The product name"')]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     * )")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#6A737D","--shiki-dark":"#6A737D"}},"     */")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#D73A49","--shiki-dark":"#F97583"}},"    public"),s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}}," $name;")]),i(`
`),s("span",{class:"line"},[s("span",{style:{"--shiki-light":"#24292E","--shiki-dark":"#E1E4E8"}},"}")])])])],-1)])]),_:1}),a[6]||(a[6]=n("",11))])}const F=h(r,[["render",o]]);export{m as __pageData,F as default};
